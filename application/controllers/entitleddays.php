<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * This file is part of lms.
 *
 * lms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with lms.  If not, see <http://www.gnu.org/licenses/>.
 */

class Entitleddays extends CI_Controller {
   
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        //Check if user is connected
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_userdata('last_page', current_url());
            redirect('session/login');
        }
        $this->load->model('entitleddays_model');
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_admin = $this->session->userdata('is_admin');
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
        $this->language = $this->session->userdata('language');
        $this->language_code = $this->session->userdata('language_code');
        $this->load->helper('language');
        $this->lang->load('entitleddays', $this->language);
    }
    
    /**
     * Prepare an array containing information about the current user
     * @return array data to be passed to the view
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function getUserContext()
    {
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;
        $data['is_hr'] = $this->is_hr;
        $data['user_id'] =  $this->user_id;
        $data['language'] = $this->language;
        $data['language_code'] =  $this->language_code;
        return $data;
    }

    /**
     * Display an ajax-based form that list entitled days of a user
     * and allow updating the list by adding or removing one item
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function user($id) {
        $this->auth->check_is_granted('entitleddays_user');
        $data = $this->getUserContext();
        $data['id'] = $id;
        $data['entitleddays'] = $this->entitleddays_model->get_entitleddays_employee($id);
        $this->load->model('types_model');
        $data['types'] = $this->types_model->get_types();
        $this->load->model('users_model');
        $data['name'] = $this->users_model->get_label($id);
        
        $data['title'] = lang('entitleddays_user_index_title');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('entitleddays/user', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display an ajax-based form that list entitled days of a contract
     * and allow updating the list by adding or removing one item
     * @param int $id contract identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function contract($id) {
        $this->auth->check_is_granted('entitleddays_contract');
        $data = $this->getUserContext();
        $data['id'] = $id;
        $data['entitleddays'] = $this->entitleddays_model->get_entitleddays_contract($id);
        $this->load->model('types_model');
        $data['types'] = $this->types_model->get_types();
        $this->load->model('contracts_model');
        $data['name'] = $this->contracts_model->get_label($id);
        
        $data['title'] = lang('entitleddays_contract_index_title');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('entitleddays/contract', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Ajax endpoint : delete an entitled days credit (to an employee)
     * and returns the number of rows affected
     * @param int $id entitled days credit identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function userdelete($id) {
        $this->auth->check_is_granted('entitleddays_user_delete');
        $this->output->set_content_type('text/plain');
        echo $this->entitleddays_model->delete_entitleddays($id);
    }
    
    /**
     * Ajax endpoint : delete an entitled days credit (to a contract)
     * and returns the number of rows affected
     * @param int $id entitled days credit identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function contractdelete($id) {
        $this->auth->check_is_granted('entitleddays_contract_delete');
        $this->output->set_content_type('text/plain');
        echo $this->entitleddays_model->delete_entitleddays($id);
    }
    
    /**
     * Ajax endpoint : insert into the list of entitled days for a given user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function ajax_user() {
        if ($this->auth->is_granted('entitleddays_user') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $user_id = $this->input->post('user_id', TRUE);
            $startdate = $this->input->post('startdate', TRUE);
            $enddate = $this->input->post('enddate', TRUE);
            $days = $this->input->post('days', TRUE);
            $type = $this->input->post('type', TRUE);   
            if (isset($startdate) && isset($enddate) && isset($days) && isset($type) && isset($user_id)) {
                $this->output->set_content_type('text/plain');
                $id = $this->entitleddays_model->insert_entitleddays_employee($user_id, $startdate, $enddate, $days, $type);
                echo $id;
            } else {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            }
        }
    }
    
    /**
     * Ajax endpoint : insert into the list of entitled days for a given contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function ajax_contract() {
        if ($this->auth->is_granted('entitleddays_user') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $contract_id = $this->input->post('contract_id', TRUE);
            $startdate = $this->input->post('startdate', TRUE);
            $enddate = $this->input->post('enddate', TRUE);
            $days = $this->input->post('days', TRUE);
            $type = $this->input->post('type', TRUE);
            $description = $this->input->post('description', TRUE);
            if (isset($startdate) && isset($enddate) && isset($days) && isset($type) && isset($contract_id)) {
                $this->output->set_content_type('text/plain');
                $id = $this->entitleddays_model->insert_entitleddays_contract($contract_id, $startdate, $enddate, $days, $type, $description);
                echo $id;
            } else {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            }
        }
    }
    
    /**
     * Ajax endpoint : Increase or decrease by one an entitled days row 
     * on a contract of an employee (as the both are stored into the same table)
     * id : row identifier into the database
     * operation : "increase" or "decrease" by 1 (the number can be negative).
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function ajax_incdec() {
        if ($this->auth->is_granted('entitleddays_user') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $id = $this->input->post('id', TRUE);
            $operation = $this->input->post('operation', TRUE);   
            if (isset($id) && isset($operation)) {
                $this->output->set_content_type('text/plain');
                switch ($operation) {
                    case  "increase":
                        $id = $this->entitleddays_model->inc_entitleddays($id);
                        break;
                    case "decrease":
                        $id = $this->entitleddays_model->dec_entitleddays($id);
                        break;
                    case "credit":
                        $days = $this->input->post('days', TRUE);
                        $id = $this->entitleddays_model->update_days_entitleddays($id, $days);
                        break;
                    default:
                        $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
                }
                echo $id;
            } else {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            }
        }
    }

}
