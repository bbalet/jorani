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
        $this->auth->check_is_granted('list_users');
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
    public function view($id) {
        $this->auth->check_is_granted('view_user');
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
    public function edit($id) {
        $this->auth->check_is_granted('edit_user');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Create a new user';
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;

        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');
        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');
        $this->form_validation->set_rules('login', 'Login identifier', 'required');
        $this->form_validation->set_rules('email', 'E-mail', 'required');
        $this->form_validation->set_rules('role', 'role', 'required');

        $data['users_item'] = $this->users_model->get_users($id);
        if (empty($data['users_item'])) {
            show_404();
        }
        $data['title'] = 'User';
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;
        $this->load->model('roles_model');
        $data['roles'] = $this->roles_model->get_roles();
        $data['users'] = $this->users_model->get_users();
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('users/edit', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Delete a given user
     * @param int $id User identifier
     */
    public function delete($id) {
        $this->auth->check_is_granted('delete_user');
        //Test if user exists
        $data['users_item'] = $this->users_model->get_users($id);
        if (empty($data['users_item'])) {
            show_404();
        } else {
            $this->users_model->delete_user($id);
        }
        $this->session->set_flashdata('msg', 'The user has been succesfully deleted');
        redirect('users/index');
    }

    /**
     * Display the form / action Create a new user
     */
    public function create() {
        $this->auth->check_is_granted('create_user');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Create a new user';
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;

        $this->load->model('roles_model');
        $data['roles'] = $this->roles_model->get_roles();
        $data['users'] = $this->users_model->get_users();
        $data['public_key'] = file_get_contents('./assets/keys/public.pem', true);
        
        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');
        $this->form_validation->set_rules('login', 'Login identifier', 'required');
        $this->form_validation->set_rules('email', 'E-mail', 'required');
        //$this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('CipheredValue', 'Password', 'required');
        $this->form_validation->set_rules('role', 'role', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('users/create');
            $this->load->view('templates/footer');
        } else {
            $this->users_model->set_users();
            $this->session->set_flashdata('msg', 'The user has been succesfully created');
            redirect('users/index');
        }
    }

    /**
     * Action : update a user (using data from HTTP form)
     */
    public function update() {
        $this->auth->check_is_granted('update_user');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Create a new user';
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;

        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('users/edit/' . $this->input->post('id'));
            $this->load->view('templates/footer');
        } else {
            $this->users_model->update_users();
            $this->index();
        }
    }

    /**
     * Action: export the list of all users into an Excel file
     */
    public function export() {
        $this->auth->check_is_granted('export_user');
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('List of users');
        $this->excel->getActiveSheet()->setCellValue('A1', 'ID');
        $this->excel->getActiveSheet()->setCellValue('B1', 'Firstname');
        $this->excel->getActiveSheet()->setCellValue('C1', 'Lastname');
        $this->excel->getActiveSheet()->setCellValue('D1', 'E-mail');
        $this->excel->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $users = $this->users_model->get_users();
        $line = 2;
        foreach ($users as $user) {
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $user['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $user['firstname']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $user['lastname']);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $user['email']);
            $line++;
        }

        $filename = 'users.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    //TODO reset password from list
    
    //TODO reset my password as connected user
}
