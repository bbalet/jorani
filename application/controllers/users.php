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

class Users extends CI_Controller {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->load->model('users_model');
        $this->lang->load('users', $this->language);
    }

    /**
     * Display the list of all users
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->check_is_granted('list_users');
        expires_now();
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['users'] = $this->users_model->get_users();
        $data['title'] = lang('users_index_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_list_users');
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, true);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('users/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Set a user as active (TRUE) or inactive (FALSE)
     * @param int $id User identifier
     * @param bool $active active (TRUE) or inactive (FALSE)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function active($id, $active) {
        $this->auth->check_is_granted('list_users');
        $data['users'] = $this->users_model->set_active($id, $active);
        $this->session->set_flashdata('msg', lang('users_edit_flash_msg_success'));
        redirect('users');
    }
    
    /**
     * Enable a user 
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function enable($id) {
        $this->active($id, TRUE);
    }
    
    /**
     * Disable a user 
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function disable($id) {
        $this->active($id, FALSE);
    }

    /**
     * Display the modal pop-up content of the list of employees
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function employees() {
        $this->auth->check_is_granted('employees_list');
        expires_now();
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['employees'] = $this->users_model->get_all_employees();
        $data['title'] = lang('employees_index_title');
        $this->load->view('users/employees', $data);
    }

    /**
     * Display details of the connected user (contract, line manager, etc.)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function myprofile() {
        $this->auth->check_is_granted('view_myprofile');
        $data = getUserContext($this);
        $data['user'] = $this->users_model->get_users($this->user_id);
        if (empty($data['user'])) {
            show_404();
        }
        expires_now();
        $data['title'] = lang('users_myprofile_html_title');
        $this->load->model('roles_model');
        $this->load->model('positions_model');
        $this->load->model('contracts_model');
        $this->load->model('organization_model');
        $data['roles'] = $this->roles_model->get_roles();
        $data['manager_label'] = $this->users_model->get_label($data['user']['manager']);
        $data['contract_id'] = intval($data['user']['contract']);
        $data['contract_label'] = $this->contracts_model->get_label($data['user']['contract']);
        $data['position_label'] = $this->positions_model->get_label($data['user']['position']);
        $data['organization_label'] = $this->organization_model->get_label($data['user']['organization']);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('users/myprofile', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display a for that allows updating a given user
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($id) {
        $this->auth->check_is_granted('edit_user');
        expires_now();
        $data = getUserContext($this);
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('polyglot');
        $data['title'] = lang('users_edit_html_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_create_user');
        
        $this->form_validation->set_rules('firstname', lang('users_edit_field_firstname'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('lastname', lang('users_edit_field_lastname'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('login', lang('users_edit_field_login'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('email', lang('users_edit_field_email'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('role', lang('users_edit_field_role'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('manager', lang('users_edit_field_manager'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('contract', lang('users_edit_field_contract'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('entity', lang('users_edit_field_entity'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('position', lang('users_edit_field_position'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('datehired', lang('users_edit_field_hired'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('identifier', lang('users_edit_field_identifier'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('language', lang('users_edit_field_language'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('timezone', lang('users_edit_field_timezone'), 'xss_clean|strip_tags');
        if ($this->config->item('ldap_basedn_db')) $this->form_validation->set_rules('ldap_path', lang('users_edit_field_ldap_path'), 'xss_clean|strip_tags');
        
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
            $data = getUserContext($this);
            $data['target_user_id'] = $id;
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('CipheredValue', 'Password', 'required');
            if ($this->form_validation->run() === FALSE) {
                $data['public_key'] = file_get_contents('./assets/keys/public.pem', true);
                $this->load->view('users/reset', $data);
            } else {
                $this->users_model->reset_password($id, $this->input->post('CipheredValue'));
                log_message('info', 'Password of user #' . $id . ' has been modified by user #' . $this->session->userdata('id'));
                
                //Send an e-mail to the user so as to inform that its password has been changed
                $user = $this->users_model->get_users($id);
                $this->load->library('email');
                $this->load->library('polyglot');
                $usr_lang = $this->polyglot->code2language($user['language']);
                //We need to instance an different object as the languages of connected user may differ from the UI lang
                $lang_mail = new CI_Lang();
                $lang_mail->load('email', $usr_lang);

                $this->load->library('parser');
                $data = array(
                    'Title' => $lang_mail->line('email_password_reset_title'),
                    'Firstname' => $user['firstname'],
                    'Lastname' => $user['lastname']
                );
                $message = $this->parser->parse('emails/' . $user['language'] . '/password_reset', $data, TRUE);
                $this->email->set_encoding('quoted-printable');
                
                if ($this->config->item('from_mail') != FALSE && $this->config->item('from_name') != FALSE ) {
                    $this->email->from($this->config->item('from_mail'), $this->config->item('from_name'));
                } else {
                    $this->email->from('do.not@reply.me', 'LMS');
                }
                $this->email->to($user['email']);
                if ($this->config->item('subject_prefix') != FALSE) {
                    $subject = $this->config->item('subject_prefix');
                } else {
                   $subject = '[Jorani] ';
                }
                $this->email->subject($subject . $lang_mail->line('email_password_reset_subject'));
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
        expires_now();
        $data = getUserContext($this);
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('polyglot');
        $data['title'] = lang('users_create_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_create_user');

        $this->load->model('roles_model');
        $data['roles'] = $this->roles_model->get_roles();
        $this->load->model('contracts_model');
        $data['contracts'] = $this->contracts_model->get_contracts();
        $data['public_key'] = file_get_contents('./assets/keys/public.pem', true);

        $this->form_validation->set_rules('firstname', lang('users_create_field_firstname'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('lastname', lang('users_create_field_lastname'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('login', lang('users_create_field_login'), 'required|callback_login_check|xss_clean|strip_tags');
        $this->form_validation->set_rules('email', lang('users_create_field_email'), 'required|xss_clean|strip_tags');
        if (!$this->config->item('ldap_enabled')) $this->form_validation->set_rules('CipheredValue', lang('users_create_field_password'), 'required');
        $this->form_validation->set_rules('role[]', lang('users_create_field_role'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('manager', lang('users_create_field_manager'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('contract', lang('users_create_field_contract'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('position', lang('users_create_field_position'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('entity', lang('users_create_field_entity'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('datehired', lang('users_create_field_hired'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('identifier', lang('users_create_field_identifier'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('language', lang('users_create_field_language'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('timezone', lang('users_create_field_timezone'), 'xss_clean|strip_tags');
        if ($this->config->item('ldap_basedn_db')) $this->form_validation->set_rules('ldap_path', lang('users_create_field_ldap_path'), 'xss_clean|strip_tags');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('users/create', $data);
            $this->load->view('templates/footer');
        } else {
            $password = $this->users_model->set_users();
            log_message('info', 'User ' . $this->input->post('login') . ' has been created by user #' . $this->session->userdata('id'));
            
            //Send an e-mail to the user so as to inform that its account has been created
            $this->load->library('email');
            $usr_lang = $this->polyglot->code2language($this->input->post('language'));
            //We need to instance an different object as the languages of connected user may differ from the UI lang
            $lang_mail = new CI_Lang();
            $lang_mail->load('email', $usr_lang);
            
            $this->load->library('parser');
            $data = array(
                'Title' => $lang_mail->line('email_user_create_title'),
                'BaseURL' => base_url(),
                'Firstname' => $this->input->post('firstname'),
                'Lastname' => $this->input->post('lastname'),
                'Login' => $this->input->post('login'),
                'Password' => $password
            );
            $message = $this->parser->parse('emails/' . $this->input->post('language') . '/new_user', $data, TRUE);
            $this->email->set_encoding('quoted-printable');

            if ($this->config->item('from_mail') != FALSE && $this->config->item('from_name') != FALSE ) {
                $this->email->from($this->config->item('from_mail'), $this->config->item('from_name'));
            } else {
               $this->email->from('do.not@reply.me', 'LMS');
            }
            $this->email->to($this->input->post('email'));
            if ($this->config->item('subject_prefix') != FALSE) {
                $subject = $this->config->item('subject_prefix');
            } else {
               $subject = '[Jorani] ';
            }
            $this->email->subject($subject . $lang_mail->line('email_user_create_subject'));
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
     * @author Benjamin BALET <benjamin.balet@gmail.com>
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
     * @author Benjamin BALET <benjamin.balet@gmail.com>
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
        expires_now();
        $this->load->library('excel');
        $sheet = $this->excel->setActiveSheetIndex(0);
        $sheet->setTitle(mb_strimwidth(lang('users_export_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
        $sheet->setCellValue('A1', lang('users_export_thead_id'));
        $sheet->setCellValue('B1', lang('users_export_thead_firstname'));
        $sheet->setCellValue('C1', lang('users_export_thead_lastname'));
        $sheet->setCellValue('D1', lang('users_export_thead_email'));
        $sheet->setCellValue('E1', lang('users_export_thead_manager'));
        
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $users = $this->users_model->get_users();
        $line = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $line, $user['id']);
            $sheet->setCellValue('B' . $line, $user['firstname']);
            $sheet->setCellValue('C' . $line, $user['lastname']);
            $sheet->setCellValue('D' . $line, $user['email']);
            $sheet->setCellValue('E' . $line, $user['manager']);
            $line++;
        }

        //Autofit
        foreach(range('A', 'E') as $colD) {
            $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
        }
        
        $filename = 'users.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

	/**
	 * Action Import users from ldap
	 * @author Julien Ducro <julien@ducro.fr>
	 */
	public function import() {
		$this->auth->check_is_granted('create_user');
		expires_now();

		$ldap = ldap_connect($this->config->item('ldap_host'), $this->config->item('ldap_port'));
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		set_error_handler(function() { /* ignore errors */ });
		$bind = ldap_bind($ldap, $this->config->item('ldap_user'), $this->config->item('ldap_pass'));
		if ($bind) {
			$deleted = 0;
			$created = 0;
			if ($this->config->item('ldap_basedn_db')) {
				$basedn = $this->users_model->get_basedn($this->input->post('login'));
			} else {
				$basedn = sprintf($this->config->item('ldap_basedn'), $this->input->post('login'));
			}
			$result = ldap_search($ldap, $basedn, '(&(mail=*)(|(useraccountcontrol=514)(useraccountcontrol=546)))') or die ("Error in search query: ".ldap_error($ldap));
			$ldap_data = ldap_get_entries($ldap, $result);
			for ($i = 0; $i < $ldap_data['count']; $i++) {
				$email = $ldap_data[$i]['mail'][0];
				if ($user = $this->users_model->getUserByLogin($email)) {
					if ($user->active) {
						$deleted++;
						$this->users_model->set_active($user->id, false);
					}
				}
			}
			$result = ldap_search($ldap, $basedn, '(&(mail=*)(&(!(useraccountcontrol=514))(!(useraccountcontrol=546))))') or die ("Error in search query: ".ldap_error($ldap));
			$ldap_data = ldap_get_entries($ldap, $result);
			for ($i = 0; $i < $ldap_data['count']; $i++) {
				$email = $ldap_data[$i]['mail'][0];
				if (!$this->users_model->getUserByLogin($email)) {
					$ldap_user = $ldap_data[$i];
					$firstname = $ldap_user['givenname'][0];
					$lastname = $ldap_user['sn'][0];
					$login = $email;
					$password = '';
					// @TODO get default role
					$role = 2;
					$manager = NULL;
					$organization = 0;
					$contract = NULL;
					$timezone = NULL;
					$position = NULL;
					$identifier = NULL;
					$datehired = NULL;
					$ldap_path = NULL;
					$language = 'fr';
					$country = NULL;
					$calendar = NULL;
					if ($this->users_model->insert_user_api($firstname, $lastname, $login, $email, $password, $role,
						$manager, $organization, $contract, $position, $datehired, $identifier, $language, $timezone,
						$ldap_path, TRUE, $country, $calendar)) {
						$created++;
					}
				}
			}
			if ($deleted || $created) {
				$this->session->set_flashdata('msg', lang('users_import_flash_msg_success') . ' - ' . $deleted . ', + ' . $created);
			} else {
				$this->session->set_flashdata('msg', lang('users_import_flash_msg_no_change'));
			}
		}
		restore_error_handler();
		ldap_close($ldap);

		redirect('users');
	}
}
