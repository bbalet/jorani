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
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
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
     * Display a form that list entitled days of a user
     * and allow updating the list by adding or removing one item
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function user($id) {
        $this->auth->check_is_granted('entitleddays_user');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Add entitled days';
        
        $this->form_validation->set_rules('startdate', 'startdate', 'required|xss_clean');
        $this->form_validation->set_rules('enddate', 'enddate', 'required|xss_clean');
        $this->form_validation->set_rules('days', 'days', 'required|xss_clean');
        $this->form_validation->set_rules('type', 'type', 'required|xss_clean');        
        
        if ($this->form_validation->run() === FALSE) {
            $data['id'] = $id;
            $data['entitleddays'] = $this->entitleddays_model->get_entitleddays_employee($id);
            $this->load->model('types_model');
            $data['types'] = $this->types_model->get_types();
            $this->load->view('entitleddays/user', $data);
        } else {
            $this->entitleddays_model->set_entitleddays_employee();
            $this->session->set_flashdata('msg', 'The entitled days has been succesfully added for the employee');
            redirect('hr/employees');
        }
    }
    
    /**
     * Display a form that list entitled days of a contract
     * and allow updating the list by adding or removing one item
     * @param int $id contract identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function contract($id) {
        $this->auth->check_is_granted('entitleddays_contract');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Add entitled days';
        
        $this->form_validation->set_rules('startdate', 'startdate', 'required|xss_clean');
        $this->form_validation->set_rules('enddate', 'enddate', 'required|xss_clean');
        $this->form_validation->set_rules('days', 'days', 'required|xss_clean');
        $this->form_validation->set_rules('type', 'type', 'required|xss_clean');        
        
        if ($this->form_validation->run() === FALSE) {
            $data['id'] = $id;
            $data['entitleddays'] = $this->entitleddays_model->get_entitleddays_contract($id);
            $this->load->model('types_model');
            $data['types'] = $this->types_model->get_types();
            $this->load->view('entitleddays/contract', $data);
        } else {
            $this->entitleddays_model->set_entitleddays_contract();
            $this->session->set_flashdata('msg', 'The entitled days has been succesfully added for the contract');
            redirect('contracts');
        }
    }
    
    /**
     * Action : delete an entitled days credit (to an employee)
     * @param int $id entitled days credit identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function userdelete($id) {
        $this->auth->check_is_granted('entitleddays_user_delete');
        $this->entitleddays_model->delete_entitleddays($id);
        $this->session->set_flashdata('msg', 'The entitled days has been succesfully deleted for the employee');
        redirect('hr/employees');
    }
    
    /**
     * Action : delete an entitled days credit (to a contract)
     * @param int $id entitled days credit identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function contractdelete($id) {
        $this->auth->check_is_granted('entitleddays_contract_delete');
        $this->entitleddays_model->delete_entitleddays($id);
        $this->session->set_flashdata('msg', 'The entitled days has been succesfully deleted for the contract');
        redirect('contracts');
    }
}
