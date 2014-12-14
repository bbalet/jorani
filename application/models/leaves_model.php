<?php
/*
 * This file is part of lms.
 *
 * lms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with lms.  If not, see <http://www.gnu.org/licenses/>.
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
     * @param date $start
     * @param date $end
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
     * @return array computed aggregated taken/entitled leaves
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_user_leaves_summary($id, $sum_extra = FALSE) {
        //Compute the current leave period and check if the user has a contract
        $this->load->model('contracts_model');
        $hasContract = $this->contracts_model->getBoundaries($id, $startentdate, $endentdate);
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
            $this->db->where('leaves.startdate > ', $startentdate);
            $this->db->where('leaves.enddate <', $endentdate);
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
            $this->db->where('entitleddays.startdate < CURDATE()');
            $this->db->where('entitleddays.enddate > CURDATE()');
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
            $this->db->where('entitleddays.startdate < CURDATE()');
            $this->db->where('entitleddays.enddate > CURDATE()');
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
            $this->db->where('date >= DATE_SUB(NOW(),INTERVAL 1 YEAR)');
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
            if ($sum_extra == TRUE) {
                $this->db->select('sum(leaves.duration) as taken');
                $this->db->from('leaves');
                $this->db->where('leaves.employee', $id);
                $this->db->where('leaves.status', 3);
                $this->db->where('leaves.type', 0);
                $this->db->where('leaves.startdate >= DATE_SUB(NOW(),INTERVAL 1 YEAR)');
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
            return null;
        }        
    }
    
    /**
     * Compute the carried over leaves of an employee (used by report). Overtime is not taken into account
     * @param int $id ID of the employee
     * @return array computed aggregated taken/entitled leaves
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_user_leaves_carried_over($id) {
        //Compute the current leave period and check if the user has a contract
        $this->load->model('contracts_model');
        $hasContract = $this->contracts_model->getLastBoundaries($id, $startentdate, $endentdate);
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
            $this->db->where('leaves.startdate > ', $startentdate);
            $this->db->where('leaves.enddate <', $endentdate);
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
            $this->db->where('entitleddays.startdate < DATE_SUB(NOW(),INTERVAL 1 YEAR)');
            $this->db->where('entitleddays.enddate > DATE_SUB(NOW(),INTERVAL 1 YEAR)');
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
            $this->db->where('entitleddays.startdate < DATE_SUB(NOW(),INTERVAL 1 YEAR)');
            $this->db->where('entitleddays.enddate > DATE_SUB(NOW(),INTERVAL 1 YEAR)');
            $entitled_days = $this->db->get()->result_array();

            foreach ($entitled_days as $entitled) {
                //Note: this is an addition to the entitled days attached to the contract
                //Most common use cases are 'special leave', 'maternity leave'...
                $summary[$entitled['type']][1] += (float) $entitled['entitled']; //entitled
            }
            
            //Remove all lines having taken and entitled set to set to 0
            foreach ($summary as $key => $value) {
                if ($value[0]==0 && $value[1]==0) {
                    unset($summary[$key]);
                }
            }
            return $summary;
        } else { //User attached to no contract
            return null;
        }        
    }
    
    /**
     * Get the number of days a user can take for a given leave type
     * @param int $id employee id
     * @param int $type type of leave
     * @return int number of days not taken
     */
    public function get_user_leaves_credit($id, $type) {
        $summary = $this->get_user_leaves_summary($id);
        //return entitled days - taken (for a given leave type)
        return ($summary[$type][1] - $summary[$type][0]);
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
     * Update a leave request in the database
     * @param type $id
     * @return type
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
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function accept_leave($id) {
        $data = array(
            'status' => 3
        );
        $this->db->where('id', $id);
        $this->db->update('leaves', $data);
    }

    /**
     * Reject a leave request
     * @param int $id leave request identifier
     * @return type
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
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_leave($id) {
        return $this->db->delete('leaves', array('id' => $id));
    }
    
    /**
     * All leave request of the user
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
        $this->db->limit(255);  //Security limit
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
     * All users having the same manager
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
        $this->db->limit(255);  //Security limit
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
     * All users having the same manager
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
        $this->db->limit(255);  //Security limit
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
     * All leave request of the user
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
}
