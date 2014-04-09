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

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hr extends CI_Controller {

    /**
     * Connected user fullname
     * @var string $fullname
     */
    private $fullname;
    
    /**
     * Connected user privilege
     * @var bool true if admin, false otherwise  
     */
    private $is_admin;  
    
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
        $this->load->model('leaves_model');
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_admin = $this->session->userdata('is_admin');
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
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
        return $data;
    }

    /**
     * Display the list of all requests submitted to you
     * Status is submitted
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index($filter = 'requested') {
        $this->auth->check_is_granted('list_requests');
        if ($filter == 'all') {
            $showAll = true;
        } else {
            $showAll = false;
        }
        
        $data = $this->getUserContext();
        $data['filter'] = $filter;
        $data['title'] = 'List of requested leaves';
        $data['requests'] = $this->leaves_model->requests($this->user_id, $showAll);
        
        $this->load->model('types_model');
        for ($i = 0; $i < count($data['requests']); ++$i) {
            $data['requests'][$i]['type_label'] = $this->types_model->get_label($data['requests'][$i]['type']);
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('hr/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the list of all employees
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function employees() {
        $this->auth->check_is_granted('list_employees');
        $data = $this->getUserContext();
        $data['title'] = 'List of employees';
        $this->load->model('users_model');
        $data['users'] = $this->users_model->get_employees();
        
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('hr/employees', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display a form that allows updating the contract of a given user
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function contract($id) {
        $this->auth->check_is_granted('employee_contract');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Attach a contract';
        $this->form_validation->set_rules('contract', 'contract', 'required|xss_clean');
        if ($this->form_validation->run() === FALSE) {
            $data['id'] = $id;
            $this->load->model('contracts_model');
            $data['contracts'] = $this->contracts_model->get_contracts();
            $this->load->view('hr/contract', $data);
        } else {
            $this->load->model('users_model');
            $this->users_model->set_contract();
            $this->session->set_flashdata('msg', 'The contract has been succesfully attached to the employee');
            redirect('hr/employees');
        }
    }
    
    /**
     * Display a form that allows updating the manager of a given user
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function manager($id) {
        $this->auth->check_is_granted('employee_manager');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Attach a manager';
        $this->form_validation->set_rules('manager', 'manager', 'required|xss_clean');
        if ($this->form_validation->run() === FALSE) {
            $data['id'] = $id;
            $this->load->model('users_model');
            $data['users'] = $this->users_model->get_users();
            $this->load->view('hr/manager', $data);
        } else {
            $this->load->model('users_model');
            $this->users_model->set_manager();
            $this->session->set_flashdata('msg', 'The manager has been succesfully attached to the employee');
            redirect('hr/employees');
        }
    }
}
