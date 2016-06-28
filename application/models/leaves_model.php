<?php
/**
 * This Model contains all the business logic and the persistence layer for leave request objects.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class contains the business logic and manages the persistence of leave requests.
 */
class Leaves_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {
        
    }

    /**
     * Get the list of all leave requests or one leave
     * @param int $id Id of the leave request
     * @return array list of records
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getLeaves($id = 0) {
        $this->db->select('leaves.*');
        $this->db->select('status.name as status_name, types.name as type_name');
        $this->db->from('leaves');
        $this->db->join('status', 'leaves.status = status.id');
        $this->db->join('types', 'leaves.type = types.id');
        if ($id === 0) {
            return $this->db->get()->result_array();
        }
        $this->db->where('leaves.id', $id);
        return $this->db->get()->row_array();
    }

    /**
     * Get the the list of leaves requested for a given employee
     * Id are replaced by label
     * @param int $employee ID of the employee
     * @return array list of records
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getLeavesOfEmployee($employee) {
        $this->db->select('leaves.*');
        $this->db->select('status.name as status_name, types.name as type_name');
        $this->db->from('leaves');
        $this->db->join('status', 'leaves.status = status.id');
        $this->db->join('types', 'leaves.type = types.id');
        $this->db->where('leaves.employee', $employee);
        $this->db->order_by('leaves.id', 'desc');
        return $this->db->get()->result_array();
    }
    
    /**
     * Return a list of Accepted leaves between two dates and for a given employee
     * @param int $employee ID of the employee
     * @param string $start Start date
     * @param string $end End date
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getAcceptedLeavesBetweenDates($employee, $start, $end) {
        $this->db->select('leaves.*, types.name as type');
        $this->db->from('leaves');
        $this->db->join('status', 'leaves.status = status.id');
        $this->db->join('types', 'leaves.type = types.id');
        $this->db->where('employee', $employee);
        $this->db->where("(startdate <= STR_TO_DATE('" . $end . "', '%Y-%m-%d') AND enddate >= STR_TO_DATE('" . $start . "', '%Y-%m-%d'))");
        $this->db->where('leaves.status', 3);   //Accepted
        $this->db->order_by('startdate', 'asc');
        return $this->db->get()->result_array();
    }
    
    /**
     * Try to calculate the length of a leave using the start and and date of the leave
     * and the non working days defined on a contract
     * @param int $employee Identifier of the employee
     * @param date $start start date of the leave request
     * @param date $end end date of the leave request
     * @param string $startdatetype start date type of leave request being created (Morning or Afternoon)
     * @param string $enddatetype end date type of leave request being created (Morning or Afternoon)
     * @return float length of leave
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function length($employee, $start, $end, $startdatetype, $enddatetype) {
        $this->db->select('sum(CASE `type` WHEN 1 THEN 1 WHEN 2 THEN 0.5 WHEN 3 THEN 0.5 END) as days');
        $this->db->from('users');
        $this->db->join('dayoffs', 'users.contract = dayoffs.contract');
        $this->db->where('users.id', $employee);
        $this->db->where('date >=', $start);
        $this->db->where('date <=', $end);
        $result = $this->db->get()->result_array();
        $startTimeStamp = strtotime($start." UTC");
        $endTimeStamp = strtotime($end." UTC");
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff / 86400;  // 86400 seconds in one day
        if (count($result) != 0) { //Test if some non working days are defined on a contract
            return $numberDays - $result[0]['days'];
        } else {
            //Special case when the leave request is half a day long, 
            //we assume that the non-working day is not at the same time than the leave request
            if ($startdatetype == $enddatetype) {
                return 0.5;
            } else {
                return $numberDays;
            }
        }
    }
    
    /**
     * Calculate the actual length of a leave request by taking into account the non-working days
     * Detect overlapping with non-working days. It returns a K/V arrays of 3 items.
     * @param int $employee Identifier of the employee
     * @param date $startdate start date of the leave request
     * @param date $enddate end date of the leave request
     * @param string $startdatetype start date type of leave request being created (Morning or Afternoon)
     * @param string $enddatetype end date type of leave request being created (Morning or Afternoon)
     * @param array List of non-working days
     * @return array (length=>length of leave, overlapping=>excat match with a non-working day, daysoff=>sum of days off)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function actualLengthAndDaysOff($employee, $startdate, $enddate, $startdatetype, $enddatetype, $daysoff) {
        $startDateObject = DateTime::createFromFormat('Y-m-d H:i:s', $startdate . ' 00:00:00');
        $endDateObject = DateTime::createFromFormat('Y-m-d H:i:s', $enddate . ' 00:00:00');
        $iDate = clone $startDateObject;

        //Simplify logic
        if ($startdate == $enddate) $one_day = TRUE; else $one_day = FALSE;
        if ($startdatetype == 'Morning') $start_morning = TRUE; else $start_morning = FALSE;
        if ($startdatetype == 'Afternoon') $start_afternoon = TRUE; else $start_afternoon = FALSE;
        if ($enddatetype == 'Morning') $end_morning = TRUE; else $end_morning = FALSE;
        if ($enddatetype == 'Afternoon') $end_afternoon = TRUE; else $end_afternoon = FALSE;

        //Iteration between start and end dates of the leave request
        $lengthDaysOff = 0;
        $length = 0;
        $hasDayOff = FALSE;
        $overlapDayOff = FALSE;
        while ($iDate <= $endDateObject)
        {
            if ($iDate == $startDateObject) $first_day = TRUE; else $first_day = FALSE;
            $isDayOff = FALSE;
            //Iterate on the list of days off with two objectives:
            // - Compute sum of days off between the two dates
            // - Detect if the leave request exactly overlaps with a day off
            foreach ($daysoff as $dayOff) {
                $dayOffObject = DateTime::createFromFormat('Y-m-d H:i:s', $dayOff['date'] . ' 00:00:00');
                if ($dayOffObject == $iDate) {
                    $lengthDaysOff+=$dayOff['length'];
                    $isDayOff = TRUE;
                    $hasDayOff = TRUE;
                    switch ($dayOff['type']) {
                        case 1: //1 : All day
                            if ($one_day && $start_morning && $end_afternoon && $first_day)
                                $overlapDayOff = TRUE;
                            break;
                        case 2: //2 : Morning
                            if ($one_day && $start_morning && $end_morning && $first_day)
                                $overlapDayOff = TRUE;
                            else
                                $length+=0.5;
                            break;
                        case 3: //3 : Afternnon
                            if ($one_day && $start_afternoon && $end_afternoon && $first_day)
                                $overlapDayOff = TRUE;
                            else
                                $length+=0.5;
                            break;
                        default:
                            break;
                    }
                    break;
                }
            }
            if (!$isDayOff) {
                if ($one_day) {
                    if ($start_morning && $end_afternoon) $length++;
                    if ($start_morning && $end_morning) $length+=0.5;
                    if ($start_afternoon && $end_afternoon) $length+=0.5;
                } else {
                    if ($iDate == $endDateObject) $last_day = TRUE; else $last_day = FALSE;
                    if (!$first_day && !$last_day) $length++;
                    if ($first_day && $start_morning) $length++;
                    if ($first_day && $start_afternoon) $length+=0.5;
                    if ($last_day && $end_morning) $length+=0.5;
                    if ($last_day && $end_afternoon) $length++;
                }
                $overlapDayOff = FALSE;
            }
            $iDate->modify('+1 day');   //Next day
        }

        //Other obvious cases of overlapping
        if ($hasDayOff && ($length == 0)) {
            $overlapDayOff = TRUE;
        }
        return array('length' => $length, 'daysoff' => $lengthDaysOff, 'overlapping' => $overlapDayOff);
    }
    
    /**
     * Get all entitled days applicable to the reference date (to contract and employee)
     * Compute Min and max date by type
     * @param int $employee Employee identifier
     * @param int $contract contract identifier
     * @param string $refDate Date of execution
     * @return array Array of entitled days associated to the key type id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getSumEntitledDays($employee, $contract, $refDate) {
        $this->db->select('types.id as type_id, types.name as type_name');
        $this->db->select('SUM(entitleddays.days) as entitled');
        $this->db->select('MIN(startdate) as min_date');
        $this->db->select('MAX(enddate) as max_date');
        $this->db->from('entitleddays');
        $this->db->join('types', 'types.id = entitleddays.type');
        $this->db->group_by('types.id');
        $this->db->where('entitleddays.startdate <= ', $refDate);
        $this->db->where('entitleddays.enddate >= ', $refDate);
        $where = ' (entitleddays.contract=' . $contract . 
                       ' OR entitleddays.employee=' . $employee . ')';
        $this->db->where($where, NULL, FALSE);   //Not very safe, but can't do otherwise
        $results = $this->db->get()->result_array();
        //Create an associated array have the leave type as key
        $entitled_days = array();
        foreach ($results as $result) {
            $entitled_days[$result['type_id']] = $result;
        }
        return $entitled_days;
    }
    
    /**
     * Compute the leave balance of an employee (used by report and counters)
     * @param int $id ID of the employee
     * @param bool $sum_extra TRUE: sum compensate summary
     * @param string $refDate tmp of the Date of reference (or current date if NULL)
     * @return array computed aggregated taken/entitled leaves
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getLeaveBalanceForEmployee($id, $sum_extra = FALSE, $refDate = NULL) {
        //Determine if we use current date or another date
        if ($refDate == NULL) {
            $refDate = date("Y-m-d");
        }

        //Compute the current leave period and check if the user has a contract
        $this->load->model('contracts_model');
        $hasContract = $this->contracts_model->getBoundaries($id, $startentdate, $endentdate, $refDate);
        if ($hasContract) {
            $this->load->model('types_model');
            $this->load->model('users_model');
            //Fill a list of all existing leave types
            $summary = $this->types_model->allTypes($compensate_name);
            //Get the sum of entitled days
            $user = $this->users_model->getUsers($id);
            $entitlements = $this->getSumEntitledDays($id, $user['contract'], $refDate);
            
            foreach ($entitlements as $entitlement) {
                //Get the total of taken leaves grouped by type
                $this->db->select('SUM(leaves.duration) as taken, types.name as type');
                $this->db->from('leaves');
                $this->db->join('types', 'types.id = leaves.type');
                $this->db->where('leaves.employee', $id);
                $this->db->where('leaves.status', 3);
                $this->db->where('leaves.startdate >= ', $entitlement['min_date']);
                $this->db->where('leaves.enddate <=', $entitlement['max_date']);
                $this->db->where('leaves.type', $entitlement['type_id']);
                $this->db->group_by("leaves.type");
                $taken_days = $this->db->get()->result_array();
                //Count the number of taken days
                foreach ($taken_days as $taken) {
                    $summary[$taken['type']][0] = (float) $taken['taken']; //Taken
                }
                //Report the number of available days
                $summary[$entitlement['type_name']][1] = (float) $entitlement['entitled'];
            }
            
            //Add the validated catch up days
            //Employee must catch up in the year
            $this->db->select('duration, date, cause');
            $this->db->from('overtime');
            $this->db->where('employee', $id);
            $this->db->where("date >= DATE_SUB(STR_TO_DATE('" . $refDate . "', '%Y-%m-%d'),INTERVAL 1 YEAR)");
            $this->db->where('status = 3'); //Accepted
            $overtime_days = $this->db->get()->result_array();
            $sum = 0;
            foreach ($overtime_days as $entitled) {
                if ($sum_extra == FALSE) {
                    $summary['Catch up for ' . $entitled['date']][0] = '-'; //taken
                    $summary['Catch up for ' . $entitled['date']][1] = (float) $entitled['duration']; //entitled
                    $summary['Catch up for ' . $entitled['date']][2] = $entitled['cause']; //description
                }
                $sum += (float) $entitled['duration']; //entitled
            }
            $this->db->select('sum(leaves.duration) as taken');
            $this->db->from('leaves');
            $this->db->where('leaves.employee', $id);
            $this->db->where('leaves.status', 3);
            $this->db->where('leaves.type', 0);
            $this->db->where("leaves.startdate >= DATE_SUB(STR_TO_DATE('" . $refDate . "', '%Y-%m-%d'),INTERVAL 1 YEAR)");
            $this->db->group_by("leaves.type");
            $taken_days = $this->db->get()->result_array();
            if (count($taken_days) > 0) {
                $summary[$compensate_name][0] = (float) $taken_days[0]['taken']; //taken
            } else {
                $summary[$compensate_name][0] = 0; //taken
            }
            //Add the sum of validated catch up for the employee
            if (array_key_exists($compensate_name, $summary)) {
                $summary[$compensate_name][1] = (float) $summary[$compensate_name][1] + $sum; //entitled
            }
            
            //Remove all lines having taken and entitled set to set to 0
            foreach ($summary as $key => $value) {
                if ($value[0]==0 && $value[1]==0) {
                    unset($summary[$key]);
                }
            }
            return $summary;
        } else { //User attached to no contract
            return NULL;
        }        
    }
    
    /**
     * Get the number of days a user can take for a given leave type
     * @param int $id employee identifier
     * @param string $type type of leave request
     * @param date $startdate Start date of leave request or null
     * @return int number of available days or NULL if the user has no contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getLeavesTypeBalanceForEmployee($id, $type, $startdate = NULL) {
        $summary = $this->getLeaveBalanceForEmployee($id, TRUE, $startdate);
        //return entitled days - taken (for a given leave type)
        if (is_null($summary)) {
            return NULL;
        } else {
            if (array_key_exists($type, $summary)) {
                return ($summary[$type][1] - $summary[$type][0]);
            } else {
                return 0;
            }
        }
    }

    /**
     * Detect if the leave request overlaps with another request of the employee
     * @param int $id employee id
     * @param date $startdate start date of leave request being created
     * @param date $enddate end date of leave request being created
     * @param string $startdatetype start date type of leave request being created (Morning or Afternoon)
     * @param string $enddatetype end date type of leave request being created (Morning or Afternoon)
     * @param int $leave_id When this function is used for editing a leave request, we must not collide with this leave request
     * @return boolean TRUE if another leave request has been emmitted, FALSE otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function detectOverlappingLeaves($id, $startdate, $enddate, $startdatetype, $enddatetype, $leave_id=NULL) {
        $overlapping = FALSE;
        $this->db->where('employee', $id);
        $this->db->where('status != 4');
        $this->db->where('(startdate <= DATE(\'' . $enddate . '\') AND enddate >= DATE(\'' . $startdate . '\'))');
        if (!is_null($leave_id)) {
            $this->db->where('id != ', $leave_id);
        }
        $leaves = $this->db->get('leaves')->result();
        
        if ($startdatetype == "Morning") {
            $startTmp = strtotime($startdate." 08:00:00 UTC");
        } else {
            $startTmp = strtotime($startdate." 12:01:00 UTC");
        }
        if ($enddatetype == "Morning") {
            $endTmp = strtotime($enddate." 12:00:00 UTC");
        } else {
            $endTmp = strtotime($enddate." 18:00:00 UTC");
        }
        
        foreach ($leaves as $leave) {
            if ($leave->startdatetype == "Morning") {
                $startTmpDB = strtotime($leave->startdate." 08:00:00 UTC");
            } else {
                $startTmpDB = strtotime($leave->startdate." 12:01:00 UTC");
            }
            if ($leave->enddatetype == "Morning") {
                $endTmpDB = strtotime($leave->enddate." 12:00:00 UTC");
            } else {
                $endTmpDB = strtotime($leave->enddate." 18:00:00 UTC");
            }
            if (($startTmpDB <= $endTmp) && ($endTmpDB >= $startTmp)) {
                $overlapping = TRUE;
            }
        }
        return $overlapping;
    }
    
    /**
     * Create a leave request
     * @param int $id Identifier of the employee
     * @return int id of the newly created leave request into the db
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function setLeaves($id) {
        $data = array(
            'startdate' => $this->input->post('startdate'),
            'startdatetype' => $this->input->post('startdatetype'),
            'enddate' => $this->input->post('enddate'),
            'enddatetype' => $this->input->post('enddatetype'),
            'duration' => abs($this->input->post('duration')),
            'type' => $this->input->post('type'),
            'cause' => $this->input->post('cause'),
            'status' => $this->input->post('status'),
            'employee' => $id
        );
        $this->db->insert('leaves', $data);
        return $this->db->insert_id();
    }
    
    /**
     * Create the same leave request for a list of employees
     * @param int $type Identifier of the leave type
     * @param float $duration duration of the leave
     * @param string $startdate Start date (MySQL format YYYY-MM-DD)
     * @param string $enddate End date (MySQL format YYYY-MM-DD)
     * @param string $startdatetype Start date type of the leave (Morning/Afternoon)
     * @param string $enddatetype End date type of the leave (Morning/Afternoon)
     * @param string $cause Identifier of the leave
     * @param int $status status of the leave
     * @param array $employees List of DB Ids of the affected employees
     * @return int Result
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function createRequestForUserList($type, $duration, $startdate, $enddate,
                    $startdatetype, $enddatetype, $cause, $status, $employees) {
        $data = array();
        foreach ($employees as $id) {
            $data[] = array(
                'startdate' => $this->input->post('startdate'),
                'startdatetype' => $this->input->post('startdatetype'),
                'enddate' => $this->input->post('enddate'),
                'enddatetype' => $this->input->post('enddatetype'),
                'duration' => abs($this->input->post('duration')),
                'type' => $this->input->post('type'),
                'cause' => $this->input->post('cause'),
                'status' => $this->input->post('status'),
                'employee' => $id);
        }
        return $this->db->insert_batch('leaves', $data); 
    }

    /**
     * Create a leave request (suitable for API use)
     * @param string $startdate Start date (MySQL format YYYY-MM-DD)
     * @param string $enddate End date (MySQL format YYYY-MM-DD)
     * @param int $status Status of leave (see table status or doc)
     * @param int $employee Identifier of the employee
     * @param string $cause Optional reason of the leave
     * @param string $startdatetype Start date type (Morning/Afternoon)
     * @param string $enddatetype End date type (Morning/Afternoon)
     * @param float $duration duration of the leave request
     * @param int $type Type of leave (except compensate, fully customizable by user)
     * @return int id of the newly acreated leave request into the db
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function createLeaveByApi($startdate, $enddate, $status, $employee, $cause,
            $startdatetype, $enddatetype, $duration, $type) {
        
        $data = array(
            'startdate' => $startdate,
            'enddate' => $enddate,
            'status' => $status,
            'employee' => $employee,
            'cause' => $cause,
            'startdatetype' => $startdatetype,
            'enddatetype' => $enddatetype,
            'duration' => abs($duration),
            'type' => $type
        );
        $this->db->insert('leaves', $data);
        return $this->db->insert_id();
    }
    
    /**
     * Update a leave request in the database with the values posted by an HTTP POST
     * @param int $id of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function updateLeaves($id) {
        $data = array(
            'startdate' => $this->input->post('startdate'),
            'startdatetype' => $this->input->post('startdatetype'),
            'enddate' => $this->input->post('enddate'),
            'enddatetype' => $this->input->post('enddatetype'),
            'duration' => abs($this->input->post('duration')),
            'type' => $this->input->post('type'),
            'cause' => $this->input->post('cause'),
            'status' => $this->input->post('status')
        );
        $this->db->where('id', $id);
        $this->db->update('leaves', $data);
    }
    
    /**
     * Accept a leave request
     * @param int $id leave request identifier
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function acceptLeave($id) {
        $data = array(
            'status' => 3
        );
        $this->db->where('id', $id);
        return $this->db->update('leaves', $data);
    }

    /**
     * Reject a leave request
     * @param int $id leave request identifier
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function rejectLeave($id) {
        $data = array(
            'status' => 4
        );
        $this->db->where('id', $id);
        return $this->db->update('leaves', $data);
    }
    
    /**
     * Delete a leave from the database
     * @param int $id leave request identifier
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteLeave($id) {
        return $this->db->delete('leaves', array('id' => $id));
    }
    
    /**
     * Delete leaves attached to a user
     * @param int $id identifier of an employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteLeavesCascadeUser($id) {
        return $this->db->delete('leaves', array('employee' => $id));
    }
    
    /**
     * Leave requests of All leave request of the user (suitable for FullCalendar widget)
     * @param int $user_id connected user
     * @param string $start Unix timestamp / Start date displayed on calendar
     * @param string $end Unix timestamp / End date displayed on calendar
     * @return string JSON encoded list of full calendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual($user_id, $start = "", $end = "") {
        $this->db->select('leaves.*, types.name as type');
        $this->db->join('types', 'leaves.type = types.id');
        $this->db->where('employee', $user_id);
        $this->db->where('(leaves.startdate <= DATE(' . $this->db->escape($end) . ') AND leaves.enddate >= DATE(' . $this->db->escape($start) . '))');
        $this->db->order_by('startdate', 'desc');
        $this->db->limit(1024);  //Security limit
        $events = $this->db->get('leaves')->result();
        
        $jsonevents = array();
        foreach ($events as $entry) {
            
            if ($entry->startdatetype == "Morning") {
                $startdate = $entry->startdate . 'T07:00:00';
            } else {
                $startdate = $entry->startdate . 'T12:00:00';
            }

            if ($entry->enddatetype == "Morning") {
                $enddate = $entry->enddate . 'T12:00:00';
            } else {
                $enddate = $entry->enddate . 'T18:00:00';
            }
            
            $imageUrl = '';
            $allDay = FALSE;
            $startdatetype =  $entry->startdatetype;
            $enddatetype = $entry->enddatetype;
            if ($startdate == $enddate) { //Deal with invalid start/end date
                $imageUrl = base_url() . 'assets/images/date_error.png';
                $startdate = $entry->startdate . 'T07:00:00';
                $enddate = $entry->enddate . 'T18:00:00';
                $startdatetype = "Morning";
                $enddatetype = "Afternoon";
                $allDay = TRUE;
            }
            
            switch ($entry->status)
            {
                case 1: $color = '#999'; break;     // Planned
                case 2: $color = '#f89406'; break;  // Requested
                case 3: $color = '#468847'; break;  // Accepted
                case 4: $color = '#ff0000'; break;  // Rejected
            }
            
            $jsonevents[] = array(
                'id' => $entry->id,
                'title' => $entry->type,
                'imageurl' => $imageUrl,
                'start' => $startdate,
                'color' => $color,
                'allDay' => $allDay,
                'end' => $enddate,
                'startdatetype' => $startdatetype,
                'enddatetype' => $enddatetype
            );
        }
        return json_encode($jsonevents);
    }

    /**
     * Leave requests of All users having the same manager (suitable for FullCalendar widget)
     * @param int $user_id id of the manager
     * @param string $start Unix timestamp / Start date displayed on calendar
     * @param string $end Unix timestamp / End date displayed on calendar
     * @return string JSON encoded list of full calendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function workmates($user_id, $start = "", $end = "") {
        $this->db->join('users', 'users.id = leaves.employee');
        $this->db->where('users.manager', $user_id);
        $this->db->where('leaves.status != ', 4);       //Exclude rejected requests
        $this->db->where('(leaves.startdate <= DATE(' . $this->db->escape($end) . ') AND leaves.enddate >= DATE(' . $this->db->escape($start) . '))');
        $this->db->order_by('startdate', 'desc');
        $this->db->limit(1024);  //Security limit
        $events = $this->db->get('leaves')->result();
        
        $jsonevents = array();
        foreach ($events as $entry) {
            if ($entry->startdatetype == "Morning") {
                $startdate = $entry->startdate . 'T07:00:00';
            } else {
                $startdate = $entry->startdate . 'T12:00:00';
            }

            if ($entry->enddatetype == "Morning") {
                $enddate = $entry->enddate . 'T12:00:00';
            } else {
                $enddate = $entry->enddate . 'T18:00:00';
            }
            
            $imageUrl = '';
            $allDay = FALSE;
            $startdatetype =  $entry->startdatetype;
            $enddatetype = $entry->enddatetype;
            if ($startdate == $enddate) { //Deal with invalid start/end date
                $imageUrl = base_url() . 'assets/images/date_error.png';
                $startdate = $entry->startdate . 'T07:00:00';
                $enddate = $entry->enddate . 'T18:00:00';
                $startdatetype = "Morning";
                $enddatetype = "Afternoon";
                $allDay = TRUE;
            }
            
            switch ($entry->status)
            {
                case 1: $color = '#999'; break;     // Planned
                case 2: $color = '#f89406'; break;  // Requested
                case 3: $color = '#468847'; break;  // Accepted
                case 4: $color = '#ff0000'; break;  // Rejected
            }
            
            $jsonevents[] = array(
                'id' => $entry->id,
                'title' => $entry->firstname .' ' . $entry->lastname,
                'imageurl' => $imageUrl,
                'start' => $startdate,
                'color' => $color,
                'allDay' => $allDay,
                'end' => $enddate,
                'startdatetype' => $startdatetype,
                'enddatetype' => $enddatetype
            );
        }
        return json_encode($jsonevents);
    }

    /**
     * Leave requests of All users having the same manager (suitable for FullCalendar widget)
     * @param int $user_id id of the manager
     * @param string $start Unix timestamp / Start date displayed on calendar
     * @param string $end Unix timestamp / End date displayed on calendar
     * @return string JSON encoded list of full calendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators($user_id, $start = "", $end = "") {
        $this->db->join('users', 'users.id = leaves.employee');
        $this->db->where('users.manager', $user_id);
        $this->db->where('(leaves.startdate <= DATE(' . $this->db->escape($end) . ') AND leaves.enddate >= DATE(' . $this->db->escape($start) . '))');
        $this->db->order_by('startdate', 'desc');
        $this->db->limit(1024);  //Security limit
        $events = $this->db->get('leaves')->result();
        
        $jsonevents = array();
        foreach ($events as $entry) {
            if ($entry->startdatetype == "Morning") {
                $startdate = $entry->startdate . 'T07:00:00';
            } else {
                $startdate = $entry->startdate . 'T12:00:00';
            }

            if ($entry->enddatetype == "Morning") {
                $enddate = $entry->enddate . 'T12:00:00';
            } else {
                $enddate = $entry->enddate . 'T18:00:00';
            }
            
            $imageUrl = '';
            $allDay = FALSE;
            $startdatetype =  $entry->startdatetype;
            $enddatetype = $entry->enddatetype;
            if ($startdate == $enddate) { //Deal with invalid start/end date
                $imageUrl = base_url() . 'assets/images/date_error.png';
                $startdate = $entry->startdate . 'T07:00:00';
                $enddate = $entry->enddate . 'T18:00:00';
                $startdatetype = "Morning";
                $enddatetype = "Afternoon";
                $allDay = TRUE;
            }
            
            switch ($entry->status)
            {
                case 1: $color = '#999'; break;     // Planned
                case 2: $color = '#f89406'; break;  // Requested
                case 3: $color = '#468847'; break;  // Accepted
                case 4: $color = '#ff0000'; break;  // Rejected
            }
            
            $jsonevents[] = array(
                'id' => $entry->id,
                'title' => $entry->firstname .' ' . $entry->lastname,
                'imageurl' => $imageUrl,
                'start' => $startdate,
                'color' => $color,
                'allDay' => $allDay,
                'end' => $enddate,
                'startdatetype' => $startdatetype,
                'enddatetype' => $enddatetype
            );
        }
        return json_encode($jsonevents);
    }
    
    /**
     * Leave requests of All users of a department (suitable for FullCalendar widget)
     * @param int $entity_id Entity identifier (the department)
     * @param string $start Unix timestamp / Start date displayed on calendar
     * @param string $end Unix timestamp / End date displayed on calendar
     * @param bool $children Include sub department in the query
     * @return string JSON encoded list of full calendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function department($entity_id, $start = "", $end = "", $children = FALSE) {
        $this->db->select('users.firstname, users.lastname,  leaves.*, types.name as type');
        $this->db->from('organization');
        $this->db->join('users', 'users.organization = organization.id');
        $this->db->join('leaves', 'leaves.employee  = users.id');
        $this->db->join('types', 'leaves.type = types.id');
        $this->db->where('(leaves.startdate <= DATE(' . $this->db->escape($end) . ') AND leaves.enddate >= DATE(' . $this->db->escape($start) . '))');
        if ($children === TRUE) {
            $this->load->model('organization_model');
            $list = $this->organization_model->getAllChildren($entity_id);
            $ids = array();
            if ($list[0]['id'] != '') {
                $ids = explode(",", $list[0]['id']);
                array_push($ids, $entity_id);
                $this->db->where_in('organization.id', $ids);
            } else {
                $this->db->where('organization.id', $entity_id);
            }
        } else {
            $this->db->where('organization.id', $entity_id);
        }
        $this->db->where('leaves.status != ', 4);       //Exclude rejected requests
        $this->db->order_by('startdate', 'desc');
        $this->db->limit(1024);  //Security limit
        $events = $this->db->get()->result();
        $jsonevents = array();
        foreach ($events as $entry) {
            
            if ($entry->startdatetype == "Morning") {
                $startdate = $entry->startdate . 'T07:00:00';
            } else {
                $startdate = $entry->startdate . 'T12:00:00';
            }

            if ($entry->enddatetype == "Morning") {
                $enddate = $entry->enddate . 'T12:00:00';
            } else {
                $enddate = $entry->enddate . 'T18:00:00';
            }
            
            $imageUrl = '';
            $allDay = FALSE;
            $startdatetype =  $entry->startdatetype;
            $enddatetype = $entry->enddatetype;
            if ($startdate == $enddate) { //Deal with invalid start/end date
                $imageUrl = base_url() . 'assets/images/date_error.png';
                $startdate = $entry->startdate . 'T07:00:00';
                $enddate = $entry->enddate . 'T18:00:00';
                $startdatetype = "Morning";
                $enddatetype = "Afternoon";
                $allDay = TRUE;
            }
            
            switch ($entry->status)
            {
                case 1: $color = '#999'; break;     // Planned
                case 2: $color = '#f89406'; break;  // Requested
                case 3: $color = '#468847'; break;  // Accepted
                case 4: $color = '#ff0000'; break;  // Rejected
            }
            
            $jsonevents[] = array(
                'id' => $entry->id,
                'title' => $entry->firstname .' ' . $entry->lastname,
                'imageurl' => $imageUrl,
                'start' => $startdate,
                'color' => $color,
                'allDay' => $allDay,
                'end' => $enddate,
                'startdatetype' => $startdatetype,
                'enddatetype' => $enddatetype
            );
        }
        return json_encode($jsonevents);
    }
    
    /**
     * Leave requests of All users of an entity
     * @param int $entity_id Entity identifier (the department)
     * @param bool $children Include sub department in the query
     * @return array List of leave requests (DB records)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function entity($entity_id, $children = FALSE) {
        $this->db->select('users.firstname, users.lastname,  leaves.*, types.name as type');
        $this->db->from('organization');
        $this->db->join('users', 'users.organization = organization.id');
        $this->db->join('leaves', 'leaves.employee  = users.id');
        $this->db->join('types', 'leaves.type = types.id');
        if ($children === TRUE) {
            $this->load->model('organization_model');
            $list = $this->organization_model->getAllChildren($entity_id);
            $ids = array();
            if (count($list) > 0) {
                $ids = explode(",", $list[0]['id']);
            }
            array_push($ids, $entity_id);
            $this->db->where_in('organization.id', $ids);
        } else {
            $this->db->where('organization.id', $entity_id);
        }
        $this->db->where('leaves.status != ', 4);       //Exclude rejected requests
        $this->db->order_by('startdate', 'desc');
        $events = $this->db->get()->result_array();
        return $events;
    }
    
    /**
     * List all leave requests submitted to the connected user (or if delegate of a manager)
     * Can be filtered with "Requested" status.
     * @param int $manager connected user
     * @param bool $all TRUE all requests, FALSE otherwise
     * @return array Recordset (can be empty if no requests or not a manager)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getLeavesRequestedToManager($manager, $all = FALSE) {
        $this->load->model('delegations_model');
        $ids = $this->delegations_model->listManagersGivingDelegation($manager);
        $this->db->select('leaves.id as leave_id, users.*, leaves.*, types.name as type_label');
        $this->db->select('status.name as status_name, types.name as type_name');
        $this->db->join('status', 'leaves.status = status.id');
        $this->db->join('types', 'leaves.type = types.id');
        $this->db->join('users', 'users.id = leaves.employee');

        if (count($ids) > 0) {
            array_push($ids, $manager);
            $this->db->where_in('users.manager', $ids);
        } else {
            $this->db->where('users.manager', $manager);
        }
        if ($all == FALSE) {
            $this->db->where('leaves.status', 2);
        }
        $this->db->order_by('leaves.startdate', 'desc');
        $query = $this->db->get('leaves');
        return $query->result_array();
    }
    
    /**
     * Count leave requests submitted to the connected user (or if delegate of a manager)
     * @param int $manager connected user
     * @return int number of requests
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function countLeavesRequestedToManager($manager) {
        $this->load->model('delegations_model');
        $ids = $this->delegations_model->listManagersGivingDelegation($manager);
        $this->db->select('count(*) as number', FALSE);
        $this->db->join('users', 'users.id = leaves.employee');
        $this->db->where('leaves.status', 2);

        if (count($ids) > 0) {
            array_push($ids, $manager);
            $this->db->where_in('users.manager', $ids);
        } else {
            $this->db->where('users.manager', $manager);
        }
        $result = $this->db->get('leaves');
        return $result->row()->number;
    }
    
    /**
     * Purge the table by deleting the records prior $toDate
     * @param date $toDate 
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function purgeLeaves($toDate) {
        $this->db->where(' <= ', $toDate);
        return $this->db->delete('leaves');
    }

    /**
     * Count the number of rows into the table
     * @return int number of rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function count() {
        $this->db->select('count(*) as number', FALSE);
        $this->db->from('leaves');
        $result = $this->db->get();
        return $result->row()->number;
    }
    
    /**
     * All leaves between two timestamps, no filters
     * @param string $start Unix timestamp / Start date displayed on calendar
     * @param string $end Unix timestamp / End date displayed on calendar
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function all($start, $end) {
        $this->db->select("users.id as user_id, users.firstname, users.lastname, leaves.*", FALSE);
        $this->db->join('users', 'users.id = leaves.employee');
        $this->db->where('( (leaves.startdate <= FROM_UNIXTIME(' . $this->db->escape($start) . ') AND leaves.enddate >= FROM_UNIXTIME(' . $this->db->escape($start) . '))' .
                                   ' OR (leaves.startdate >= FROM_UNIXTIME(' . $this->db->escape($start) . ') AND leaves.enddate <= FROM_UNIXTIME(' . $this->db->escape($end) . ')))');
        $this->db->order_by('startdate', 'desc');
        return $this->db->get('leaves')->result();
    }
    
    /**
     * Leave requests of users of a department(s)
     * @param int $entity Entity identifier (the department)
     * @param int $month Month number
     * @param int $year Year number
     * @param bool $children Include sub department in the query
     * @return array Array of objects containing leave details
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function tabular(&$entity=-1, &$month=0, &$year=0, &$children=TRUE) {
        //Mangage paramaters
        if ($month==0) $month = date("m");
        if ($year==0) $year = date("Y");
        $children = filter_var($children, FILTER_VALIDATE_BOOLEAN);
        //If no entity was selected, select the entity of the connected user or the root of the organization
        if ($entity == -1) {
            if (!$this->session->userdata('logged_in')) {
                $entity = 0;
            } else {
                $this->load->model('users_model');
                $user = $this->users_model->getUsers($this->session->userdata('id'));
                $entity = $user['organization'];
            }
        }
        $tabular = array();
        
        //We must show all users of the departement
        $this->load->model('dayoffs_model');
        $this->load->model('organization_model');
        $employees = $this->organization_model->allEmployees($entity, $children);
        foreach ($employees as $employee) {
            $tabular[$employee->id] = $this->linear($employee->id, $month, $year, TRUE, TRUE, TRUE, FALSE);
        }
        return $tabular;
    }
    
    /**
     * Count the total duration of leaves for the month. Only accepted leaves are taken into account
     * @param array $linear linear calendar for one employee
     * @return int total of leaves duration
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function monthlyLeavesDuration($linear) {
        $total = 0;
        foreach ($linear->days as $day) {
          if (strstr($day->display, ';')) {
              $display = explode(";", $day->display);
              if ($display[0] == '2') $total += 0.5;
              if ($display[0] == '3') $total += 0.5;
              if ($display[1] == '2') $total += 0.5;
              if ($display[1] == '3') $total += 0.5;
          } else {
              if ($day->display == 2) $total += 0.5;
              if ($day->display == 3) $total += 0.5;
              if ($day->display == 1) $total += 1;
          }
        }
        return $total;
    }
    
    /**
     * Count the total duration of leaves for the month, grouped by leave type.
     * Only accepted leaves are taken into account.
     * @param array $linear linear calendar for one employee
     * @return array key/value array (k:leave type label, v:sum for the month)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function monthlyLeavesByType($linear) {
        $by_types = array();
        foreach ($linear->days as $day) {
          if (strstr($day->display, ';')) {
              $display = explode(";", $day->display);
              $type = explode(";", $day->type);
              if ($display[0] == '2') array_key_exists($type[0], $by_types) ? $by_types[$type[0]] += 0.5: $by_types[$type[0]] = 0.5;
              if ($display[0] == '3') array_key_exists($type[0], $by_types) ? $by_types[$type[0]] += 0.5: $by_types[$type[0]] = 0.5;
              if ($display[1] == '2') array_key_exists($type[1], $by_types) ? $by_types[$type[1]] += 0.5: $by_types[$type[1]] = 0.5;
              if ($display[1] == '3') array_key_exists($type[1], $by_types) ? $by_types[$type[1]] += 0.5: $by_types[$type[1]] = 0.5;
          } else {
              if ($day->display == 2) array_key_exists($day->type, $by_types) ? $by_types[$day->type] += 0.5: $by_types[$day->type] = 0.5;
              if ($day->display == 3) array_key_exists($day->type, $by_types) ? $by_types[$day->type] += 0.5: $by_types[$day->type] = 0.5;
              if ($day->display == 1) array_key_exists($day->type, $by_types) ? $by_types[$day->type] += 1: $by_types[$day->type] = 1;
          }
        }
        return $by_types;
    }
    
    /**
     * Leave requests of users of a department(s)
     * @param int $employee Employee identifier
     * @param int $month Month number
     * @param int $year Year number
     * @return array Array of objects containing leave details
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function linear($employee_id, $month, $year, 
            $planned = FALSE, $requested = FALSE, $accepted = FALSE, $rejected = FALSE) {
        $start = $year . '-' . $month . '-' .  '1';    //first date of selected month
        $lastDay = date("t", strtotime($start));    //last day of selected month
        $end = $year . '-' . $month . '-' . $lastDay;    //last date of selected month
        
        //We must show all users of the departement
        $this->load->model('dayoffs_model');
        $this->load->model('users_model');
        $employee = $this->users_model->getUsers($employee_id);
        $user = new stdClass;
        $user->name = $employee['firstname'] . ' ' . $employee['lastname'];
        $user->days = array();

        //Init all day to working day
        for ($ii = 1; $ii <= $lastDay; $ii++) {
            $day = new stdClass;
            $day->type = '';
            $day->status = '';
            $day->display = 0; //working day
            $user->days[$ii] = $day;
        }

        //Force all day offs (mind the case of employees having no leave)
        $dayoffs = $this->dayoffs_model->lengthDaysOffBetweenDatesForEmployee($employee_id, $start, $end);
        foreach ($dayoffs as $dayoff) {
            $iDate = new DateTime($dayoff->date);
            $dayNum = intval($iDate->format('d'));
            $user->days[$dayNum]->display = (string) $dayoff->type + 3;
            $user->days[$dayNum]->status = (string) $dayoff->type + 3;
            $user->days[$dayNum]->type = $dayoff->title;
        }
        
        //Build the complex query for all leaves
        $this->db->select('leaves.*, types.name as type');
        $this->db->from('leaves');
        $this->db->join('types', 'leaves.type = types.id');
        $this->db->where('(leaves.startdate <= DATE(' . $this->db->escape($end) . ') AND leaves.enddate >= DATE(' . $this->db->escape($start) . '))');
        if (!$planned) $this->db->where('leaves.status != ', 1);
        if (!$requested) $this->db->where('leaves.status != ', 2);
        if (!$accepted) $this->db->where('leaves.status != ', 3);
        if (!$rejected) $this->db->where('leaves.status != ', 4);        
        
        $this->db->where('leaves.employee = ', $employee_id);
        $this->db->order_by('startdate', 'asc');
        $events = $this->db->get()->result();
        $limitDate = DateTime::createFromFormat('Y-m-d H:i:s', $end . ' 00:00:00');
        $floorDate = DateTime::createFromFormat('Y-m-d H:i:s', $start . ' 00:00:00');
        
        $this->load->model('dayoffs_model');
        foreach ($events as $entry) {
            
            $startDate = DateTime::createFromFormat('Y-m-d H:i:s', $entry->startdate . ' 00:00:00');
            if ($startDate < $floorDate) $startDate = $floorDate;
            $iDate = clone $startDate;
            $endDate = DateTime::createFromFormat('Y-m-d H:i:s', $entry->enddate . ' 00:00:00');
            if ($endDate > $limitDate) $endDate = $limitDate;
            
            //Iteration between 2 dates
            while ($iDate <= $endDate)
            {
                if ($iDate > $limitDate) break;     //The calendar displays the leaves on one month
                if ($iDate < $startDate) continue;  //The leave starts before the first day of the calendar
                $dayNum = intval($iDate->format('d'));

                //Simplify logic
                if ($startDate == $endDate) $one_day = TRUE; else $one_day = FALSE;
                if ($entry->startdatetype == 'Morning') $start_morning = TRUE; else $start_morning = FALSE;
                if ($entry->startdatetype == 'Afternoon') $start_afternoon = TRUE; else $start_afternoon = FALSE;
                if ($entry->enddatetype == 'Morning') $end_morning = TRUE; else $end_morning = FALSE;
                if ($entry->enddatetype == 'Afternoon') $end_afternoon = TRUE; else $end_afternoon = FALSE;
                if ($iDate == $startDate) $first_day = TRUE; else $first_day = FALSE;
                if ($iDate == $endDate) $last_day = TRUE; else $last_day = FALSE;
                
                //Display (different from contract/calendar)
                //0 - Working day  _
                //1 - All day           []
                //2 - Morning        |\
                //3 - Afternoon      /|
                //4 - All Day Off       []
                //5 - Morning Day Off   |\
                //6 - Afternoon Day Off /|
                //9 - Error in start/end types
                
                //Length of leave request is one day long
                if ($one_day && $start_morning && $end_afternoon) $display = '1';
                if ($one_day && $start_morning && $end_morning) $display = '2';
                if ($one_day && $start_afternoon && $end_afternoon) $display = '3';
                if ($one_day && $start_afternoon && $end_morning) $display = '9';
                //Length of leave request is one day long is more than one day
                //We are in the middle of a long leave request
                if (!$one_day && !$first_day && !$last_day) $display = '1';
                //First day of a long leave request
                if (!$one_day && $first_day && $start_morning) $display = '1';
                if (!$one_day && $first_day && $start_afternoon) $display = '3';
                //Last day of a long leave request
                if (!$one_day && $last_day && $end_afternoon) $display = '1';
                if (!$one_day && $last_day && $end_morning) $display = '2';
                
                //Check if another leave was defined on this day
                if ($user->days[$dayNum]->display != '4') { //Except full day off
                    if ($user->days[$dayNum]->type != '') { //Overlapping with a day off or another request
                        if (($user->days[$dayNum]->display == 2) ||
                                ($user->days[$dayNum]->display == 6)) { //Respect Morning/Afternoon order
                            $user->days[$dayNum]->type .= ';' . $entry->type;
                            $user->days[$dayNum]->display .= ';' . $display;
                            $user->days[$dayNum]->status .= ';' . $entry->status;
                        } else {
                            $user->days[$dayNum]->type = $entry->type . ';' . $user->days[$dayNum]->type;
                            $user->days[$dayNum]->display = $display . ';' . $user->days[$dayNum]->display;
                            $user->days[$dayNum]->status = $entry->status . ';' . $user->days[$dayNum]->status;
                        }
                    } else  {   //All day entry
                        $user->days[$dayNum]->type = $entry->type;
                        $user->days[$dayNum]->display = $display;
                        $user->days[$dayNum]->status = $entry->status;
                    }
                }
                $iDate->modify('+1 day');   //Next day
            }   
        }
        return $user;
    }
    
    /**
     * List all duplicated leave requests (exact same dates, status, etc.)
     * Note: this doesn't detect overlapping requests.
     * @return array List of duplicated leave requests
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function detectDuplicatedRequests() {
        $this->db->select('leaves.id, CONCAT(users.firstname, \' \', users.lastname) as user_label', FALSE);
        $this->db->select('leaves.startdate, types.name as type_label');
        $this->db->from('leaves');
        $this->db->join('(SELECT * FROM leaves) dup', 'leaves.employee = dup.employee' .
                ' AND leaves.startdate = dup.startdate' .
                ' AND leaves.enddate = dup.enddate' .
                ' AND leaves.startdatetype = dup.startdatetype' .
                ' AND leaves.enddatetype = dup.enddatetype' .
                ' AND leaves.status = dup.status' .
                ' AND leaves.id != dup.id', 'inner');
        $this->db->join('users', 'users.id = leaves.employee', 'inner');
        $this->db->join('types', 'leaves.type = types.id', 'inner');
        $this->db->where('leaves.status', 3);   //Accepted
        $this->db->order_by("users.id", "asc"); 
        $this->db->order_by("leaves.startdate", "desc");
        return $this->db->get()->result_array();
    }
    
    /**
     * List all leave requests with a wrong date type (starting afternoon and ending morning of the same day)
     * @return array List of wrong leave requests
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function detectWrongDateTypes() {
        $this->db->select('leaves.*, CONCAT(users.firstname, \' \', users.lastname) as user_label', FALSE);
        $this->db->select('status.name as status_label');
        $this->db->from('leaves');
        $this->db->join('users', 'users.id = leaves.employee', 'inner');
        $this->db->join('status', 'leaves.status = status.id', 'inner');
        $this->db->where('leaves.startdatetype', 'Afternoon');
        $this->db->where('leaves.enddatetype', 'Morning');
        $this->db->where('leaves.startdate = leaves.enddate');
        $this->db->order_by("users.id", "asc"); 
        $this->db->order_by("leaves.startdate", "desc");
        return $this->db->get()->result_array();
    }
    
    /**
     * List of leave requests for which they are not entitled days on contracts or employee
     * Note: this might be an expected behaviour (avoid to track them into the balance report).
     * @return array List of duplicated leave requests
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function detectBalanceProblems() {
        $query = $this->db->query('SELECT CONCAT(users.firstname, \' \', users.lastname) AS user_label,
        contracts.id, contracts.name AS contract_label,
        types.name AS type_label,
        status.name AS status_label,
        leaves.*
        FROM leaves
        inner join users on leaves.employee = users.id
        inner join contracts on users.contract = contracts.id
        inner join types on types.id = leaves.type
        inner join status on status.id = leaves.status
        LEFT OUTER JOIN entitleddays
            ON (entitleddays.type = leaves.type AND
                (users.id = entitleddays.employee OR contracts.id = entitleddays.contract)
                        and entitleddays.startdate <= leaves.enddate AND entitleddays.enddate >= leaves.startdate)
        WHERE entitleddays.type IS NULL
        ORDER BY users.id ASC, leaves.startdate DESC', FALSE);
        return $query->result_array();  
    }

    /**
     * List of coming leaves
     * @param  Int $dayBefore is the number of days before a leave
     * @return array List of leave requests
     * @author Loc Nguyen <me@locnh.com>
     */
    public function getComingLeave($dayBefore) {
        $query =   "SELECT * FROM leaves AS l
                    JOIN users as u ON l.employee = u.id
                    WHERE startdate = DATE_ADD(CURDATE(), INTERVAL $dayBefore DAY)
                    AND status != 4";
        return $this->db->query($query)->result_array();
    }
}
