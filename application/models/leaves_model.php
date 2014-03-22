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

    public function set_leaves() {      
        $data = array(
            'startdate' => $this->input->post('startdate'),
            'startdatetype' => $this->input->post('startdatetype'),
            'enddate' => $this->input->post('enddate'),
            'enddatetype' => $this->input->post('enddatetype'),
            'duration' => $this->input->post('duration'),
            'cause' => $this->input->post('cause'),
            'status' => $this->input->post('status')
        );        
        return $this->db->insert('leaves', $data);
    }

    public function delete_leave($id = 0) {
        $query = $this->db->delete('leaves', array('id' => $id));
    }

}
