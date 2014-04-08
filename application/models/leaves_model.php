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
        
        //leaves['type'][x][x]
        $summary = array();
        $types = $this->db->get_where('types')->result_array();
        foreach ($types as $type) {
            $summary[$type['name']][0] = 0; //Taken
            $summary[$type['name']][1] = 0; //Entitled
        }
        
/*        var_dump($summary);
        die();*/
        
        /*select sum(leaves.duration) as taken, types.name as type
        from leaves
        inner join types on types.id = leaves.type
        where leaves.employee = 6
                and leaves.status = 3
        group by leaves.type*/
        
        //TODO : need to set boundaries (in-period)
        $this->db->select('sum(leaves.duration) as taken, types.name as type');
        $this->db->from('leaves');
        $this->db->join('types', 'types.id = leaves.type');
        $this->db->where('leaves.employee', $id);
        $this->db->where('leaves.status', 3); 
        $this->db->group_by("leaves.type");
        $taken_days = $this->db->get();
        var_dump($taken_days);
        foreach ($taken_days as $taken) {
            var_dump($taken);
            die();
            $summary[$taken['type']][0] = $taken['taken']; //Taken
        }

        //From contract
        /*
        select types.name, entitleddays.days
        from users
        inner join contracts on contracts.id = users.contract
        left outer join entitleddays on entitleddays.contract = users.contract
        inner join types on types.id = entitleddays.type
        where users.id = 6
         */
        
        //From entitled days of employee
        
        /*
        select types.name, entitleddays.days
        from users
        inner join contracts on contracts.id = users.contract
        left outer join entitleddays on entitleddays.employee = users.id
        inner join types on types.id = entitleddays.type
        where users.id = 6
         */
        
        return $summary;
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
        $this->db->where('employee', $user_id);
        $this->db->order_by('startdate', 'desc');
        $this->db->limit(70);
        $events = $this->db->get('leaves')->result();

        $jsonevents = array();
        foreach ($events as $entry) {
            $jsonevents[] = array(
                'id' => $entry->id,
                'title' => 'Leave',
                'start' => $entry->startdate,
                'allDay' => false,
                'end' => $entry->enddate
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
    public function team($user_id) {
        $this->db->join('users', 'users.id = leaves.employee');
        $this->db->where('users.manager', $user_id);
        $this->db->order_by('startdate', 'desc');
        $this->db->limit(70);
        $events = $this->db->get('leaves')->result();

        $jsonevents = array();
        foreach ($events as $entry) {
            $jsonevents[] = array(
                'id' => $entry->id,
                'title' => 'Leave',
                'start' => $entry->startdate,
                'allDay' => false,
                'end' => $entry->enddate
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
        //return $this->db->get('leaves')->result();
        $query = $this->db->get('leaves');
        return $query->result_array();
    }
}
