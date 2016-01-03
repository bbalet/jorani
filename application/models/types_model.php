<?php
/**
 * This Class contains all the business logic and the persistence layer for the types of leave request.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This Class contains all the business logic and the persistence layer for the types of leave request.
 */
class Types_model extends CI_Model {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        
    }

    /**
     * Get the list of types or one type
     * @param int $id optional id of a type
     * @return array record of types
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getTypes($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('types');
            return $query->result_array();
        }
        $query = $this->db->get_where('types', array('id' => $id));
        return $query->row_array();
    }
    
    /**
     * Get the name of a given type id
     * @param int $id ID of the type
     * @return string label of the type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getName($id) {
        $type = $this->getTypes($id);
        return $type['name'];
    }
    
    /**
     * Insert a new leave type
     * @param string $name name of the type
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function setTypes($name) {
        $data = array(
            'name' => $this->input->post('name')
        );
        return $this->db->insert('types', $data);
    }
    
    /**
     * Delete a leave type from the database
     * @param int $id identifier of the leave type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteType($id) {
        $this->db->delete('types', array('id' => $id));
    }
    
    /**
     * Update a given leave type in the database.
     * @param int $id identifier of the leave type
     * @param string $name name of the type
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function updateTypes($id, $name) {
        $data = array(
            'name' => $name
        );
        $this->db->where('id', $id);
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
