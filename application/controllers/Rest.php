<?php
/**
 * This controller is the entry point for the REST API used by mobile and HTML5
 * Clients. They use CORS requests. Each call to end points uses BasicAuth 
 * except the preflight exchange. So it should be used with a TLS connection
 * @copyright  Copyright (c) 2014-2018 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.3.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

require_once FCPATH . "vendor/autoload.php";

/**
 * This class implements a REST API
 */
class Rest extends CI_Controller {
    
    /**
     * @var int Indentifier of the connected user 
     */
    protected $userId;
    
    /**
     * @var string Requested language (english language name)
     */
    protected $language;
    
    /**
     * Default constructor
     * Check user credentials
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        log_message('debug', 'Current URI = ' . $this->uri->uri_string());
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding, Accept-Language");
        if ($this->input->method(FALSE) != 'OPTIONS') {
            if (empty($this->input->server('PHP_AUTH_USER'))) {
                log_message('debug', 'Authenticate: PHP_AUTH_USER is missing (webserver misconfiguration or BasicAuth wasn\'t sent)');
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
                    } else {
                        log_message('debug', 'Authenticate: Empty password provided for LDAP Backend');
                    }
                } else {
                    $loggedin = $this->users_model->checkCredentials($username, $password);
                    $this->userId = $this->session->userdata('id');
                    log_message('debug', 'Authenticate: Welcome dear user #' . $this->userId);
                }

                if ($loggedin == FALSE) {
                    $this->notAuthenticated();
                } else {
                    $this->load->library('polyglot');
                    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                        log_message('debug', 'Client sent us acceptable language codes: ' . $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                        $negotiator = new \Negotiation\LanguageNegotiator();
                        $availableLanguages = explode(",", $this->config->item('languages'));
                        log_message('debug', 'Jorani currently support one of these lang codes: ' . $this->config->item('languages'));
                        $bestLanguage = $negotiator->getBest(
                                $_SERVER['HTTP_ACCEPT_LANGUAGE'],
                                $availableLanguages);
                        $langCode = $bestLanguage->getType();
                        if (!in_array($langCode, $availableLanguages)) {
                            log_message('debug', 'Sorry, language code of client is not supported: ' . $langCode);
                            $langCode = $this->polyglot->language2code($this->config->item('language'));
                        }
                        $this->language = $this->polyglot->code2language($langCode);
                    } else {
                        log_message('debug', 'Client did not send its favourite languages.');
                        $this->language = $this->config->item('language');
                    }
                    log_message('debug', 'We\'ll use ' . $this->language);
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
        log_message('debug', 'Authenticate: Send back HTTP 401 Error');
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
        log_message('debug', '++leaves');
        $this->load->model('leaves_model');
        $leaves = $this->leaves_model->getLeavesOfEmployee($this->userId);
        //Format and translate according to Accept-Language Header
        $langRest = new CI_Lang();
        $langRest->load('global', $this->language);
        foreach ($leaves as &$leave) {
            $leave['startdatetype'] = $langRest->line($leave['startdatetype']);
            $leave['enddatetype'] = $langRest->line($leave['enddatetype']);
            $leave['status_name'] = $langRest->line($leave['status_name']);
            $date = new DateTime($leave['startdate']);
            $leave['startdate'] = $date->format($langRest->line('global_date_format'));
            $date = new DateTime($leave['enddate']);
            $leave['enddate'] = $date->format($langRest->line('global_date_format'));
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($leaves));
        log_message('debug', '--leaves');
    }
    
    /**
     * Get the the properties of the connected employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getPropertiesOfConnectedUser() {
        log_message('debug', '++getPropertiesOfConnectedUser');
        $data = new \stdClass();
        $data->userID = $this->userId;
        $data->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $data->isManager = $this->session->userdata('is_manager');
        $data->isAdmin = $this->session->userdata('is_admin');
        $data->isHR = $this->session->userdata('is_hr');
        $data->manager = $this->session->userdata('manager');
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
        log_message('debug', '--getPropertiesOfConnectedUser');
    }
    
}
