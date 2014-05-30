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
     * Get the label for a given contract id
     * @param type $id
     * @return string label
     */
    public function get_label($id) {
        $record = $this->get_contracts($id);
        if (count($record) > 0) {
            return $record['name'];
        } else {
            return '';
        }
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
        $this->load->model('entitleddays_model');
        $this->entitleddays_model->delete_entitleddays_cascade_contract($id);
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
        return $this->db->update('contracts', $data);
    }
    
    /**
     * Get the list of contracts or one contract
     * @param int $contract identifier of the contract
     * @param string $year year to be displayed on the calendar
     * @return array record of contracts
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_dayoffs($contract, $year) {
        $this->db->select('UNIX_TIMESTAMP(date) as timestamp, type, title');
        $this->db->where('contract', $contract);
        $this->db->where('YEAR(date)', $year);
        $query = $this->db->get('dayoffs');
        foreach($query->result() as $row)
        {
            $dayoffs[$row->timestamp][0] = $row->type;
            $dayoffs[$row->timestamp][1] = $row->title;
        }
        return $dayoffs;
    }
    
    /**
     * Insert a day off into the day offs table
     * @param int $contract Identifier of the contract
     * @param string $timestamp Date of the day off
     * @return bool outcome of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deletedayoff($contract, $timestamp) {
        $this->db->where('contract', $contract);
        $this->db->where('date', date('Y/m/d', $timestamp));
        return $this->db->delete('dayoffs');
    }
    
    /**
     * Insert a day off into the day offs table
     * @param int $contract Identifier of the contract
     * @param string $timestamp Date of the day off
     * @param int $type 1:day, 2:morning, 3:afternoon
     * @param string $title Short description of the day off
     * @return bool outcome of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function adddayoff($contract, $timestamp, $type, $title) {

        $this->db->select('id');
        $this->db->where('contract', $contract);
        $this->db->where('date', date('Y/m/d', $timestamp));
        $query = $this->db->get('dayoffs');
        if ($query->num_rows() > 0) {
            $data = array(
                'date' => date('Y/m/d', $timestamp),
                'type' => $type,
                'title' => $title
            );
            $this->db->where('id', $query->row('id'));
            return $this->db->update('dayoffs', $data);
        } else {
            $data = array(
                'contract' => $contract,
                'date' => date('Y/m/d', $timestamp),
                'type' => $type,
                'title' => $title
            );
            return $this->db->insert('dayoffs', $data);
        }
    }
}
