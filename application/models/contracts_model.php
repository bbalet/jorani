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
     * @param int $id Unique identifier of a contract
     * @return string label of the contract
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
     * @return int number of affected rows
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
     * @return int number of affected rows
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
     * Computes the boundaries (current leave period) of the contract of a user
     * Modifies the start and end dates passed as parameter
     * @param int Unique identifier of a user
     * @param &date start date of the current leave period 
     * @param &date end date of the current leave period 
     * @param string $refDate tmp of the Date of reference (or current date if NULL)
     * @return bool TRUE means that the user has a contract, FALSE otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getBoundaries($userId, &$startentdate, &$endentdate, $refDate = NULL) {
        $this->db->select('startentdate, endentdate');
        $this->db->from('contracts');
        $this->db->join('users', 'users.contract = contracts.id');
        $this->db->where('users.id', $userId);
        $boundaries = $this->db->get()->result_array();
        
        if ($refDate == NULL) {
            $refDate = date("Y-m-d");
        }
        $refYear = substr($refDate, 0, 4);
        $refMonth = substr($refDate, 5, 2);
        $nextYear = strval(intval($refYear) + 1);
        $lastYear = strval(intval($refYear) - 1);
        
        if (count($boundaries) != 0) {
            $startmonth = intval(substr($boundaries[0]['startentdate'], 0, 2));
            if ($startmonth == 1 ) {
                $startentdate = $refYear . "-" . str_replace("/", "-", $boundaries[0]['startentdate']);
                $endentdate =  $refYear . "-" . str_replace("/", "-", $boundaries[0]['endentdate']);
            } else {
                if (intval($refMonth) < 6) {
                    $startentdate = $lastYear . "-" . str_replace("/", "-", $boundaries[0]['startentdate']);
                    $endentdate = $refYear . "-" . str_replace("/", "-", $boundaries[0]['endentdate']);
                } else {
                    $startentdate = $refYear . "-" . str_replace("/", "-", $boundaries[0]['startentdate']);
                    $endentdate = $nextYear . "-" . str_replace("/", "-", $boundaries[0]['endentdate']);
                }
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Computes the boundaries (last leave period, i.e. year - 1) of the contract of a user
     * Modifies the start and end dates passed as parameter
     * @param int Unique identifier of a user
     * @param &date start date of the current leave period 
     * @param &date end date of the current leave period 
     * @return bool TRUE means that the user has a contract, FALSE otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getLastBoundaries($userId, &$startentdate, &$endentdate) {
        $this->db->select('startentdate, endentdate');
        $this->db->from('contracts');
        $this->db->join('users', 'users.contract = contracts.id');
        $this->db->where('users.id', $userId);
        $boundaries = $this->db->get()->result_array();
        
        if (count($boundaries) != 0) {
            $startmonth = intval(substr($boundaries[0]['startentdate'], 0, 2));
            if ($startmonth == 1 ) {
                $startentdate = date("Y",strtotime("-1 year")) . "-" . str_replace("/", "-", $boundaries[0]['startentdate']);
                $endentdate =  date("Y",strtotime("-1 year")) . "-" . str_replace("/", "-", $boundaries[0]['endentdate']);
            } else {
                if (intval(date('m')) < 6) {
                    $startentdate = date("Y", strtotime("-2 year")) . "-" . str_replace("/", "-", $boundaries[0]['startentdate']);
                    $endentdate = date("Y",strtotime("-1 year")) . "-" . str_replace("/", "-", $boundaries[0]['endentdate']);
                } else {
                    $startentdate = date("Y",strtotime("-1 year")) . "-" . str_replace("/", "-", $boundaries[0]['startentdate']);
                    $endentdate = date("Y") . "-" . str_replace("/", "-", $boundaries[0]['endentdate']);
                }
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
