<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */

class Api extends CI_Controller {
    
    protected $server; 
    
    /**
     * Default constructor
     * Initializing of OAuth2 server
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        require_once(APPPATH . 'third_party/OAuth2/Autoloader.php');
        OAuth2\Autoloader::register();
        $dsn = 'mysql:dbname=' . $this->db->database . ';host=' . $this->db->hostname;
        $username = $this->db->username;
        $password = $this->db->password;
        $storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
        $this->server = new OAuth2\Server($storage);
        $this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
        $this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
    }

    /**
     * Get a OAuth2 token
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function token() {
        require_once(APPPATH . 'third_party/OAuth2/Server.php');
        $this->server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
    }
    
    /**
     * Get the list of contracts or a specific contract
     * @param int $id Unique identifier of a contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function contracts($id = 0) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('contracts_model');
            $result = $this->contracts_model->get_contracts($id);
            echo json_encode($result);
        }
    }
    
    /**
     * Get the list of entitled days for a given contract
     * @param int $id Unique identifier of an contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function entitleddayscontract($id) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('entitleddays_model');
            $result = $this->entitleddays_model->get_entitleddays_contract($id);
            echo json_encode($result);
        }
    }
    
    /**
     * Add entitled days to a given contract
     * @param int $id Unique identifier of an contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function addentitleddayscontract($id) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('entitleddays_model');
            $startdate = $this->input->post('startdate');
            $enddate = $this->input->post('enddate');
            $days = $this->input->post('days');
            $type = $this->input->post('type');
            $description = $this->input->post('description');
            $result = $this->entitleddays_model->insert_entitleddays_contract($id, $startdate, $enddate, $days, $type, $description);
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
        }
    }
    
    /**
     * Get the list of entitled days for a given employee
     * @param int $id Unique identifier of an employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function entitleddaysemployee($id) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('entitleddays_model');
            $result = $this->entitleddays_model->get_entitleddays_employee($id);
            echo json_encode($result);
        }
    }
    
    /**
     * Add entitled days to a given employee
     * @param int $id Unique identifier of an employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function addentitleddaysemployee($id) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('entitleddays_model');
            $startdate = $this->input->post('startdate');
            $enddate = $this->input->post('enddate');
            $days = $this->input->post('days');
            $type = $this->input->post('type');
            $description = $this->input->post('description');
            $result = $this->entitleddays_model->insert_entitleddays_employee($id, $startdate, $enddate, $days, $type, $description);
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
        }
    }
    
    /**
     * Get the leaves counter of a given user
     * @param int $id Unique identifier of a user
     * @param string $refTmp tmp of the Date of reference (or current date if NULL)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function leavessummary($id, $refTmp = NULL) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('leaves_model');
            if ($refTmp != NULL) {
                $refDate = date("Y-m-d", $refTmp);
            } else {
                $refDate = date("Y-m-d");
            }
            $result = $this->leaves_model->get_user_leaves_summary($id, FALSE, $refDate);
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
        }
    }
    
    /**
     * Get the leaves counter of a given user
     * @param string $startTmp tmp of the Start Date
     * @param string $endTmp tmp of the End Date
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function leaves($startTmp, $endTmp) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('leaves_model');
            $result = $this->leaves_model->all($startTmp, $endTmp);
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
        }
    }
    
    /**
     * Get the list of leave types (useful to get the labels into a cache)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function leavetypes() {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('types_model');
            $result = $this->types_model->get_types();
            echo json_encode($result);
        }
    }
    
    /**
     * Get the list of positions (useful to get the labels into a cache)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function positions() {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('positions_model');
            $result = $this->positions_model->get_positions();
            echo json_encode($result);
        }
    }
    
    /**
     * Get the department details of a given user (label and ID)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function userdepartment($id) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('organization_model');
            $result = $this->organization_model->get_department($id);
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
        }
    }

    /**
     * Get the list of users or a specific user
     * @param int $id Unique identifier of a user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function users($id = 0) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('users_model');
            $result = $this->users_model->get_users($id);
            foreach($result as $k1=>$q) {
              foreach($q as $k2=>$r) {
                if($k2 == 'password') {
                  unset($result[$k1][$k2]);
                }
              }
            }
            echo json_encode($result);
        }
    }

    /**
     * Get the list of extra for a given employee
     * @param int $id Unique identifier of an employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function userextras($id) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('overtime_model');
            $result = $this->overtime_model->get_user_extras($id);
            echo json_encode($result);
        }
    }
    
    /**
     * Get the list of leaves for a given employee
     * @param int $id Unique identifier of an employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function userleaves($id) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('leaves_model');
            $result = $this->leaves_model->get_user_leaves($id);
            echo json_encode($result);
        }
    }
    
    //From this line on, we are in API v2
    
    /**
     * Get the monthly presence stats for a given employee
     * @param int $id Unique identifier of an employee
     * @param int $month Month number [1-12]
     * @param int $year Year number (XXXX)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function monthlypresence($id, $month, $year) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            expires_now();
            $this->load->model('users_model');
            $employee = $this->users_model->get_users($id);
            if (!isset($employee['contract'])) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                $this->load->model('leaves_model');
                $this->load->model('dayoffs_model');
                $start = sprintf('%d-%02d-01', $year, $month);
                $lastDay = date("t", strtotime($start));    //last day of selected month
                $end = sprintf('%d-%02d-%02d', $year, $month, $lastDay);
                $result = new stdClass();
                $linear = $this->leaves_model->linear($id, $month, $year, FALSE, FALSE, TRUE, FALSE);
                $result->leaves = $this->leaves_model->monthly_leaves_duration($linear);
                $result->dayoffs = $this->dayoffs_model->sumdayoffs($employee['contract'], $start, $end);
                $result->total = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $result->start = $start;
                $result->end = $end;
                $result->open = $result->total - $result->dayoffs;
                $result->work = $result->open - $result->leaves;
                echo json_encode($result);
            }
        }
    }

    /**
     * Delete an employee from the database
     * This is not recommended. Consider moving it into an archive entity of your organization
     * @param int $id Unique identifier of an employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteuser($id) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            $this->load->model('users_model');
            $employee = $this->users_model->get_users($id);
            if (count($employee) == 0) {
                $this->output->set_header("HTTP/1.1 404 Not Found");
            } else {
                $this->users_model->delete_user($id);
                echo json_encode("OK");
            }
        }
    }
    
    /**
     * Update an employee
     * Updated fields are passed by POST parameters
     * @param int $id Unique identifier of an employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function updateuser($id) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            $this->load->model('users_model');
            $data = array();
            if ($this->input->post('firstname')!=FALSE) {$data['firstname']= $this->input->post('firstname');}
            if ($this->input->post('lastname')!=FALSE) {$data['lastname']= $this->input->post('lastname');}
            if ($this->input->post('login')!=FALSE) {$data['login']= $this->input->post('login');}
            if ($this->input->post('email')!=FALSE) {$data['email']= $this->input->post('email');}
            if ($this->input->post('password')!=FALSE) {$data['password']= $this->input->post('password');}
            if ($this->input->post('role')!=FALSE) {$data['role']= $this->input->post('role');}
            if ($this->input->post('manager')!=FALSE) {$data['manager']= $this->input->post('manager');}
            if ($this->input->post('organization')!=FALSE) {$data['organization']= $this->input->post('organization');}
            if ($this->input->post('contract')!=FALSE) {$data['contract']= $this->input->post('contract');}
            if ($this->input->post('position')!=FALSE) {$data['position']= $this->input->post('position');}
            if ($this->input->post('datehired')!=FALSE) {$data['datehired']= $this->input->post('datehired');}
            if ($this->input->post('identifier')!=FALSE) {$data['identifier']= $this->input->post('identifier');}
            if ($this->input->post('language')!=FALSE) {$data['language']= $this->input->post('language');}
            if ($this->input->post('timezone')!=FALSE) {$data['timezone']= $this->input->post('timezone');}
            if ($this->input->post('ldap_path')!=FALSE) {$data['ldap_path']= $this->input->post('ldap_path');}
            if ($this->input->post('country')!=FALSE) {$data['country']= $this->input->post('country');}
            if ($this->input->post('calendar')!=FALSE) {$data['calendar']= $this->input->post('calendar');}
            $result = $this->users_model->update_user_api($id, $data);
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
        }
    }
    
    /**
     * Create an employee (fields are passed by POST parameters)
     * Returns the new inserted id
     * @param bool $sendEmail Send an Email to the new employee (FALSE by default)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function createuser($sendEmail = FALSE) {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            $this->load->model('users_model');
            $firstname = $this->input->post('firstname');
            $lastname = $this->input->post('lastname');
            $login = $this->input->post('login');
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $role = $this->input->post('role');
            $manager = $this->input->post('manager');
            $organization = $this->input->post('organization');
            $contract = $this->input->post('contract');
            $position = $this->input->post('position');
            $datehired = $this->input->post('datehired');
            $identifier = $this->input->post('identifier');
            $language = $this->input->post('language');
            $timezone = $this->input->post('timezone');
            $ldap_path = $this->input->post('ldap_path');
            //$active = $this->input->post('active');         //Not used
            $country = $this->input->post('country');   //Not used
            $calendar = $this->input->post('calendar'); //Not used
            
            //Prevent misinterpretation of content
            if ($datehired == FALSE) {$datehired = NULL;}
            if ($organization == FALSE) {$organization = NULL;}
            if ($identifier == FALSE) {$identifier = NULL;}
            if ($timezone == FALSE) {$timezone = NULL;}
            if ($contract == FALSE) {$contract = NULL;}
            if ($position == FALSE) {$position = NULL;}
            if ($manager == FALSE) {$manager = NULL;}
            
            //Set default values
            $this->load->library('polyglot');
            if ($language == FALSE) {$language = $this->polyglot->language2code($this->config->item('language'));}
            
            //Generate a random password if the field is empty
            if ($password == FALSE) {
                $password = $this->users_model->randomPassword(8);
            }
            //Check mandatory fields
            if ($firstname == FALSE || $lastname == FALSE || $login == FALSE || $email == FALSE || $role == FALSE) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
                log_message('error', 'Mandatory fields are missing.');
            } else {
                if ($this->users_model->is_login_available($login)) {
                    $result = $this->users_model->insert_user_api($firstname, $lastname, $login, $email, $password, $role,
                        $manager, $organization, $contract, $position, $datehired, $identifier, $language, $timezone,
                        $ldap_path, TRUE, $country, $calendar);
                    
                    if($sendEmail == TRUE) {
                        //Send an e-mail to the user so as to inform that its account has been created
                        $this->load->library('email');
                        $usr_lang = $this->polyglot->code2language($language);
                        $this->lang->load('users', $usr_lang);
                        $this->lang->load('email', $usr_lang);

                        $this->load->library('parser');
                        $data = array(
                            'Title' => lang('email_user_create_title'),
                            'BaseURL' => base_url(),
                            'Firstname' => $firstname,
                            'Lastname' => $lastname,
                            'Login' => $login,
                            'Password' => $password
                        );
                        $message = $this->parser->parse('emails/' . $language . '/new_user', $data, TRUE);
                        $this->email->set_encoding('quoted-printable');

                        if ($this->config->item('from_mail') != FALSE && $this->config->item('from_name') != FALSE ) {
                            $this->email->from($this->config->item('from_mail'), $this->config->item('from_name'));
                        } else {
                           $this->email->from('do.not@reply.me', 'LMS');
                        }
                        $this->email->to($email);
                        if ($this->config->item('subject_prefix') != FALSE) {
                            $subject = $this->config->item('subject_prefix');
                        } else {
                           $subject = '[Jorani] ';
                        }
                        $this->email->subject($subject . lang('email_user_create_subject'));
                        $this->email->message($message);
                        $this->email->send();
                    }
                    echo json_encode($result);
                } else {
                    $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
                    log_message('error', 'This login is not available.');
                }
            }
        }
    }

    /**
     * Create a leave request (fields are passed by POST parameters).
     * This function doesn't send e-mails and it is used for imposed leaves
     * Returns the new inserted id.
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function createleave() {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            $this->load->model('leaves_model');
            $startdate = $this->input->post('startdate');
            $enddate = $this->input->post('enddate');
            $status = $this->input->post('status');
            $employee = $this->input->post('employee');
            $cause = $this->input->post('cause');
            $startdatetype = $this->input->post('startdatetype');
            $enddatetype = $this->input->post('enddatetype');
            $duration = $this->input->post('duration');
            $type = $this->input->post('type');
            
            $debug1 = var_export($startdate, true);
            $debug2 = var_export($enddate, true);
            $debug3 = var_export($status, true);
            $debug4 = var_export($employee, true);
            $debug5 = var_export($cause, true);
            $debug6 = var_export($startdatetype, true);
            $debug7 = var_export($enddatetype, true);
            $debug8 = var_export($duration, true);
            $debug9 = var_export($type, true);

            //Prevent misinterpretation of content
            if ($cause == FALSE) {$cause = NULL;}
            
            //Check mandatory fields
            if ($startdate == FALSE || $enddate == FALSE || $status === FALSE || $employee === FALSE 
                    || $startdatetype == FALSE || $enddatetype == FALSE || $duration === FALSE || $type === FALSE) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
                log_message('error', 'Mandatory fields are missing.');
            } else {
                    $result = $this->leaves_model->add_leaves_api($startdate, $enddate, $status, $employee, $cause,
                                                                                                $startdatetype, $enddatetype, $duration, $type);
                    echo json_encode($result);
            }
        }
    }

}
