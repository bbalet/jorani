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

class Types_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {
        
    }

    /**
     * Get the list of types or one type
     * @param int $id optional id of a type
     * @return array record of types
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_types($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('types');
            return $query->result_array();
        }
        $query = $this->db->get_where('types', array('id' => $id));
        return $query->row_array();
    }
    
    /**
     * Get the label of a given type id
     * @param int $id ID of the type
     * @return string label of the type
     */
    public function get_label($id) {
        $type = $this->get_types($id);
        return $type['name'];
    }
    
    /**
     * Insert a new leave type
     * Inserted data are coming from an HTML form
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_types() {
        
        $data = array(
            'name' => $this->input->post('name')
        );
        return $this->db->insert('types', $data);
    }
    
    /**
     * Delete a leave type from the database
     * @param int $id identifier of the leave type record
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_type($id) {
        $this->db->delete('types', array('id' => $id));
    }
    
    /**
     * Update a given leave type in the database. Update data are coming from an
     * HTML form
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function update_types() {
        $data = array(
            'name' => $this->input->post('name')
        );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('types', $data);
    }
    
    /**
     * Count the number of time a leave type is used into the database
     * @param int $id identifier of the leave type record
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function usage($id) {
        $this->db->select('COUNT(*)');
        $this->db->from('leaves');
        $this->db->where('type', $id);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result['COUNT(*)'];
    }
    
    /**
     * Create an arry containing the list of all existing leave types
     * Modify the name (key) of the compensate leave type name passed as parameter
     * @param &$compensate_name compensate leave type name
     * @return array Bi-dimensionnal array of types
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function allTypes(&$compensate_name) {
        $summary = array();
        $types = $this->db->get_where('types')->result_array();
        foreach ($types as $type) {
            $summary[$type['name']][0] = 0; //Taken
            if ($type['id'] != 0) {
                $summary[$type['name']][1] = 0; //Entitled
                $summary[$type['name']][2] = ''; //Description is only filled for catch-up
            } else {
                $compensate_name = $type['name'];
                $summary[$type['name']][1] = '-'; //Entitled for catch up
                $summary[$type['name']][2] = ''; //Description is only filled for catch-up
            }
        }
        return $summary;
    }
}
