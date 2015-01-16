<?php
/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jorani is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
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
    public function get_leaves($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('leaves');
            return $query->result_array();
        }
        $query = $this->db->get_where('leaves', array('id' => $id));
        return $query->row_array();
    }

    /**
     * Get the the list of leaves requested for a given employee
     * @param int $id ID of the employee
     * @return array list of records
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_user_leaves($id) {
        $query = $this->db->get_where('leaves', array('employee' => $id));
        return $query->result_array();
    }

    /**
     * Get the the list of leaves requested for a given employee
     * Id are replaced by label
     * @param int $id ID of the employee
     * @return array list of records
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_employee_leaves($id) {
        $this->db->select('leaves.id, status.name as status, leaves.startdate, leaves.enddate, leaves.duration, types.name as type');
        $this->db->from('leaves');
        $this->db->join('status', 'leaves.status = status.id');
        $this->db->join('types', 'leaves.type = types.id');
        $this->db->where('leaves.employee', $id);
        $this->db->order_by('leaves.id', 'desc');
        return $this->db->get()->result_array();
    }
    
    /**
     * Try to calculate the lenght of a leave using the start and and date of the leave
     * and the non working days defined on a contract
     * @param int $employee
     * @param date $start start date of the leave request
     * @param date $end end date of the leave request
     * @return float length of leave
     */
    public function length($employee, $start, $end) {
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
            return $numberDays;
        }
    }
    
    /**
     * Compute the leave balance of an employee (used by report and counters)
     * @param int $id ID of the employee
     * @param bool $sum_extra TRUE: sum compensate summary
     * @param string $refDate tmp of the Date of reference (or current date if NULL)
     * @return array computed aggregated taken/entitled leaves
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_user_leaves_summary($id, $sum_extra = FALSE, $refDate = NULL) {
        //Determine if we use current date or another date
        if ($refDate == NULL) {
            $refDate = date("Y-m-d");
        }

        //Compute the current leave period and check if the user has a contract
        $this->load->model('contracts_model');
        $hasContract = $this->contracts_model->getBoundaries($id, $startentdate, $endentdate, $refDate);
        if ($hasContract) {
            //Fill a list of all existing leave types
            $this->load->model('types_model');
            $summary = $this->types_model->allTypes($compensate_name);
            
            //Get the total of taken leaves grouped by type
            $this->db->select('sum(leaves.duration) as taken, types.name as type');
            $this->db->from('leaves');
            $this->db->join('types', 'types.id = leaves.type');
            $this->db->where('leaves.employee', $id);
            $this->db->where('leaves.status', 3);
            $this->db->where('leaves.startdate >= ', $startentdate);
            $this->db->where('leaves.enddate <=', $endentdate);
            $this->db->group_by("leaves.type");
            $taken_days = $this->db->get()->result_array();
            foreach ($taken_days as $taken) {
                $summary[$taken['type']][0] = (float) $taken['taken']; //Taken
            }
            
            //Get the total of entitled days affected to a contract (between 2 dates)
            $this->db->select('types.name as type, entitleddays.days as entitled');
            $this->db->from('users');
            $this->db->join('contracts', 'contracts.id = users.contract');
            $this->db->join('entitleddays', 'entitleddays.contract = users.contract', 'left outer');
            $this->db->join('types', 'types.id = entitleddays.type');
            $this->db->where('users.id', $id);
            $this->db->where('entitleddays.startdate <= ', $refDate);
            $this->db->where('entitleddays.enddate >= ', $refDate);
            $entitled_days = $this->db->get()->result_array();
            foreach ($entitled_days as $entitled) {
                $summary[$entitled['type']][1] = (float) $entitled['entitled']; //entitled
            }

            //Add entitled days of employee (number of entitled days can be negative)
            $this->db->select('types.name as type, entitleddays.days as entitled');
            $this->db->from('users');
            $this->db->join('contracts', 'contracts.id = users.contract');
            $this->db->join('entitleddays', 'entitleddays.employee = users.id', 'left outer');
            $this->db->join('types', 'types.id = entitleddays.type');
            $this->db->where('users.id', $id);
            $this->db->where('entitleddays.startdate <= ', $refDate);
            $this->db->where('entitleddays.enddate >= ', $refDate);
            $entitled_days = $this->db->get()->result_array();

            foreach ($entitled_days as $entitled) {
                //Note: this is an addition to the entitled days attached to the contract
                //Most common use cases are 'special leave', 'maternity leave'...
                $summary[$entitled['type']][1] += (float) $entitled['entitled']; //entitled
            }
            
            //Add the validated catch up days
            //Employee must catch up in the year
            $this->db->select('duration, date, cause');
            $this->db->from('overtime');
            $this->db->where('employee', $id);
            $this->db->where("date >= DATE_SUB(STR_TO_DATE('" . $refDate . "', '%Y-%m-%d'),INTERVAL 1 YEAR)");
            $this->db->where('status = 3'); //Accepted
            $overtime_days = $this->db->get()->result_array();
            //$this->output->enable_profiler(TRUE);
            $sum = 0;
            foreach ($overtime_days as $entitled) {
                if ($sum_extra == FALSE) {
                    $summary['Catch up for ' . $entitled['date']][0] = '-'; //taken
                    $summary['Catch up for ' . $entitled['date']][1] = (float) $entitled['duration']; //entitled
                    $summary['Catch up for ' . $entitled['date']][2] = $entitled['cause']; //description
                }
                $sum += (float) $entitled['duration']; //entitled
            }
            if ($sum_extra == TRUE) {
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
     * @param int $id employee id
     * @param string $type type of leave or NULL if the user has no contract/no
     * @return int number of days not taken
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_user_leaves_credit($id, $type) {
        $summary = $this->get_user_leaves_summary($id);
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
     * @return boolean TRUE if another leave request has been emmitted, FALSE otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function detect_overlapping_leaves($id, $startdate, $enddate, $startdatetype, $enddatetype) {
        $overlapping = FALSE;
        $this->db->where('employee', $id);
        $this->db->where('status != 4');
        $this->db->where('(startdate <= DATE(\'' . $enddate . '\') AND enddate >= DATE(\'' . $startdate . '\'))');
        $leaves = $this->db->get('leaves')->result();
        foreach ($leaves as $leave) {
            if (($leave->startdate != $startdate) && ($leave->enddate != $enddate)) {
                //Leave request is exactly within another leave request
                $overlapping = TRUE;
            } else {
                if (($leave->startdate == $startdate) && ($leave->enddate == $enddate)) {
                    //Two leave requests of one day or exactly the same periods (possible duplicate)
                    if (($leave->startdatetype == $startdatetype) || ($leave->enddatetype == $enddatetype)) $overlapping = TRUE;
                } else {
                    //Compute a pattern that is human readable
                    $pattern = $leave->startdatetype . $leave->enddatetype . $startdatetype . $enddatetype;
                    if ($startdate == $leave->startdate) {
                        //Example of a leave request that starts the same day, but of one day
                        switch ($pattern) {
                            case'MorningMorningMorningMorning': $overlapping = TRUE; break;
                            case'MorningMorningMorningAfternoon': $overlapping = TRUE; break;
                            case'MorningMorningAfternoonAfternoon': $overlapping = TRUE; break;
                            case'MorningAfternoonMorningAfternoon': $overlapping = TRUE; break;
                            case'MorningAfternoonMorningMorning': $overlapping = TRUE; break;
                            case'MorningAfternoonAfternoonAfternoon': $overlapping = TRUE; break;
                            case'AfternoonAfternoonAfternoonAfternoon': $overlapping = TRUE; break;
                            case'AfternoonAfternoonMorningAfternoon': $overlapping = TRUE; break;
                            case'AfternoonMorningMorningAfternoon': $overlapping = TRUE; break;
                            case'AfternoonMorningAfternoonAfternoon': $overlapping = TRUE; break;
                        }
                    }
                    if ($startdate == $leave->enddate) {
                        //Example of a leave request that starts the same day onother request ends, but of one day
                        switch ($pattern) {
                            case'MorningMorningMorningMorning': $overlapping = TRUE; break;
                            case'MorningMorningMorningAfternoon': $overlapping = TRUE; break;
                            case'MorningAfternoonMorningAfternoon': $overlapping = TRUE; break;
                            case'MorningAfternoonMorningMorning': $overlapping = TRUE; break;
                            case'MorningAfternoonAfternoonAfternoon': $overlapping = TRUE; break;
                            case'AfternoonAfternoonAfternoonAfternoon': $overlapping = TRUE; break;
                            case'AfternoonAfternoonMorningAfternoon': $overlapping = TRUE; break;
                            case'AfternoonAfternoonMorningMorning': $overlapping = TRUE; break;
                            case'AfternoonMorningMorningMorning': $overlapping = TRUE; break;
                            case'AfternoonMorningMorningAfternoon': $overlapping = TRUE; break;
                        }
                    }
                    if ($enddate == $leave->startdate) {
                        //Example of a leave request that ends the same day onother request starts
                        switch ($pattern) {
                            case'AfternoonAfternoonMorningAfternoon': $overlapping = TRUE; break;
                            case'AfternoonAfternoonAfternoonAfternoon': $overlapping = TRUE; break;
                            case'AfternoonMorningMorningAfternoon': $overlapping = TRUE; break;
                            case'AfternoonMorningAfternoonAfternoon': $overlapping = TRUE; break;
                        }
                    }
                }
            }
        }
        return $overlapping;
    }
    
    /**
     * Create a leave request
     * @return int id of the leave request into the db
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_leaves() {
        $data = array(
            'startdate' => $this->input->post('startdate'),
            'startdatetype' => $this->input->post('startdatetype'),
            'enddate' => $this->input->post('enddate'),
            'enddatetype' => $this->input->post('enddatetype'),
            'duration' => $this->input->post('duration'),
            'type' => $this->input->post('type'),
            'cause' => $this->input->post('cause'),
            'status' => $this->input->post('status'),
            'employee' => $this->session->userdata('id')
        );
        $this->db->insert('leaves', $data);
        return $this->db->insert_id();
    }

    /**
     * Update a leave request in the database with the values posted by an HTTP POST
     * @param type $id of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function update_leaves($id) {
        $data = array(
            'startdate' => $this->input->post('startdate'),
            'startdatetype' => $this->input->post('startdatetype'),
            'enddate' => $this->input->post('enddate'),
            'enddatetype' => $this->input->post('enddatetype'),
            'duration' => $this->input->post('duration'),
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
    public function accept_leave($id) {
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
    public function reject_leave($id) {
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
    public function delete_leave($id) {
        return $this->db->delete('leaves', array('id' => $id));
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
        $this->db->where('( (leaves.startdate <= DATE(\'' . $start . '\') AND leaves.enddate >= DATE(\'' . $start . '\'))' .
                                  ' OR (leaves.startdate >= DATE(\'' . $start . '\') AND leaves.enddate <= DATE(\'' . $end . '\')) )');
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
                'start' => $startdate,
                'color' => $color,
                'allDay' => false,
                'end' => $enddate
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
        $this->db->where('( (leaves.startdate <= DATE(\'' . $start . '\') AND leaves.enddate >= DATE(\'' . $start . '\'))' .
                                   ' OR (leaves.startdate >= DATE(\'' . $start . '\') AND leaves.enddate <= DATE(\'' . $end . '\')))');
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
                'start' => $startdate,
                'color' => $color,
                'allDay' => false,
                'end' => $enddate
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
        $this->db->where('( (leaves.startdate <= DATE(\'' . $start . '\') AND leaves.enddate >= DATE(\'' . $start . '\'))' .
                                ' OR (leaves.startdate >= DATE(\'' . $start . '\') AND leaves.enddate <= DATE(\'' . $end . '\')) )');
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
                'start' => $startdate,
                'color' => $color,
                'allDay' => false,
                'end' => $enddate
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
    public function department($entity_id, $start = "", $end = "", $children = false) {
        $this->db->select('users.firstname, users.lastname,  leaves.*, types.name as type');
        $this->db->from('organization');
        $this->db->join('users', 'users.organization = organization.id');
        $this->db->join('leaves', 'leaves.employee  = users.id');
        $this->db->join('types', 'leaves.type = types.id');
        $this->db->where('( (leaves.startdate <= DATE(\'' . $start . '\') AND leaves.enddate >= DATE(\'' . $start . '\'))' .
                                    ' OR (leaves.startdate >= DATE(\'' . $start . '\') AND leaves.enddate <= DATE(\'' . $end . '\')) )');
        if ($children == true) {
            $this->load->model('organization_model');
            $list = $this->organization_model->get_all_children($entity_id);
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
        $this->db->limit(512);  //Security limit
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
                'start' => $startdate,
                'color' => $color,
                'allDay' => false,
                'end' => $enddate
            );
        }
        return json_encode($jsonevents);
    }    
    
    /**
     * List all leave requests submitted to the connected user or only those
     * with the "Requested" status.
     * @param int $user_id connected user
     * @param bool $all true all requests, false otherwise
     * @return array Recordset (can be empty if no requests or not a manager)
     */
    public function requests($user_id, $all = false) {
        $this->db->select('leaves.id as id, users.*, leaves.*');
        $this->db->join('users', 'users.id = leaves.employee');
        $this->db->where('users.manager', $user_id);
        if (!$all) {
            $this->db->where('status', 2);
        }
        $this->db->order_by('startdate', 'desc');
        $query = $this->db->get('leaves');
        return $query->result_array();
    }
    
    /**
     * Purge the table by deleting the records prior $toDate
     * @param date $toDate 
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function purge_leaves($toDate) {
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
        $this->db->where('( (leaves.startdate <= FROM_UNIXTIME(\'' . $start . '\') AND leaves.enddate >= FROM_UNIXTIME(\'' . $start . '\'))' .
                                   ' OR (leaves.startdate >= FROM_UNIXTIME(\'' . $start . '\') AND leaves.enddate <= FROM_UNIXTIME(\'' . $end . '\')))');
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
    public function tabular(&$entity=0, &$month=0, &$year=0, &$children=TRUE) {
        //Mangage paramaters
        if ($month==0) $month = date("m");
        if ($year==0) $year = date("Y");
        $children = filter_var($children, FILTER_VALIDATE_BOOLEAN);
        $start = $year . '-' . $month . '-' .  '1';    //first date of selected month
        $lastDay = date("t", strtotime($start));    //last day of selected month
        $end = $year . '-' . $month . '-' . $lastDay;    //last date of selected month
        //If no entity was selected, select the entity of the connected user or the root of the organization
        if ($entity == 0) {
            $this->load->model('users_model');
            $user = $this->users_model->get_users($this->session->userdata('id'));
            if (is_null($user['organization'])) {
                $entity = 0;
            } else {
                $entity = $user['organization'];
            }
        }
        $tabular = array();
        
        //We must show all users of the departement
        $this->load->model('organization_model');
        $employees = $this->organization_model->all_employees($entity, $children);
        foreach ($employees as $employee) {
            $user = new stdClass;
            $user->name = $employee->firstname . ' ' . $employee->lastname;
            $user->days = array();
            
            //Init all day to working day
            for ($ii = 1; $ii <= $lastDay; $ii++) {
                $day = new stdClass;
                $day->type = '';
                $day->status = '';
                $day->display = 0; //working day
                $user->days[$ii] = $day;
            }
            $tabular[$employee->id] = $user;
        }
        
        //Build the complex query for all leaves
        $this->db->select('users.id as uid, users.firstname, users.lastname, leaves.*, types.name as type');
        $this->db->from('organization');
        $this->db->join('users', 'users.organization = organization.id');
        $this->db->join('leaves', 'leaves.employee  = users.id');
        $this->db->join('types', 'leaves.type = types.id');
        $this->db->where('( (leaves.startdate <= DATE(\'' . $start . '\') AND leaves.enddate >= DATE(\'' . $start . '\'))' .
                                    ' OR (leaves.startdate >= DATE(\'' . $start . '\') AND leaves.enddate <= DATE(\'' . $end . '\')) )');
        
        if ($children == true) {
            $this->load->model('organization_model');
            $list = $this->organization_model->get_all_children($entity);
            $ids = array();
            if (count($list) > 0) {
                $ids = explode(",", $list[0]['id']);
            }
            array_push($ids, $entity);
            $this->db->where_in('organization.id', $ids);
        } else {
            $this->db->where('organization.id', $entity);
        }
        $this->db->where('leaves.status != ', 4);       //Exclude rejected requests
        $this->db->order_by('startdate', 'asc');
        $this->db->limit(512);  //Security limit
        $events = $this->db->get()->result();
        $limitDate = new DateTime($end);
        
        $this->load->model('dayoffs_model');
        foreach ($events as $entry) {
                                        
            $iDate = new DateTime($entry->startdate);
            $endDate = new DateTime($entry->enddate);
            $startDate = new DateTime($entry->startdate);

            //Iteration between 2 dates
            while ($iDate <= $endDate)
            {
                
                if ($iDate > $limitDate) break;     //The calendar displays the leaves on one month
                if ($iDate < $startDate) continue;  //The leave starts before the first day of the calendar
                $dayNum = intval($iDate->format('d'));
                
                //Display (different from contract/calendar)
                //0 - Working day  _
                //1 - All day           []
                //2 - Morning        |\
                //3 - Afternoon      /|
                //4 - Day Off
                if (($startDate == $endDate) && ($entry->startdatetype == 'Morning') && ($entry->enddatetype == 'Afternoon')) $display = '1';
                if (($startDate == $endDate) && ($entry->startdatetype == 'Morning') && ($entry->enddatetype == 'Morning')) $display = '2';
                if (($startDate == $endDate) && ($entry->startdatetype == 'Afternoon') && ($entry->enddatetype == 'Afternoon')) $display = '3';
                if (($startDate != $endDate) && ($entry->startdatetype == 'Morning')) $display = '1';
                if (($startDate != $endDate) && ($entry->startdatetype == 'Afternoon')) $display = '3';
                if (($startDate != $endDate) && ($entry->enddatetype == 'Morning')) $display = '2';
                if (($startDate != $endDate) && ($iDate != $startDate) && ($iDate != $endDate)) $display = '1';
                if (($startDate != $endDate) && ($iDate == $startDate) && ($entry->startdatetype == 'Morning')) $display = '1';
                if (($startDate != $endDate) && ($iDate == $startDate) && ($entry->startdatetype == 'Afternoon')) $display = '3';
                if (($startDate != $endDate) && ($iDate == $endDate) && ($entry->startdatetype == 'Afternoon')) $display = '1';
                
                //Check if another leave was defined on this day
                if ($tabular[$entry->uid]->days[$dayNum]->type != '') {
                    $tabular[$entry->uid]->days[$dayNum]->type .= ';' . $entry->type;
                    $tabular[$entry->uid]->days[$dayNum]->display .= ';' . $display;
                    $tabular[$entry->uid]->days[$dayNum]->status .= ';' . $entry->status;
                } else  {
                    $tabular[$entry->uid]->days[$dayNum]->type = $entry->type;
                    $tabular[$entry->uid]->days[$dayNum]->display = $display;
                    $tabular[$entry->uid]->days[$dayNum]->status = $entry->status;
                }
                $iDate->modify('+1 day');   //Next day
            }
            
            //Force all day offs
            $dayoffs = $this->dayoffs_model->employee_all_dayoffs($entry->uid, $start, $end);
            foreach ($dayoffs as $dayoff) {
                $iDate = new DateTime($dayoff->date);
                $dayNum = intval($iDate->format('d'));
                $tabular[$entry->uid]->days[$dayNum]->display = '4';
                $tabular[$entry->uid]->days[$dayNum]->type = $dayoff->title;
            }
        }
        return $tabular;
    }
}
