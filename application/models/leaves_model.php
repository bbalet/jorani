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

class Users_model extends CI_Model {

    public function __construct() {
        
    }

    public function get_users($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('users');
            return $query->result_array();
        }

        $query = $this->db->get_where('users', array('id' => $id));
        return $query->row_array();
    }

    public function set_users() {
        $data = array(
            'firstname' => $this->input->post('firstname'),
            'lastname' => $this->input->post('lastname')
        );

        return $this->db->insert('users', $data);
    }

}
