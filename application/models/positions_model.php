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

class Positions_model extends CI_Model {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        
    }

    /**
     * Get the list of positions or one position
     * @param int $id optional id of a position
     * @return array record of positions
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getPositions($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('positions');
            return $query->result_array();
        }
        $query = $this->db->get_where('positions', array('id' => $id));
        return $query->row_array();
    }
    
    /**
     * Get the name of a position
     * @param int $id Identifier of the postion
     * @return string Name of the position
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getName($id) {
        $record = $this->getPositions($id);
        return $record['name'];
    }
    
    /**
     * Insert a new position
     * @param string $name Name of the postion
     * @param string $description Description of the postion
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function setPositions($name, $description) {
        $data = array(
            'name' => $name,
            'description' => $description
        );
        return $this->db->insert('positions', $data);
    }
    
    /**
     * Delete a position from the database
     * Cascade update all users having this postion (filled with 0)
     * @param int $id identifier of the position record
     * @return bool TRUE if the operation was successful, FALSE otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deletePosition($id) {
        $delete = $this->db->delete('positions', array('id' => $id));
        $data = array(
            'position' => 0
        );
        $this->db->where('position', $id);
        $update = $this->db->update('users', $data);
        return $delete && $update;
    }
    
    /**
     * Update a given position in the database.
     * @param int $id Identifier of the database
     * @param string $name Name of the postion
     * @param string $description Description of the postion
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function updatePositions($id, $name, $description) {
        $data = array(
            'name' => $name,
            'description' => $description
        );
        $this->db->where('id', $id);
        return $this->db->update('positions', $data);
    }
}
