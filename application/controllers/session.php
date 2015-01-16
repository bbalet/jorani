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
        $this->load->helper('language');
        $this->lang->load('session', $this->session->userdata('language'));
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
        $this->load->helper('form');
        $this->load->library('form_validation');
        //Note that we don't receive the password as a clear string
        $this->form_validation->set_rules('login', lang('session_login_field_login'), 'required');
        $this->form_validation->set_rules('CipheredValue', lang('session_login_field_password'), 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['public_key'] = file_get_contents('./assets/keys/public.pem', true);
            $data['salt'] = $this->generateRandomString(rand(5, 20));
            $data['language'] = $this->session->userdata('language');
            $data['language_code'] = $this->session->userdata('language_code');
            $this->session->set_userdata('salt', $data['salt']);
            $this->load->view('templates/header', $data);
            $this->load->view('session/login', $data);
            $this->load->view('templates/footer');
        } else {
            $this->load->model('users_model');
            //Set language
            $this->session->set_userdata('language_code', $this->input->post('language'));
            $this->session->set_userdata('language', $this->polyglot->code2language($this->input->post('language')));
            
            //Decipher the password value (RSA encoded -> base64 -> decode -> decrypt)
            set_include_path(get_include_path() . PATH_SEPARATOR . APPPATH . 'third_party/phpseclib');
            include(APPPATH . '/third_party/phpseclib/Crypt/RSA.php');
            if(extension_loaded('openssl') && file_exists(CRYPT_RSA_OPENSSL_CONFIG)) {
                define("CRYPT_RSA_MODE", CRYPT_RSA_MODE_OPENSSL);
            } else {
                define("CRYPT_RSA_MODE", CRYPT_RSA_MODE_INTERNAL);
            }            
            $private_key = file_get_contents('./assets/keys/private.pem', true);
            $rsa = new Crypt_RSA();
            $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
            $rsa->loadKey($private_key, CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
            $password = $rsa->decrypt(base64_decode($this->input->post('CipheredValue')));
            //Remove the salt
            $len_salt = strlen($this->session->userdata('salt')) * (-1);
            $password = substr($password, 0, $len_salt);
            
            $loggedin = FALSE;
            if ($this->config->item('ldap_enabled')) {
                $ldap = ldap_connect($this->config->item('ldap_host'), $this->config->item('ldap_port'));
                ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
                set_error_handler(function() { /* ignore errors */ });
                if ($this->config->item('ldap_basedn_db')) {
                    $basedn = $this->users_model->get_basedn($this->input->post('login'));
                } else {
                    $basedn = sprintf($this->config->item('ldap_basedn'), $this->input->post('login'));
                }
                $bind = ldap_bind($ldap, $basedn, $password);
                restore_error_handler();
                if ($bind) {
                    $loggedin = $this->users_model->load_profile($this->input->post('login'));
                }
                ldap_close($ldap);
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
                $this->load->view('templates/header', $data);
                $this->load->view('session/login', $data);
                $this->load->view('templates/footer');
            } else {
                //If the user has a target page (e.g. link in an e-mail), redirect to this destination
                $parsed_url = parse_url($this->session->userdata('last_page'));
                if ($parsed_url['path'] == '/lms/index.php') {
                    $this->session->set_userdata('last_page', '');
                }
                if ($this->session->userdata('last_page') != base_url().'index.php' && $this->session->userdata('last_page') != '') {
                    log_message('debug', '{controllers/session/login} Redirect to last page=' . $this->session->userdata('last_page'));
                    redirect($this->session->userdata('last_page'));
                } else {
                    log_message('debug', '{controllers/session/login} Redirect to home page');
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
        $this->expires_now();
        $this->output->set_content_type('text/plain');
        $login = $this->input->post('login');
        $this->load->model('users_model');
        $user = $this->users_model->getUserByLogin($login);
        if (is_null($user)) {
            echo "UNKNOWN";
        } else {
            //Send an email to the user with its login information
            $this->load->model('settings_model');
            $this->load->library('email');
            $this->lang->load('email', $this->language);
            
            //Generate random password and store its hash into db
            $password = $this->users_model->resetClearPassword($user->id);
            
            //Send an e-mail to the user requesting a new password
            $this->load->library('parser');
            $data = array(
                'Title' => lang('email_password_forgotten_title'),
                'BaseURL' => base_url(),
                'Firstname' => $user->firstname,
                'Lastname' => $user->lastname,
                'Login' => $user->login,
                'Password' => $password
            );
            $message = $this->parser->parse('emails/' . $user->language . '/password_forgotten', $data, TRUE);
            if ($this->email->mailer_engine== 'phpmailer') {
                $this->email->phpmailer->Encoding = 'quoted-printable';
            }

            if ($this->config->item('from_mail') != FALSE && $this->config->item('from_name') != FALSE ) {
                $this->email->from($this->config->item('from_mail'), $this->config->item('from_name'));
            } else {
               $this->email->from('do.not@reply.me', 'LMS');
            }
            $this->email->to($user->email);
            $this->email->subject(lang('email_password_forgotten_subject'));
            $this->email->message($message);
            $this->email->send();
            echo "OK";
        }
    }
    
    /**
     * Internal utility function
     * make sure a resource is reloaded every time
     */
    private function expires_now() {
        // Date in the past
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        // always modified
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        // HTTP/1.1
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        // HTTP/1.0
        header("Pragma: no-cache");
    }
}
