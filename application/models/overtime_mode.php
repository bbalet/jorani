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
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

class Overtime_model extends CI_Model {

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
     * Get the the list of leaves requested by a given employee
     * @param int $id ID of the employee
     * @return array list of records
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_user_leaves($id) {
        $query = $this->db->get_where('leaves', array('employee' => $id));
        return $query->result_array();
    }

    /**
     * Get the the list of entitled and taken leaves of a given employee
     * @param int $id ID of the employee
     * @return array computed aggregated taken/entitled leaves
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_user_leaves_summary($id) {
        //Compute the boundaries of the contract of the user
        $this->db->select('startentdate, endentdate');
        $this->db->from('contracts');
        $this->db->join('users', 'users.contract = contracts.id');
        $this->db->where('users.id', $id);
        $boundaries = $this->db->get()->result_array();
        
        if (count($boundaries) != 0) {
        
            $startmonth = intval(substr($boundaries[0]['startentdate'], 2));
            if ($startmonth < 6 ) {
                $startentdate = date("Y") . "-" . str_replace("/", "-", $boundaries[0]['startentdate']);
                $endentdate =  date("Y") . "-" . str_replace("/", "-", $boundaries[0]['endentdate']);
            } else {
                $startentdate = date("Y", strtotime("-1 year")) . "-" . str_replace("/", "-", $boundaries[0]['startentdate']);
                $endentdate = date("Y", strtotime("+1 year")) . "-" . str_replace("/", "-", $boundaries[0]['endentdate']);
            }        

            //Fill a list of all existing leave types
            $summary = array();
            $types = $this->db->get_where('types')->result_array();
            foreach ($types as $type) {
                $summary[$type['name']][0] = 0; //Taken
                $summary[$type['name']][1] = 0; //Entitled
            }
            
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
                $summary[$taken['type']][0] = $taken['taken']; //Taken
            }

            //Get the total of entitled days affected to a contract (between 2 dates)
            $this->db->select('types.name as type, entitleddays.days as entitled');
            $this->db->from('users');
            $this->db->join('contracts', 'contracts.id = users.contract');
            $this->db->join('entitleddays', 'entitleddays.contract = users.contract', 'left outer');
            $this->db->join('types', 'types.id = entitleddays.type');
            $this->db->where('users.id', $id);
            $this->db->where('entitleddays.startdate >= ', $startentdate);
            $this->db->where('entitleddays.enddate <=', $endentdate);
            $this->db->group_by("entitleddays.type");
            $entitled_days = $this->db->get()->result_array();

            foreach ($entitled_days as $entitled) {
                $summary[$entitled['type']][1] = $entitled['entitled']; //entitled
            }

            //Add entitled days of employee      
            $this->db->select('types.name as type, entitleddays.days as entitled');
            $this->db->from('users');
            $this->db->join('contracts', 'contracts.id = users.contract');
            $this->db->join('entitleddays', 'entitleddays.employee = users.id', 'left outer');
            $this->db->join('types', 'types.id = entitleddays.type');
            $this->db->where('users.id', $id);
            $this->db->where('entitleddays.startdate >= ', $startentdate);
            $this->db->where('entitleddays.enddate <=', $endentdate);
            $this->db->group_by("entitleddays.type");
            $entitled_days = $this->db->get()->result_array();

            foreach ($entitled_days as $entitled) {
                //Note: this is an addition to the entitled days attached to the contract
                //Most common use cases are 'special leave', 'maternity leave'...
                $summary[$entitled['type']][1] += $entitled['entitled']; //entitled
            }

            //Remove all lines having taken and entitled set to set to 0
            foreach ($summary as $key => $value) {
                if ($value[0]==0 && $value[1]==0) {
                    unset($summary[$key]);
                }
            }
        } else { //User attached to no contract
            $summary = null;
        }
        
        return $summary;
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
        //log_message('debug', '{models/requests_model/reject} Entering method with id=' . $id);
        $data = array(
            'status' => 3
        );
        $this->db->where('id', $id);
        $this->db->update('leaves', $data);
        //log_message('debug', '{models/requests_model/reject} SQL=' . $this->db->last_query());
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
     * @return string JSON encoded list of full calendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual($user_id) {
        $this->db->select('leaves.*, types.name as type');
        $this->db->join('types', 'leaves.type = types.id');
        $this->db->where('employee', $user_id);
        $this->db->order_by('startdate', 'desc');
        $this->db->limit(70);
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
                case 1: $color = 'grey'; break;     // Planned
                case 2: $color = 'blue'; break;     // Requested
                case 3: $color = 'yellow'; break;   // Accepted
                case 4: $color = 'red'; break;      // Rejected
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
     * @return string JSON encoded list of full calendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function workmates($user_id) {
        $this->db->join('users', 'users.id = leaves.employee');
        $this->db->where('users.manager', $user_id);
        $this->db->where('leaves.status != ', 4);       //Exclude rejected requests
        $this->db->order_by('startdate', 'desc');
        $this->db->limit(70);
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
                case 1: $color = 'grey'; break;     // Planned
                case 2: $color = 'blue'; break;     // Requested
                case 3: $color = 'yellow'; break;   // Accepted
                case 4: $color = 'red'; break;      // Rejected
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
     * @return string JSON encoded list of full calendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators($user_id) {
        $this->db->join('users', 'users.id = leaves.employee');
        $this->db->where('users.manager', $user_id);
        $this->db->where('leaves.status != ', 4);       //Exclude rejected requests
        $this->db->order_by('startdate', 'desc');
        $this->db->limit(70);
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
                case 1: $color = 'grey'; break;     // Planned
                case 2: $color = 'blue'; break;     // Requested
                case 3: $color = 'yellow'; break;   // Accepted
                case 4: $color = 'red'; break;      // Rejected
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
}
