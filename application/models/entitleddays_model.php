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
        $query = $this->db->get_where('entitleddays', array('contract' => $contract));
        return $query->row_array();
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
     * Delete a entitleddays record from the database
     * @param int $id identifier of the entitleddays record
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_entitleddays($id) {
        $query = $this->db->delete('entitleddays', array('id' => $id));
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
    
}
