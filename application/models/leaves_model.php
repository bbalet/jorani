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

    public function __construct() {
        
    }

    public function get_leaves($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('leaves');
            return $query->result_array();
        }
        $query = $this->db->get_where('leaves', array('id' => $id));
        return $query->row_array();
    }

    public function get_user_leaves($id) {
        $query = $this->db->get_where('leaves', array('employee' => $id));
        return $query->result_array();
    }
    
    /**
     * Create a leave request
     * @return type query result
     */
    public function set_leaves() {
        $data = array(
            'startdate' => $this->input->post('startdate'),
            'startdatetype' => $this->input->post('startdatetype'),
            'enddate' => $this->input->post('enddate'),
            'enddatetype' => $this->input->post('enddatetype'),
            'duration' => $this->input->post('duration'),
            'cause' => $this->input->post('cause'),
            'status' => $this->input->post('status'),
            'employee' => $this->session->userdata('id')
        );
        return $this->db->insert('leaves', $data);
    }

    public function delete_leave($id = 0) {
        return $this->db->delete('leaves', array('id' => $id));
    }

    /**
     * All leave request of the user
     * @param type $id
     * @return type
     */
    public function individual($id) {
        $this->db->where('employee', $id);
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
     * @param type $id
     * @return type
     */
    public function team($id) {
        $this->db->join('users', 'users.id = leaves.employee');
        $this->db->where('users.manager', $id);
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

}
