<?php
/**
 * This Class contains all the business logic and the persistence layer 
 * for the service accounts (OAuth clients and sessions).
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.6.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This Class contains all the business logic and the persistence layer for 
 * for the service accounts (OAuth clients).
 * Scopes:
 * users
 * entitlements
 * contracts
 * leaves
 * selfservice
 */
class OAuthClients_model extends CI_Model {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        
    }

    /**
     * Get the list of OAuth clients or one client
     * @param string $clientId optional id of a OAuth client
     * @return array record of clients
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getOAuthClients($clientId = '') {
        if ($clientId === '') {
            $query = $this->db->get('oauth_clients');
            return $query->result_array();
        }
        $query = $this->db->get_where('oauth_clients', array('client_id' => $clientId));
        return $query->row_array();
    }
    
    /**
     * Insert a new OAuth client. Data are taken from HTML form.
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function setOAuthClients() {
        $grantTypes = ($this->input->post('grant_types') === FALSE)? NULL: $this->input->post('grant_types');
        $scope = ($this->input->post('scope') === FALSE)? NULL: $this->input->post('scope');
        $userId = ($this->input->post('user_id') === FALSE)? NULL: $this->input->post('user_id');
        $data = array(
            'client_id' => $this->input->post('client_id'),
            'client_secret' => $this->input->post('client_secret'),
            'redirect_uri' => $this->input->post('redirect_uri')
        );
        if ($grantTypes != '') $data['grant_types'] = $grantTypes;
        if ($scope != '') $data['scope'] = $scope;
        if ($userId != '') $data['user_id'] = $userId;
        return $this->db->insert('oauth_clients', $data);
    }
    
    /**
     * Delete a leave type from the database
     * @param string $id identifier of the leave type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteOAuthClients($clientId) {
        $this->db->delete('oauth_clients', array('client_id' => $clientId));
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
        return $this->db->update('oauth_clients', $data);
    }
    
    /**
     * Get the list of OAuth access tokens
     * @return array record of tokens
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getAccessTokens() {
        $query = $this->db->get('oauth_access_tokens');
        return $query->result_array();
    }
    
    /**
     * Purge the table of OAtuh2 tokens
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function purgeAccessTokens() {
        $this->db->truncate('oauth_access_tokens');
    }
}
