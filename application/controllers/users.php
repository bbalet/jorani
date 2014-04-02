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

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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
     * @author Benjamin BALET <benjamin.balet@gmail.com>
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
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
    }

    /**
     * Prepare an array containing information about the current user
     * @return array data to be passed to the view
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function getUserContext() {
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;
        $data['is_hr'] = $this->is_hr;
        $data['user_id'] = $this->user_id;
        return $data;
    }

    /**
     * Display the list of all users
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->check_is_granted('list_users');
        $data = $this->getUserContext();
        $data['users'] = $this->users_model->get_users();
        $data['title'] = 'Users';
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('users/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display details of a given user
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function view($id) {
        $this->auth->check_is_granted('view_user');
        $data = $this->getUserContext();
        $data['user'] = $this->users_model->get_users($id);
        if (empty($data['user'])) {
            show_404();
        }
        $data['title'] = 'User';
        $this->load->model('roles_model');
        $data['roles'] = $this->roles_model->get_roles();
        $data['users'] = $this->users_model->get_users();
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('users/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display a for that allows updating a given user
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($id) {
        $this->auth->check_is_granted('edit_user');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Create a new user';

        $this->form_validation->set_rules('firstname', 'Firstname', 'required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required|xss_clean');
        $this->form_validation->set_rules('firstname', 'Firstname', 'required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required|xss_clean');
        $this->form_validation->set_rules('login', 'Login identifier', 'required|callback_login_check|xss_clean');
        $this->form_validation->set_rules('email', 'E-mail', 'required|xss_clean');
        $this->form_validation->set_rules('role', 'role', 'required|xss_clean');
        $this->form_validation->set_rules('manager', 'manager', 'required|xss_clean');

        $data['users_item'] = $this->users_model->get_users($id);
        if (empty($data['users_item'])) {
            show_404();
        }

        if ($this->form_validation->run() === FALSE) {
            $this->load->model('roles_model');
            $data['roles'] = $this->roles_model->get_roles();
            $data['users'] = $this->users_model->get_users();
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('users/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $this->users_model->update_users();
            $this->session->set_flashdata('msg', 'The user has been succesfully updated');
            redirect('users/index');
        }
    }

    /**
     * Delete a given user
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) {
        log_message('debug', '{controllers/users/delete} Entering method with id=' . $id);
        $this->auth->check_is_granted('delete_user');
        //Test if user exists
        $data['users_item'] = $this->users_model->get_users($id);
        if (empty($data['users_item'])) {
            log_message('debug', '{controllers/users/delete} user not found');
            show_404();
        } else {
            $this->users_model->delete_user($id);
        }
        log_message('info', 'User #' . $id . ' has been deleted by user #' . $this->session->userdata('id'));
        $this->session->set_flashdata('msg', 'The user has been succesfully deleted');
        log_message('debug', '{controllers/users/delete} Leaving method (before redirect)');
        redirect('users/index');
    }

    /**
     * Reset the password of a user
     * Can be accessed by the user itself or by admin
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function reset($id) {
        log_message('debug', '{controllers/users/reset} Entering method with id=' . $id);
        $this->auth->check_is_granted('change_password', $id);

        //Test if user exists
        $data['users_item'] = $this->users_model->get_users($id);
        if (empty($data['users_item'])) {
            log_message('debug', '{controllers/users/reset} user not found');
            show_404();
        } else {
            $data['target_user_id'] = $id;
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('CipheredValue', 'Password', 'required');
            if ($this->form_validation->run() === FALSE) {
                log_message('debug', '{controllers/users/reset} Initial form call or bad values');
                $data['public_key'] = file_get_contents('./assets/keys/public.pem', true);
                $this->load->view('templates/header', $data);
                $this->load->view('users/reset', $data);
            } else {
                $this->users_model->reset_password($id, $this->input->post('CipheredValue'));
                log_message('info', 'Password of user #' . $id . ' has been modified by user #' . $this->session->userdata('id'));
                $this->session->set_flashdata('msg', 'The password has been succesfully changed');
                log_message('debug', '{controllers/users/reset} Leaving method (before redirect)');
                if ($this->is_admin) {
                    redirect('users');
                }
                else {
                    redirect('home');
                }
            }
        }
    }

    /**
     * Display the form / action Create a new user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        $this->auth->check_is_granted('create_user');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Create a new user';

        $this->load->model('roles_model');
        $data['roles'] = $this->roles_model->get_roles();
        $data['users'] = $this->users_model->get_users();
        $data['public_key'] = file_get_contents('./assets/keys/public.pem', true);

        $this->form_validation->set_rules('firstname', 'Firstname', 'required|xss_clean');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required|xss_clean');
        $this->form_validation->set_rules('login', 'Login identifier', 'required|callback_login_check|xss_clean');
        $this->form_validation->set_rules('email', 'E-mail', 'required|xss_clean');
        $this->form_validation->set_rules('CipheredValue', 'Password', 'required');
        $this->form_validation->set_rules('role[]', 'Role', 'required|xss_clean');
        $this->form_validation->set_rules('manager', 'Manager', 'required|xss_clean');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('users/create', $data);
            $this->load->view('templates/footer');
        } else {
            $this->users_model->set_users();
            log_message('info', 'User ' . $this->input->post('login') . ' has been created by user #' . $this->session->userdata('id'));
            $this->session->set_flashdata('msg', 'The user has been succesfully created');
            redirect('users/index');
        }
    }
   
    /**
     * Form validation callback : prevent from lgon duplication
     * @param type $login
     * @return boolean
     */
    public function login_check($login) {
        if (!$this->users_model->is_login_available($login)) {
            $this->form_validation->set_message('login_check', 'Username already exists.');
            return false;
        } else {
            return true;
        }
    }

    /**
     * Action : update a user (using data from HTTP form)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function update() {
        $this->auth->check_is_granted('update_user');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Create a new user';

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
     * @author Benjamin BALET <benjamin.balet@gmail.com>
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

    //TODO import a list of users from CSV or Excel
    //TODO check duplicated login on creation
}
