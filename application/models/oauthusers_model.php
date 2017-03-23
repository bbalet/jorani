<?php
/**
 * This Class contains all the business logic and the persistence layer 
 * for the service accounts (OAuth users).
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This Class contains all the business logic and the persistence layer for 
 * for the service accounts (OAuth users).
 */
class OAuthUsers_model extends CI_Model {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        
    }
    
    //TODO: set all permissions

    /**
     * Get the list of OAuth users or one user
     * @param string $username optional id of a OAuth user
     * @return array record of users
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getOAuthUsers($username = '') {
        if ($id === '') {
            $query = $this->db->get('oauth_users');
            return $query->result_array();
        }
        $query = $this->db->get_where('oauth_users', array('username' => $username));
        return $query->row_array();
    }
    
    /**
     * Insert a new leave type. Data are taken from HTML form.
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function setOAuthUsers() {
        $data = array(
            'username' => $this->input->post('username'),
            'username' => $this->input->post('username'),
            'username' => $this->input->post('username'),
            'username' => $this->input->post('username')
        );
        return $this->db->insert('oauth_users', $data);
    }
    
    /**
     * Delete a leave type from the database
     * @param int $id identifier of the leave type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteOAuthUsers($username) {
        $this->db->delete('oauth_users', array('username' => $username));
    }
    
    /**
     * Update a given leave type in the database.
     * @param int $id identifier of the leave type
     * @param string $name name of the type
     * @param bool $deduct Deduct days off
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function updateOAuthUsers($id, $name, $deduct) {
        $data = array(
            'name' => $name,
            'deduct_days_off' => $deduct
        );
        $this->db->where('id', $id);
        return $this->db->update('oauth_users', $data);
    }
}
