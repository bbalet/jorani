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

class Delegations_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {

    }

    /**
     * Get the list of delegations of a manager
     * @param int $id id of manager
     * @return array record of users
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_delegates($id) {
        $this->db->select('delegations.*, CONCAT(firstname, \' \', lastname) as delegate_name', FALSE);
        $this->db->join('users', 'delegations.delegate_id = users.id');
        $query = $this->db->get_where('delegations', array('manager_id' => $id));
        return $query->result_array();
    }
    
    /**
     * Return TRUE if an employee is the delegate of a manager, FALSE otherwise
     * @param int $employee id of the employee to be checked
     * @param int $manager id of a manager
     * @return bool is delegate
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function IsDelegate($employee, $manager) {
        $this->db->from('delegations');
        $this->db->where('delegate_id', $employee);
        $this->db->where('manager_id', $manager);
        $results = $this->db->get()->row_array();
        if (count($results) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Return TRUE if an employee has any delegation, FALSE otherwise
     * @param int $employee id of the employee to be checked
     * @return bool has delegation
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function HasDelegation($employee) {
        $this->db->from('delegations');
        $this->db->where('delegate_id', $employee);
        $results = $this->db->get()->row_array();
        if (count($results) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Get the list of manager ids for which an employee has the delegation
     * @param int $employee id of an employee
     * @return array of employee identifiers
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_delegates_list($id) {
        $this->db->select("manager_id");
        $this->db->from('delegations');
        $this->db->where('delegate_id', $id);
        $results = $this->db->get()->result_array();
        $ids = array();
        foreach ($results as $row) {
            array_push($ids, $row['manager_id']);
        }
        return $ids;
    }
    
    /**
     * Get the list of e-mails of employees having the delegation from a manager
     * @param int $id id of a manager
     * @return array record of users
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_delegates_mails($id) {
        $this->db->select("GROUP_CONCAT(email SEPARATOR ',') as list", FALSE);
        $this->db->from('delegations');
        $this->db->join('users', 'delegations.delegate_id = users.id');
        $this->db->group_by('manager_id');
        $this->db->where('manager_id', $id);
        $query = $this->db->get();
        $results = $query->row_array();
        if (count($results) > 0) {
            return $results['list'];
        } else {
            return '';
        }
    }
    
    /**
     * Insert a list of day offs into the day offs table
     * @param int $contract Identifier of the contract
     * @param int $type 1:day, 2:morning, 3:afternoon
     * @return bool outcome of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function add_delegate($manager, $delegate) {
        $data = array(
            'manager_id' => $manager,
            'delegate_id' => $delegate
        );
        $this->db->insert('delegations', $data);
        return $this->db->insert_id();
    }
    
    /**
     * Delete a delegation from the database
     * @param int $id identifier of the delegation
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_delegation($id) {
        $query = $this->db->delete('delegations', array('id' => $id));
    }

}
