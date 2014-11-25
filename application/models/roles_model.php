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

class Roles_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {

        /*
            00000001 1  Admin
            00000010 2	User
            00000100 8	HR Officier / Local HR Manager
            00001000 16	HR Manager
            00010000 32	General Manager
            00100000 34	Global Manager
         * 
         */
    }

    /**
     * Get the list of roles or one role
     * @param int $id optional id of one role
     * @return array record of roles
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_roles($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('roles');
            return $query->result_array();
        }
        $query = $this->db->get_where('roles', array('id' => $id));
        return $query->row_array();
    }
}