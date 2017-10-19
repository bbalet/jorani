<?php
/**
 * This controller is the entry point for the REST API used by mobile and HTML5
 * Clients. They use CORS requests. Each call to end points uses BasicAuth 
 * except the preflight exchange. So it should be used with a TLS connection
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.3.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class implements a REST API
 */
class Rest extends CI_Controller {
    
    /**
     * @var int Indentifier of the connected user 
     */
    protected $userId;
    
    /**
     * Default constructor
     * Check user credentials
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        if ($this->input->method(FALSE) != 'OPTIONS') {
            if (empty($this->input->server('PHP_AUTH_USER'))) {
                $this->notAuthenticated();
            } else {
                $this->load->model('users_model');
                $username = $this->input->server('PHP_AUTH_USER');
                $password = $this->input->server('PHP_AUTH_PW');
                $loggedin = FALSE;
                if ($this->config->item('ldap_enabled') === TRUE) {
                    if ($password != "") { //Bind to MS-AD with blank password might return OK
                        $ldap = ldap_connect($this->config->item('ldap_host'), $this->config->item('ldap_port'));
                        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
                        set_error_handler(function() { /* ignore errors */
                        });

                        $basedn = "";
                        if (($this->config->item('ldap_search_enabled')) === TRUE) {
                            $bind = ldap_bind($ldap, $this->config->item('ldap_search_user'), $this->config->item('ldap_search_password'));
                            $resultSet = ldap_search($ldap, $this->config->item('ldap_basedn'), sprintf($this->config->item('ldap_search_pattern'), $username));
                            $userEntry = ldap_first_entry($ldap, $resultSet);
                            $basedn = ldap_get_dn($ldap, $userEntry);
                        } else {
                            //Priority is given to the base DN defined into the database, then try with the template
                            $basedn = $this->users_model->getBaseDN($username);
                            if ($basedn == "") {//can return NULL
                                $basedn = sprintf($this->config->item('ldap_basedn'), $username);
                            }
                        }

                        $bind = ldap_bind($ldap, $basedn, $password);
                        restore_error_handler();
                        if ($bind) {
                            $loggedin = $this->users_model->checkCredentialsLDAP($username);
                        } else {
                            //Attempt to login the user with the password stored into DB, provided this password is not emptye
                            if ($password != "") {
                                $loggedin = $this->users_model->checkCredentials($username, $password);
                            }
                        }
                        ldap_close($ldap);
                    }
                } else {
                    $loggedin = $this->users_model->checkCredentials($username, $password);
                }

                if ($loggedin == FALSE) {
                    $this->notAuthenticated();
                } else {
                    
                }
            }
        }
    }

    /**
     * Preflight check for CORS requests
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function options() {
        
    }
    
    /**
     * Terminate lifecycle of the web request if the user can't be authenticated
     */
    private function notAuthenticated() {
        header('HTTP/1.0 401 Unauthorized');
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Basic realm="Jorani Rest API"');
        die();
    }

    /**
     * Get the list of leave requests of the connected employee
     * @param int $id Unique identifier of a leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function leaves($id = 0) {
        $data = NULL;
        $this->load->model('leaves_model');
        if ($this->config->item('enable_history') == TRUE){
          $data = $this->leaves_model->getLeavesOfEmployeeWithHistory($this->session->userdata('id'));
        }else{
          $data = $this->leaves_model->getLeavesOfEmployee($this->session->userdata('id'));
        }
        //See:http://php.net/manual/en/function.xmlrpc-encode.php
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    
    
}
