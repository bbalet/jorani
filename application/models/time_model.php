<?php
/**
 * This Class contains all the business logic and the persistence layer for the time tracking module.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This Class contains all the business logic and the persistence layer for the time tracking module.
 * As of today, the module is not yet implemented.
 */
class Time_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {
        //This class will be used in a coming version of Jorani.
    }

    /**
     * Get the list of activities or one activity
     * @param int $id optional id of a contract
     * @return array record of contracts
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getActivities($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('activities');
            return $query->result_array();
        }
        $query = $this->db->get_where('activities', array('id' => $id));
        return $query->row_array();
    }
    
    /**
     * Get the name for a given identifier of an Activity
     * @param int $id Identifier of an activity
     * @return string name of the activity
     */
    public function getName($id) {
        $record = $this->getActivities($id);
        if (count($record) > 0) {
            return $record['name'];
        } else {
            return '';
        }
    }
    
    /**
     * Insert a new activity into the database. Inserted data are coming from an HTML form
     * @return bool Status of last DB operation
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function setActivities() {
        $startentdate = str_pad($this->input->post('startentdatemonth'), 2, "0", STR_PAD_LEFT) .
                "/" . str_pad($this->input->post('startentdateday'), 2, "0", STR_PAD_LEFT);
        $endentdate = str_pad($this->input->post('endentdatemonth'), 2, "0", STR_PAD_LEFT) .
                "/" . str_pad($this->input->post('endentdateday'), 2, "0", STR_PAD_LEFT);
        $data = array(
            'name' => $this->input->post('name'),
            'startentdate' => $startentdate,
            'endentdate' => $endentdate
        );
        return $this->db->insert('activities', $data);
    }
    
    /**
     * Delete a activity from the database
     * @param int $id identifier of the contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteActivity($id) {
        $this->db->delete('activities', array('id' => $id));
        //Maybe a cascade deletion here
    }
    
    /**
     * Update a given activity in the database. Update data are coming from an HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function updateActivity() {
        
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
        return $this->db->update('activities', $data);
    }

    /**
     * Purge the table by deleting the records prior $toDate
     * @param date $toDate 
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function purgeActivities($toDate) {
        $this->db->where('DATE(datetime) <= ', $toDate);
        return $this->db->delete('activities');
    }

    /**
     * Count the number of rows into the table
     * @return int number of rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function count() {
        $this->db->select('count(*) as number', FALSE);
        $this->db->from('activities');
        $result = $this->db->get();
        return $result->row()->number;
    }
}
