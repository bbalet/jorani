<?php
/**
 * This class contains the business logic and manages the persistence of time organisations
 * @copyright  Copyright (c) 2021 Maithyly SIVAPALAN
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class contains the business logic and manages the persistence of time organisations.
 */
class time_organisation_model extends CI_Model {

    /**
     * Default constructor
     * @author Maithyly SIVAPALAN <maithyly.sivapalan@inha.fr>
     */
    public function __construct() {

    }

    /**
     * Get the list of time organisations or one time organisation
     * @param int $id optional id of a time organisation
     * @return array records of time organisations
     * @author Maithyly SIVAPALAN <maithyly.sivapalan@inha.fr>
     */
    public function getTimeOrganisations($id = 0) {
        $this->db->select('time_organisation.*');
        $this->db->select('users.firstname, users.lastname');
        $this->db->from('time_organisation');
        $this->db->join('users', 'time_organisation.employee = users.id');
        if ($id === 0) {
            return $this->db->get()->result_array();
        }
        $this->db->where('time_organisation.id', $id);
        return $this->db->get()->row_array();
    }
    
    /**
     * Get the list of users with and without time organisation
     * @return array records of users with and without time organisation
     * @author Maithyly SIVAPALAN <maithyly.sivapalan@inha.fr>
     */
    public function getTimeOrganisationsForExport() {
        $this->db->select('time_organisation.*');
        $this->db->select('users.id as employee_id, users.firstname, users.lastname');
        $this->db->from('users');
        $this->db->join('time_organisation', 'time_organisation.employee = users.id', 'left outer');
        return $this->db->get()->result_array();
    }

    /**
     * Insert a new time organisation into the database. Inserted data are coming from an HTML form
     * @return int number of affected rows
     * @author Maithyly SIVAPALAN <maithyly.sivapalan@inha.fr>
     */
    public function setTimeOrganisations() {
        $data = array(
            'employee' => $this->input->post('employee'),
            'duration' => str_replace(',', '.', $this->input->post('duration')),
            'day' => $this->input->post('day'),
            'daytype' => $this->input->post('daytype'),
            'recurrence' => $this->input->post('recurrence')
        );
        $timeorganisation = $this->getTimeOrganisationForEmployee($data['employee']);
        if (empty($timeorganisation))
            return $this->db->insert('time_organisation', $data);
        else {
            $this->db->where('employee', $data['employee']);
            return $this->db->update('time_organisation', $data);
        }
    }
    
    /**
     * Insert a new time organisation into the database. Inserted data are coming from an imported file
     * @return int number of affected rows
     * @author Maithyly SIVAPALAN <maithyly.sivapalan@inha.fr>
     */
    public function setTimeOrganisationsFromImport($data) {
        if($data['duration'] > 0 && $data['day'] != NULL && $data['daytype'] != NULL && $data['recurrence'] != NULL){
            $timeorganisation = $this->getTimeOrganisationForEmployee($data['employee']);
            if (empty($timeorganisation))
                return $this->db->insert('time_organisation', $data);
            else {
                $this->db->where('employee', $data['employee']);
                return $this->db->update('time_organisation', $data);
            }
        } 
    }

    /**
     * Update a given time organisation in the database. Update data are coming from an HTML form
     * @param int $id identifier of the time organisation
     * @return int number of affected rows
     * @author Maithyly SIVAPALAN <maithyly.sivapalan@inha.fr>
     */
    public function updateTimeOrganisation($id) {
        $data = array(
            'duration' => str_replace(',', '.', $this->input->post('duration')),
            'day' => $this->input->post('day'),
            'daytype' => $this->input->post('daytype'),
            'recurrence' => $this->input->post('recurrence')
        );
        $this->db->where('id', $id);
        return $this->db->update('time_organisation', $data);
    }   
    
    /**
     * Delete a time organisation from the database
     * @param int $id identifier of the time organisation
     * @author Maithyly SIVAPALAN <maithyly.sivapalan@inha.fr>
     */
    public function deleteTimeOrganisation($id) {
        $this->db->delete('time_organisation', array('id' => $id));
    }
    
    /**
     * Get duration of organised time for the given employee id
     * @param int $id id of an employee
     * @return float duration of organised time 
     * @author Maithyly SIVAPALAN <maithyly.sivapalan@inha.fr>
     */
    public function getTimeOrganisationForEmployee($id) {
        $this->db->select('duration, day, daytype, recurrence');
        $this->db->from('time_organisation');
        $this->db->where('employee', $id);

        return $this->db->get()->row_array();
    }
    
    /**
     * Get list of employees for organised time
     * @return list of employees for organised time
     * @author Maithyly SIVAPALAN <maithyly.sivapalan@inha.fr>
     */
    public function getTimeOrganisationEmployees() {
        $this->db->select('employee');
        $this->db->from('time_organisation');
        
        return $this->db->get()->result();
    }
}
