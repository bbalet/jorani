<?php
/**
 * This class contains the business logic and manages the persistence of contracts
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class contains the business logic and manages the persistence of contracts.
 */
class Contracts_model extends CI_Model {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        
    }

    /**
     * Get the list of contracts or one contract
     * @param int $id optional id of a contract
     * @return array records of contracts
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getContracts($id = 0) {
        if ($id === 0) {
            $this->db->order_by("name", "asc");
            $query = $this->db->get('contracts');
            return $query->result_array();
        }
        $query = $this->db->get_where('contracts', array('id' => $id));
        return $query->row_array();
    }
    
    /**
     * Get the name of a given contract
     * @param int $id Unique identifier of a contract
     * @return string name of the contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getName($id) {
        $record = $this->getContracts($id);
        if (count($record) > 0) {
            return $record['name'];
        } else {
            return '';
        }
    }
    
    /**
     * Insert a new contract into the database. Inserted data are coming from an HTML form
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function setContracts() {
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
    public function deleteContract($id) {
        $this->db->delete('contracts', array('id' => $id));
        $this->load->model('users_model');
        $this->load->model('entitleddays_model');
        $this->load->model('dayoffs_model');
        $this->entitleddays_model->deleteEntitledDaysCascadeContract($id);
        $this->dayoffs_model->deleteDaysOffCascadeContract($id);
        $this->users_model->updateUsersCascadeContract($id);
    }
    
    /**
     * Update a given contract in the database. Update data are coming from an HTML form
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function updateContract() {
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
}
