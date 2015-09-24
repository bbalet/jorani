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

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Session extends CI_Controller {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('polyglot');
        if ($this->session->userdata('language') == FALSE) {
            $this->session->set_userdata('language', $this->config->item('language'));
            $this->session->set_userdata('language_code', $this->polyglot->language2code($this->config->item('language')));
        }
        $this->lang->load('session', $this->session->userdata('language'));
        $this->lang->load('global', $this->session->userdata('language'));
    }

    /**
     * Generate a random string by picking randomly in letters and numbers
     * @param int $length optional length of the string
     * @return string random string
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    /**
     * Login form
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function login() {
        $data['title'] = lang('session_login_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_login');
        $this->load->helper('form');
        $this->load->library('form_validation');
        //Note that we don't receive the password as a clear string
        $this->form_validation->set_rules('login', lang('session_login_field_login'), 'required');
        //$this->form_validation->set_rules('CipheredValue', lang('session_login_field_password'), 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['public_key'] = file_get_contents('./assets/keys/public.pem', true);
            $data['salt'] = $this->generateRandomString(rand(5, 20));
            $data['language'] = $this->session->userdata('language');
            $data['language_code'] = $this->session->userdata('language_code');
            $this->session->set_userdata('salt', $data['salt']);
            $data['flash_partial_view'] = $this->load->view('templates/flash', $data, true);
            $this->load->view('templates/header', $data);
            $this->load->view('session/login', $data);
            $this->load->view('templates/footer');
        } else {
            $this->load->model('users_model');
            //Set language
            $this->session->set_userdata('language_code', $this->input->post('language'));
            $this->session->set_userdata('language', $this->polyglot->code2language($this->input->post('language')));
            
            //Decipher the password value (RSA encoded -> base64 -> decode -> decrypt) and remove the salt!
            require_once(APPPATH . 'third_party/phpseclib/vendor/autoload.php');
            $rsa = new phpseclib\Crypt\RSA();
            $private_key = file_get_contents('./assets/keys/private.pem', true);
            $rsa->setEncryptionMode(phpseclib\Crypt\RSA::ENCRYPTION_PKCS1);
            $rsa->loadKey($private_key, phpseclib\Crypt\RSA::PRIVATE_FORMAT_PKCS1);
            $password = $rsa->decrypt(base64_decode($this->input->post('CipheredValue')));
            //Remove the salt
            $len_salt = strlen($this->session->userdata('salt')) * (-1);
            $password = substr($password, 0, $len_salt);
            
            $loggedin = FALSE;
            if ($this->config->item('ldap_enabled')) {
                if ($password != "") { //Bind to MS-AD with blank password might return OK
                $ldap = ldap_connect($this->config->item('ldap_host'), $this->config->item('ldap_port'));
                ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
                set_error_handler(function() { /* ignore errors */ });
					$bind = ldap_bind($ldap, $this->config->item('ldap_user'), $this->config->item('ldap_pass'));
					if ($bind) {
						if ($this->config->item('ldap_basedn_db')) {
							$basedn = $this->users_model->get_basedn($this->input->post('login'));
						} else {
							$basedn = sprintf($this->config->item('ldap_basedn'), $this->input->post('login'));
						}
						$ldap_filter = str_replace('%%USER%%', $this->input->post('login'), $this->config->item('ldap_filter'));
						$result = ldap_search($ldap, $basedn, $ldap_filter) or die ("Error in search query: ".ldap_error($ldap));
						$ldap_data = ldap_get_entries($ldap, $result);
						if ($ldap_data['count']) {
							$email = $ldap_data[0]['mail'][0];
							$bind_user = ldap_bind($ldap, $ldap_data[0]['userprincipalname'][0], $password);

							if ($bind_user && $email) {
								$loggedin = $this->users_model->load_profile($email);
							}
						}
					}
					restore_error_handler();
					ldap_close($ldap);
				}
            } else {
                $loggedin = $this->users_model->check_credentials($this->input->post('login'), $password);
            }

            if ($loggedin == FALSE) {
                log_message('error', '{controllers/session/login} Invalid login id or password for user=' . $this->input->post('login'));
                $this->session->set_flashdata('msg', lang('session_login_flash_bad_credentials'));
                $data['public_key'] = file_get_contents('./assets/keys/public.pem', true);
                $data['salt'] = $this->generateRandomString(rand(5, 20));
                $data['language'] = $this->session->userdata('language');
                $data['language_code'] = $this->session->userdata('language_code');
                $this->session->set_userdata('salt', $data['salt']);
                $data['flash_partial_view'] = $this->load->view('templates/flash', $data, true);
                $this->load->view('templates/header', $data);
                $this->load->view('session/login', $data);
                $this->load->view('templates/footer');
            } else {
                //If the user has a target page (e.g. link in an e-mail), redirect to this destination
                if ($this->session->userdata('last_page') != '') {
                    if (strpos($this->session->userdata('last_page'), 'index.php', strlen($this->session->userdata('last_page')) - strlen('index.php'))) {
                        $this->session->set_userdata('last_page', base_url() . 'home');
                    }
                    redirect($this->session->userdata('last_page'));
                } else {
                    redirect(base_url() . 'home');
                }
            }
        }
    }

    /**
     * Logout the user and destroy the session data
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('session/login');
    }

    /**
     * Change the language and redirect to last page (i.e. page that submit the
     * language form)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function language() {
        $this->load->helper('form');
        $this->session->set_userdata('language_code', $this->input->post('language'));
        $this->session->set_userdata('language', $this->polyglot->code2language($this->input->post('language')));
        redirect($this->input->post('last_page'));
    }
    
    /**
     * Send the password by e-mail to a user requesting it
     */
    public function forgetpassword() {
        expires_now();
        $this->output->set_content_type('text/plain');
        $login = $this->input->post('login');
        $this->load->model('users_model');
        $user = $this->users_model->getUserByLogin($login);
        if (is_null($user)) {
            echo "UNKNOWN";
        } else {
            //Send an email to the user with its login information
            $this->load->library('email');
            
            //We need to instance an different object as the languages of connected user may differ from the UI lang
            $lang_mail = new CI_Lang();
            $usr_lang = $this->polyglot->code2language($user->language);
            $lang_mail->load('email', $usr_lang);
            $lang_mail->load('global', $usr_lang);
            
            //Generate random password and store its hash into db
            $password = $this->users_model->resetClearPassword($user->id);
            
            //Send an e-mail to the user requesting a new password
            $this->load->library('parser');
            $data = array(
                'Title' => $lang_mail->line('email_password_forgotten_title'),
                'BaseURL' => base_url(),
                'Firstname' => $user->firstname,
                'Lastname' => $user->lastname,
                'Login' => $user->login,
                'Password' => $password
            );
            $message = $this->parser->parse('emails/' . $user->language . '/password_forgotten', $data, TRUE);
            $this->email->set_encoding('quoted-printable');

            if ($this->config->item('from_mail') != FALSE && $this->config->item('from_name') != FALSE ) {
                $this->email->from($this->config->item('from_mail'), $this->config->item('from_name'));
            } else {
               $this->email->from('do.not@reply.me', 'LMS');
            }
            $this->email->to($user->email);
            if ($this->config->item('subject_prefix') != FALSE) {
                $subject = $this->config->item('subject_prefix');
            } else {
               $subject = '[Jorani] ';
            }
            $this->email->subject($subject . $lang_mail->line('email_password_forgotten_subject'));
            $this->email->message($message);
            $this->email->send();
            echo "OK";
        }
    }
}
