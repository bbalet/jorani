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

class Contracts_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {
        
    }

    /**
     * Get the list of contracts or one contract
     * @param int $id optional id of a contract
     * @return array record of contracts
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_contracts($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('contracts');
            return $query->result_array();
        }
        $query = $this->db->get_where('contracts', array('id' => $id));
        return $query->row_array();
    }
    
    /**
     * Insert a new contract into the database. Inserted data are coming from an
     * HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_contracts() {
        $startentdate = str_pad($this->input->post('startentdatemonth'), 2, "0", STR_PAD_LEFT) .
                "/" . str_pad($this->input->post('startentdateday'), 2, "0", STR_PAD_LEFT);
        $endentdate = str_pad($this->input->post('endentdatemonth'), 2, "0", STR_PAD_LEFT) .
                "/" . str_pad($this->input->post('endentdateday'), 2, "0", STR_PAD_LEFT);
        $data = array(
            'name' => $this->input->post('name'),
            'startentdate' => $startentdate,
            'endentdate' => $endentdate
        );
        return $this->db->insert('contracts', $data);
    }
    
    /**
     * Delete a contract from the database
     * @param int $id identifier of the contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_contract($id) {
        $query = $this->db->delete('contracts', array('id' => $id));
    }
    
    /**
     * Update a given contract in the database. Update data are coming from an
     * HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function update_contract() {
        
        $startentdate = str_pad($this->input->post('startentdatemonth'), 2, "0", STR_PAD_LEFT) .
                "/" . str_pad($this->input->post('startentdateday'), 2, "0", STR_PAD_LEFT);
        $endentdate = str_pad($this->input->post('endentdatemonth'), 2, "0", STR_PAD_LEFT) .
                "/" . str_pad($this->input->post('endentdateday'), 2, "0", STR_PAD_LEFT);
        $data = array(
            'name' => $this->input->post('name'),
            'startentdate' => $startentdate,
            'endentdate' => $endentdate
        );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('users', $data);
    }
    
}
