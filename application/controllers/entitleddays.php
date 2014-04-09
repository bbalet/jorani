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
        $data['title'] = 'Change entitled days';
        
        $this->form_validation->set_rules('type', 'type', 'required|xss_clean');
        $this->form_validation->set_rules('startdate', 'startdate', 'required|xss_clean');
        $this->form_validation->set_rules('enddate', 'enddate', 'required|xss_clean');
        $this->form_validation->set_rules('days', 'days', 'required|xss_clean');
        
        if ($this->form_validation->run() === FALSE) {
            $data['id'] = $id;
            $this->load->model('entitleddays_model');
            $data['entitleddays'] = $this->entitleddays_model->get_entitleddays_employee($id);
            $this->load->model('types_model');
            $data['types'] = $this->types_model->get_types();
            $this->load->view('entitleddays/user', $data);
        } else {
            $this->load->model('users_model');
            $this->users_model->set_manager();
            $this->session->set_flashdata('msg', 'The manager has been succesfully attached to the employee');
            redirect('hr/employees');
        }
    }
    
}
