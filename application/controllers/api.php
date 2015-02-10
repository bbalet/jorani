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
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
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
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
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
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
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
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
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
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
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
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
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
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
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
            if (empty($result)) {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            } else {
                echo json_encode($result);
            }
        }
    }
}
