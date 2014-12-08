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

class Entitleddays_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {
        
    }

    /**
     * Get the list of entitleddays or one entitleddays record
     * @param int $id optional id of a entitleddays record
     * @return array record of periods
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_entitleddays_contract($contract) {
        $this->db->select('entitleddays.*, types.name as type');
        $this->db->from('entitleddays');
        $this->db->join('types', 'types.id = entitleddays.type');
        $this->db->order_by("startdate", "desc");
        $this->db->where('contract =', $contract);
        return $this->db->get()->result_array();
    }
    
    /**
     * Get the list of entitleddays or one entitleddays record
     * @param int $id optional id of a entitleddays record
     * @return array record of periods
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_entitleddays_employee($employee) {
        $this->db->select('entitleddays.*, types.name as type');
        $this->db->from('entitleddays');
        $this->db->join('types', 'types.id = entitleddays.type');
        $this->db->order_by("startdate", "desc");
        $this->db->where('employee =', $employee);
        return $this->db->get()->result_array();
    }
    
    /**
     * Insert a new entitleddays record into the database. 
     * Inserted data are coming from an HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_entitleddays_employee() {
        
        $data = array(
            'employee' => $this->input->post('id'),
            'startdate' => $this->input->post('startdate'),
            'enddate' => $this->input->post('enddate'),
            'days' => $this->input->post('days'),
            'type' => $this->input->post('type')
        );
        return $this->db->insert('entitleddays', $data);
    }
    
    /**
     * Insert a new entitleddays record into the database and return the id
     * @param int $contract_id contract identifier
     * @param date $startdate Start Date
     * @param date $enddate End Date
     * @param int $days number of days to be added
     * @param int $type Leave type (of the entitled days line)
     * @param int $description Description of the entitled days line
     * @return int last inserted id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function insert_entitleddays_contract($contract_id, $startdate, $enddate, $days, $type, $description) {
        $data = array(
            'contract' => $contract_id,
            'startdate' => $startdate,
            'enddate' => $enddate,
            'days' => $days,
            'type' => $type,
            'description' => $description
        );
        $this->db->insert('entitleddays', $data);
        return $this->db->insert_id();
    }

    /**
     * Insert a new entitleddays record into the database and return the id
     * @param int $user_id employee identifier
     * @param date $startdate Start Date
     * @param date $enddate End Date
     * @param int $days number of days to be added
     * @param int $type Leave type (of the entitled days line)
     * @param int $description Description of the entitled days line
     * @return int last inserted id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function insert_entitleddays_employee($user_id, $startdate, $enddate, $days, $type, $description) {
        $data = array(
            'employee' => $user_id,
            'startdate' => $startdate,
            'enddate' => $enddate,
            'days' => $days,
            'type' => $type,
            'description' => $description
        );
        $this->db->insert('entitleddays', $data);
        return $this->db->insert_id();
    }
    
    /**
     * Insert a new entitleddays record into the database. 
     * Inserted data are coming from an HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_entitleddays_contract() {
        
        $data = array(
            'contract' => $this->input->post('id'),
            'startdate' => $this->input->post('startdate'),
            'enddate' => $this->input->post('enddate'),
            'days' => $this->input->post('days'),
            'type' => $this->input->post('type')
        );
        return $this->db->insert('entitleddays', $data);
    }
    
    /**
     * Delete a entitleddays record from the database
     * @param int $id identifier of the entitleddays record
     * @return int number of rows affected
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_entitleddays($id) {
        return $this->db->delete('entitleddays', array('id' => $id));
    }

    /**
     * Delete a entitled days attached to a user
     * @param int $id identifier of an employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_entitleddays_cascade_user($id) {
        $query = $this->db->delete('entitleddays', array('employee' => $id));
    }
    
    /**
     * Delete a entitled days attached to a contract
     * @param int $id identifier of a contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_entitleddays_cascade_contract($id) {
        $query = $this->db->delete('entitleddays', array('contract' => $id));
    }
    
    /**
     * Update a given contract in the database. Update data are coming from an
     * HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function update_entitleddays() {
        
        $data = array(
            'name' => $this->input->post('name'),
            'startdate' => $this->input->post('startdate'),
            'enddate' => $this->input->post('enddate')
        );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('entitleddays', $data);
    }
    
    /**
     * Increase an entitled days row
     * @param int $id row identifier
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function inc_entitleddays($id) {
        $this->db->set('days', 'days + 1', FALSE);
        $this->db->where('id', $id);
        return $this->db->update('entitleddays');
    }
    
    /**
     * Decrease an entitled days row
     * @param int $id row identifier
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function dec_entitleddays($id) {
        $this->db->set('days', 'days - 1', FALSE);
        $this->db->where('id', $id);
        return $this->db->update('entitleddays');
    }
    
    /**
     * Modify the the amount of days for a given entitled days row
     * @param int $id row identifier
     * @param float $days credit in days
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function update_days_entitleddays($id, $days) {
        $this->db->set('days', $days);
        $this->db->where('id', $id);
        return $this->db->update('entitleddays');
    }
    
    /**
     * Purge the table by deleting the records prior $toDate
     * @param date $toDate 
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function purge_entitleddays($toDate) {
        $this->db->where('enddate <= ', $toDate);
        return $this->db->delete('entitleddays');
    }

    /**
     * Count the number of rows into the table
     * @return int number of rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function count() {
        $this->db->select('count(*) as number', FALSE);
        $this->db->from('entitleddays');
        $result = $this->db->get();
        return $result->row()->number;
    }
}
