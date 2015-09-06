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

class Entitleddays extends CI_Controller {
   
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->load->model('entitleddays_model');
        $this->lang->load('entitleddays', $this->language);
    }

    /**
     * Display an ajax-based form that list entitled days of a user
     * and allow updating the list by adding or removing one item
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function user($id) {
        $this->auth->check_is_granted('entitleddays_user');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['id'] = $id;
        $data['entitleddays'] = $this->entitleddays_model->get_entitleddays_employee($id);
        $this->load->model('types_model');
        $data['types'] = $this->types_model->get_types();
        $this->load->model('users_model');
        $user = $this->users_model->get_users($id);
        $data['employee_name'] = $this->users_model->get_label($id);
        
        if (!empty ($user['contract'])) {
            $this->load->model('contracts_model');
            $contract = $this->contracts_model->get_contracts($user['contract']);
            $data['contract_name'] = $contract['name'];
            $data['contract_start_month'] = intval(substr($contract['startentdate'], 0, 2));
            $data['contract_start_day'] = intval(substr($contract['startentdate'], 3));
            $data['contract_end_month'] = intval(substr($contract['endentdate'], 0, 2));
            $data['contract_end_day'] = intval(substr($contract['endentdate'], 3));
        } else {
            $data['contract_name'] = '';
        }
        
        $data['title'] = lang('entitleddays_user_index_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_entitleddays_employee');
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
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['id'] = $id;
        $data['entitleddays'] = $this->entitleddays_model->get_entitleddays_contract($id);
        $this->load->model('types_model');
        $data['types'] = $this->types_model->get_types();
        $this->load->model('contracts_model');
        $contract = $this->contracts_model->get_contracts($id);
        $data['contract_name'] = $contract['name'];
        $data['contract_start_month'] = intval(substr($contract['startentdate'], 0, 2));
        $data['contract_start_day'] = intval(substr($contract['startentdate'], 3));
        $data['contract_end_month'] = intval(substr($contract['endentdate'], 0, 2));
        $data['contract_end_day'] = intval(substr($contract['endentdate'], 3));
        
        $data['title'] = lang('entitleddays_contract_index_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_entitleddays_contract');
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
            $description = sanitize($this->input->post('description', TRUE));
            if (isset($startdate) && isset($enddate) && isset($days) && isset($type) && isset($user_id)) {
                $this->output->set_content_type('text/plain');
                $id = $this->entitleddays_model->insert_entitleddays_employee($user_id, $startdate, $enddate, $days, $type, $description);
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
            $description = sanitize($this->input->post('description', TRUE));
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
                $days = $this->input->post('days', TRUE);
                switch ($operation) {
                    case  "increase":
                        $id = $this->entitleddays_model->inc_entitleddays($id, $days);
                        break;
                    case "decrease":
                        $id = $this->entitleddays_model->dec_entitleddays($id, $days);
                        break;
                    case "credit":
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
