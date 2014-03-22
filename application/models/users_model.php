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

    /**
     * Default constructor
     */
    public function __construct() {
        $this->load->database();
        //Load password hasher for create/update functions
        $this->load->library('bcrypt');
    }

    /**
     * Get the list of users or one user
     * @param int $id optional id of one user
     * @return array record of users
     */
    public function get_users($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('users');
            return $query->result_array();
        }
        $query = $this->db->get_where('users', array('id' => $id));
        return $query->row_array();
    }

    /**
     * Delete a user from the database
     * @param int $id identifier of the user
     */
    public function delete_user($id = 0) {
        $query = $this->db->delete('users', array('id' => $id));
    }

    /**
     * Insert a new user into the database. Inserted data are coming from an
     * HTML form
     * @return type
     */
    public function set_users() {
        $hash = $this->bcrypt->hash_password($this->input->post('password'));
        $data = array(
            'firstname' => $this->input->post('firstname'),
            'lastname' => $this->input->post('lastname'),
            'login' => $this->input->post('login'),
            'email' => $this->input->post('email'),
            'password' => $hash,
            'role' => $this->input->post('role'),
            'manager' => $this->input->post('manager')
        );
        return $this->db->insert('users', $data);
    }

    /**
     * Update a given user in the database. Update data are coming from an
     * HTML form
     * @return type
     */
    public function update_users() {
        $data = array(
            'firstname' => $this->input->post('firstname'),
            'lastname' => $this->input->post('lastname')
        );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('users', $data);
    }

}
