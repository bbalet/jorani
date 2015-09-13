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
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */

class Status_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {
        
    }

    /**
     * Get the list of status or one status
     * @param int $id optional id of a status
     * @return array record of statuses
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_status($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('status');
            return $query->result_array();
        }
        $query = $this->db->get_where('status', array('id' => $id));
        return $query->row_array();
    }
    
    /**
     * Get the label of a given status id
     * @param type $id
     * @return string label
     */
    public function get_label($id) {
        switch ($id) {
            case 1 : return 'Planned';
            case 2 : return 'Requested';
            case 3 : return 'Accepted';
            case 4 : return 'Rejected';
            default : return 'Unknown';
        }
    }
}