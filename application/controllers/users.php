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
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        //$this->output->enable_profiler($this->config->item('enable_profiling'));
        //Check if user is connected
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_userdata('last_page', current_url());
            redirect('session/login');
        }
        $this->load->model('users_model');
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
        $this->language = $this->session->userdata('language');
        $this->language_code = $this->session->userdata('language_code');
        $this->load->helper('language');
        $this->lang->load('users', $this->language);
    }

    /**
     * Prepare an array containing information about the current user
     * @return array data to be passed to the view
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function getUserContext() {
        $data['fullname'] = $this->fullname;
        $data['is_hr'] = $this->is_hr;
        $data['user_id'] = $this->user_id;
        $data['language'] = $this->language;
        $data['language_code'] =  $this->language_code;
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
        $data['title'] = lang('users_index_title');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('users/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display the modal pop-up content of the list of employees
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function employees() {
        $this->auth->check_is_granted('employees_list');
        $data = $this->getUserContext();
        $data['employees'] = $this->users_model->get_all_employees();
        $data['title'] = lang('employees_index_title');
        $this->load->view('users/employees', $data);
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
        $data['title'] = lang('users_view_html_title');
        $this->load->model('roles_model');
        $this->load->model('positions_model');
        $this->load->model('contracts_model');
        $this->load->model('organization_model');
        $data['roles'] = $this->roles_model->get_roles();
        $data['manager_label'] = $this->users_model->get_label($data['user']['manager']);
        $data['contract_label'] = $this->contracts_model->get_label($data['user']['contract']);
        $data['position_label'] = $this->positions_model->get_label($data['user']['position']);
        $data['organization_label'] = $this->organization_model->get_label($data['user']['organization']);
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
        $data['title'] = lang('users_edit_html_title');
        
        $this->form_validation->set_rules('firstname', lang('users_edit_field_firstname'), 'required|xss_clean');
        $this->form_validation->set_rules('lastname', lang('users_edit_field_lastname'), 'required|xss_clean');
        $this->form_validation->set_rules('login', lang('users_edit_field_login'), 'required|xss_clean');
        $this->form_validation->set_rules('email', lang('users_edit_field_email'), 'required|xss_clean');
        $this->form_validation->set_rules('role', lang('users_edit_field_role'), 'required|xss_clean');
        $this->form_validation->set_rules('manager', lang('users_edit_field_manager'), 'required|xss_clean');
        $this->form_validation->set_rules('contract', lang('users_edit_field_contract'), 'xss_clean');
        $this->form_validation->set_rules('entity', lang('users_edit_field_entity'), 'xss_clean');
        $this->form_validation->set_rules('position', lang('users_edit_field_position'), 'xss_clean');
        $this->form_validation->set_rules('datehired', lang('users_edit_field_hired'), 'xss_clean');
        $this->form_validation->set_rules('identifier', lang('users_edit_field_identifier'), 'xss_clean');
        $this->form_validation->set_rules('language', lang('users_edit_field_language'), 'xss_clean');
        
        $data['users_item'] = $this->users_model->get_users($id);
        if (empty($data['users_item'])) {
            show_404();
        }

        if ($this->form_validation->run() === FALSE) {
            $this->load->model('roles_model');
            $this->load->model('positions_model');
            $this->load->model('organization_model');
            $this->load->model('contracts_model');
            $data['contracts'] = $this->contracts_model->get_contracts();
            $data['manager_label'] = $this->users_model->get_label($data['users_item']['manager']);
            $data['position_label'] = $this->positions_model->get_label($data['users_item']['position']);
            $data['organization_label'] = $this->organization_model->get_label($data['users_item']['organization']);
            $data['roles'] = $this->roles_model->get_roles();
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('users/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $this->users_model->update_users();
            $this->session->set_flashdata('msg', lang('users_edit_flash_msg_success'));
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('users');
            }
        }
    }

    /**
     * Delete a given user
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) { 
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
        $this->session->set_flashdata('msg', lang('users_delete_flash_msg_success'));
        redirect('users');
    }

    /**
     * Reset the password of a user
     * Can be accessed by the user itself or by admin
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function reset($id) {
        $this->auth->check_is_granted('change_password', $id);

        //Test if user exists
        $data['users_item'] = $this->users_model->get_users($id);
        if (empty($data['users_item'])) {
            log_message('debug', '{controllers/users/reset} user not found');
            show_404();
        } else {
            $data = $this->getUserContext();
            $data['target_user_id'] = $id;
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('CipheredValue', 'Password', 'required');
            if ($this->form_validation->run() === FALSE) {
                $data['public_key'] = file_get_contents('./assets/keys/public.pem', true);
                $this->load->view('templates/header', $data);
                $this->load->view('users/reset', $data);
            } else {
                $this->users_model->reset_password($id, $this->input->post('CipheredValue'));
                log_message('info', 'Password of user #' . $id . ' has been modified by user #' . $this->session->userdata('id'));
                
                //Send an e-mail to the user so as to inform that its password has been changed
                $this->load->model('settings_model');
                $user = $this->users_model->get_users($id);
                $this->load->library('email');
                $config = $this->settings_model->get_mail_config();            
                $this->email->initialize($config);
                
                $this->load->library('language');
                $usr_lang = $this->language->code2language($user['language']);
                $this->lang->load('email', $usr_lang);

                $this->load->library('parser');
                $data = array(
                    'Title' => lang('email_password_reset_title'),
                    'Firstname' => $user['firstname'],
                    'Lastname' => $user['lastname']
                );
                $message = $this->parser->parse('emails/' . $user['language'] . '/password_reset', $data, TRUE);
                //$message = iconv(mb_detect_encoding($message, mb_detect_order(), true), "UTF-8", $message);

                $this->email->from('do.not@reply.me', 'LMS');
                $this->email->to($user['email']);
                $this->email->subject(lang('email_password_reset_subject'));
                $this->email->message($message);
                $this->email->send();
                
                //Inform back the user by flash message
                $this->session->set_flashdata('msg', lang('users_reset_flash_msg_success'));
                if ($this->is_hr) {
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
        $data['title'] = lang('users_create_title');

        $this->load->model('roles_model');
        $data['roles'] = $this->roles_model->get_roles();
        $this->load->model('contracts_model');
        $data['contracts'] = $this->contracts_model->get_contracts();
        $data['public_key'] = file_get_contents('./assets/keys/public.pem', true);

        $this->form_validation->set_rules('firstname', lang('users_create_field_firstname'), 'required|xss_clean');
        $this->form_validation->set_rules('lastname', lang('users_create_field_lastname'), 'required|xss_clean');
        $this->form_validation->set_rules('login', lang('users_create_field_login'), 'required|callback_login_check|xss_clean');
        $this->form_validation->set_rules('email', lang('users_create_field_email'), 'required|xss_clean');
        $this->form_validation->set_rules('CipheredValue', lang('users_create_field_password'), 'required');
        $this->form_validation->set_rules('role[]', lang('users_create_field_role'), 'required|xss_clean');
        $this->form_validation->set_rules('manager', lang('users_create_field_manager'), 'required|xss_clean');
        $this->form_validation->set_rules('contract', lang('users_create_field_contract'), 'xss_clean');
        $this->form_validation->set_rules('position', lang('users_create_field_position'), 'xss_clean');
        $this->form_validation->set_rules('entity', lang('users_create_field_entity'), 'xss_clean');
        $this->form_validation->set_rules('datehired', lang('users_create_field_hired'), 'xss_clean');
        $this->form_validation->set_rules('identifier', lang('users_create_field_identifier'), 'xss_clean');
        $this->form_validation->set_rules('language', lang('users_create_field_language'), 'xss_clean');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('users/create', $data);
            $this->load->view('templates/footer');
        } else {
            $password = $this->users_model->set_users();
            log_message('info', 'User ' . $this->input->post('login') . ' has been created by user #' . $this->session->userdata('id'));
            
            //Send an e-mail to the user so as to inform that its account has been created
            $this->load->model('settings_model');
            $this->load->library('email');
            $config = $this->settings_model->get_mail_config();            
            $this->email->initialize($config);
            
            /*
            $lang['email_user_create_subject'] = '[LMS] Votre compte a été créé';
            $lang['email_user_create_title'] = 'Votre compte a été créé';
            */
            
            $this->load->library('parser');
            $data = array(
                'Title' => 'Your account has been created',
                'BaseURL' => base_url(),
                'Firstname' => $this->input->post('firstname'),
                'Lastname' => $this->input->post('lastname'),
                'Login' => $this->input->post('login'),
                'Password' => $password
            );
            $message = $this->parser->parse('emails/new_user', $data, TRUE);

            $this->email->from('do.not@reply.me', 'LMS');
            $this->email->to($this->input->post('email'));
            $this->email->subject('[LMS] Your account has been created');
            $this->email->message($message);
            $this->email->send();
            
            $this->session->set_flashdata('msg', lang('users_create_flash_msg_success'));
            redirect('users');
        }
    }
   
    /**
     * Form validation callback : prevent from login duplication
     * @param type $login
     * @return boolean true if the field is valid, false otherwise
     */
    public function login_check($login) {
        if (!$this->users_model->is_login_available($login)) {
            $this->form_validation->set_message('login_check', lang('users_create_login_check'));
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Ajax endpoint : check login duplication
     */
    public function check_login() {
        header("Content-Type: text/plain");
        if ($this->users_model->is_login_available($this->input->post('login'))) {
            echo 'true';
        } else {
            echo 'false';
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
        $this->excel->getActiveSheet()->setTitle(lang('users_export_title'));
        $this->excel->getActiveSheet()->setCellValue('A1', lang('users_export_thead_id'));
        $this->excel->getActiveSheet()->setCellValue('B1', lang('users_export_thead_firstname'));
        $this->excel->getActiveSheet()->setCellValue('C1', lang('users_export_thead_lastname'));
        $this->excel->getActiveSheet()->setCellValue('D1', lang('users_export_thead_email'));
        $this->excel->getActiveSheet()->setCellValue('E1', lang('users_export_thead_manager'));
        
        $this->excel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $users = $this->users_model->get_users();
        $line = 2;
        foreach ($users as $user) {
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $user['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $user['firstname']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $user['lastname']);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $user['email']);
            $this->excel->getActiveSheet()->setCellValue('E' . $line, $user['manager']);
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
    /**
     * Action: export the list of all users into an Excel file
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function import() {
        $this->auth->check_is_granted('import_user');
        $this->load->view('users/import');
        
        //A : Firstname
        //B : Lastname
        //C : E-mail
        //D : Role (2 for user if ommitted)
        //E : optional login (first letter of firstname + lastname if ommitted
        //F : manager optional (cell into this Worksheet, "self" or ID in database) : cell must preceed)
        //G : optional password (automatically generated if ommitted)
        //H : optional Identifier into the database if set, will update user
        
        //Filename <= uniqid($prefix);
        
        /*$config['upload_path'] = 'temp/';
        $config['allowed_types'] = 'xls|csv|xlxs';
        $config['max_size'] = '100';
        $config['max_width'] = '1024';
        $config['max_height'] = '768';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload()) {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('upload_form', $error);
        } else {
            $this->load->library('excel');
            $data = array('upload_data' => $this->upload->data());

            //$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
            $objPHPExcel = PHPExcel_IOFactory::load($path);
            $worksheet->getHighestRow();
            
            //First line contains an header with the names of columns
            for ($row = 2; $row <= $highestRow; ++ $row) {
                
            }
            //$cell = $worksheet->getCellByColumnAndRow($col, $row);
            $val = $cell->getValue();
            $line++;

            
            //$this->load->view('upload_success', $data);
        }*/
        

    }    
}
