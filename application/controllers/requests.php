<?php
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

class Users extends CI_Controller {

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
     */
    public function __construct() {
        parent::__construct();
        //Check if user is connected
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_userdata('last_page', current_url());
            redirect('session/login');
        }
        $this->load->model('users_model');
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_admin = $this->session->userdata('is_admin');
    }

    /**
     * Display the list of all users
     */
    public function index() {
        $data['users'] = $this->users_model->get_users();
        $data['title'] = 'Users';
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('users/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display details of a given user
     * @param int $id User identifier
     */
    public function accept($id, $comment="") {
        $data['users_item'] = $this->users_model->get_users($id);
        if (empty($data['users_item'])) {
            show_404();
        }
        $data['title'] = 'User';
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('users/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display a for that allows updating a given user
     * @param int $id User identifier
     */
    public function reject($id, $comment="") {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Update a leave request';
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;

        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');


        $data['users_item'] = $this->users_model->get_users($id);
        if (empty($data['users_item'])) {
            show_404();
        }
        $this->load->model('roles_model');
        $data['roles'] = $this->roles_model->get_roles();
        $data['users'] = $this->users_model->get_users();
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('users/edit', $data);
        $this->load->view('templates/footer');
    }
}
