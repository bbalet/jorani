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
     */
    public function __construct() {
        
    }

    /**
     * Get the list of positions or one position
     * @param int $id optional id of a position
     * @return array record of types
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_positions($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('positions');
            return $query->result_array();
        }
        $query = $this->db->get_where('positions', array('id' => $id));
        return $query->row_array();
    }
    
    /**
     * Get the label of a given position id
     * @param type $id
     * @return string label
     */
    public function get_label($id) {
        $record = $this->get_positions($id);
        if (count($record) > 0) {
            return $record['name'];
        } else {
            return '';
        }
    }
    
    /**
     * Insert a new position
     * Inserted data are coming from an HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_positions() {
        
        $data = array(
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description')
        );
        return $this->db->insert('positions', $data);
    }
    
    /**
     * Delete a position from the database
     * Cascade update all users having this postion (filled with 0)
     * @param int $id identifier of the position record
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_position($id) {
        $delete = $this->db->delete('positions', array('id' => $id));
        $data = array(
            'position' => 0
        );
        $this->db->where('position', $id);
        $update = $this->db->update('users', $data);
        return $delete && $update;
    }
    
    /**
     * Update a given position in the database. Update data are coming from an
     * HTML form
     * @param int $id Identifier of the database
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function update_positions($id) {
        $data = array(
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description')
        );

        $this->db->where('id', $id);
        return $this->db->update('positions', $data);
    }
}
	