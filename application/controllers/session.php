<?php
/**
 * This controller manages user session
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class manages user session
 * CodeIgniter uses a cookie to store session's details.
 * Login page uses RSA so as to encrypt the user's password.
 */
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
     * Generate a random string by using openssl, dev/urandom or random
     * @param int $length optional length of the string
     * @return string random string
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function generateRandomString($length = 10) {
        if(function_exists('openssl_random_pseudo_bytes')) {
          $rnd = openssl_random_pseudo_bytes($length, $strong);
          if ($strong === TRUE)
            return base64_encode($rnd);
        }
        $sha =''; $rnd ='';
        if (file_exists('/dev/urandom')) {
          $fp = fopen('/dev/urandom', 'rb');
          if ($fp) {
              if (function_exists('stream_set_read_buffer')) {
                  stream_set_read_buffer($fp, 0);
              }
              $sha = fread($fp, $length);
              fclose($fp);
          }
        }
        for ($i=0; $i<$length; $i++) {
          $sha  = hash('sha256',$sha.mt_rand());
          $char = mt_rand(0,62);
          $rnd .= chr(hexdec($sha[$char].$sha[$char+1]));
        }
        return base64_encode($rnd);
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

        $data['last_page'] = $this->session->userdata('last_page');
        if ($this->form_validation->run() === FALSE) {
            $data['public_key'] = file_get_contents('./assets/keys/public.pem', TRUE);
            $data['salt'] = $this->generateRandomString(rand(5, 20));
            $data['language'] = $this->session->userdata('language');
            $data['language_code'] = $this->session->userdata('language_code');
            $this->session->set_userdata('salt', $data['salt']);
            $data['flash_partial_view'] = $this->load->view('templates/flash', $data, TRUE);
            $this->load->view('templates/header', $data);
            $this->load->view('session/login', $data);
            $this->load->view('templates/footer');
        } else {
            $this->load->model('users_model');
            //Set language
            $this->session->set_userdata('language_code', $this->input->post('language'));
            $this->session->set_userdata('language', $this->polyglot->code2language($this->input->post('language')));
            
            //Decipher the password value (RSA encoded -> base64 -> decode -> decrypt) and remove the salt!
            $password = '';
            if (function_exists('openssl_pkey_get_private')) {
                $privateKey = openssl_pkey_get_private(file_get_contents('./assets/keys/private.pem', TRUE));
                openssl_private_decrypt(base64_decode($this->input->post('CipheredValue')), $password, $privateKey);
            } else {
                require_once(APPPATH . 'third_party/phpseclib/vendor/autoload.php');
                $rsa = new phpseclib\Crypt\RSA();
                $private_key = file_get_contents('./assets/keys/private.pem', TRUE);
                $rsa->setEncryptionMode(phpseclib\Crypt\RSA::ENCRYPTION_PKCS1);
                $rsa->loadKey($private_key, phpseclib\Crypt\RSA::PRIVATE_FORMAT_PKCS1);
                $password = $rsa->decrypt(base64_decode($this->input->post('CipheredValue')));
            }
            //Remove the salt
            $len_salt = strlen($this->session->userdata('salt')) * (-1);
            $password = substr($password, 0, $len_salt);
            
            $loggedin = FALSE;
            if ($this->config->item('ldap_enabled')) {
                if ($password != "") { //Bind to MS-AD with blank password might return OK
                $ldap = ldap_connect($this->config->item('ldap_host'), $this->config->item('ldap_port'));
                ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
                set_error_handler(function() { /* ignore errors */ });
                if ($this->config->item('ldap_basedn_db')) {
                    $basedn = $this->users_model->getBaseDN($this->input->post('login'));
                } else {
                    $basedn = sprintf($this->config->item('ldap_basedn'), $this->input->post('login'));
                }
                $bind = ldap_bind($ldap, $basedn, $password);
                restore_error_handler();
                if ($bind) {
                    $loggedin = $this->users_model->checkCredentialsLDAP($this->input->post('login'));
                }
                ldap_close($ldap);
                }
            } else {
                $loggedin = $this->users_model->checkCredentials($this->input->post('login'), $password);
            }
            
            if ($loggedin == FALSE) {
                log_message('error', '{controllers/session/login} Invalid login id or password for user=' . $this->input->post('login'));
                if ($this->users_model->isActive($this->input->post('login'))) {
                    $this->session->set_flashdata('msg', lang('session_login_flash_bad_credentials'));
                } else {
                    $this->session->set_flashdata('msg', lang('session_login_flash_account_disabled'));
                }
                redirect('session/login');
            } else {
                //If the user has a target page (e.g. link in an e-mail), redirect to this destination
                if ($this->session->userdata('last_page') != '') {
                    if (strpos($this->session->userdata('last_page'), 'index.php', strlen($this->session->userdata('last_page')) - strlen('index.php'))) {
                        $this->session->set_userdata('last_page', base_url() . 'home');
                    }
                    if ($this->session->userdata('last_page_params') == '') {
                        redirect($this->session->userdata('last_page'));
                    } else {
                        redirect($this->session->userdata('last_page') . '?' . $this->session->userdata('last_page_params'));
                    }
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
     * Change the language and redirect to last page (i.e. page that submit the language form)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function language() {
        $this->load->helper('form');
        $this->session->set_userdata('language_code', $this->input->post('language'));
        $this->session->set_userdata('language', $this->polyglot->code2language($this->input->post('language')));
        redirect($this->input->post('last_page'));
    }
    
    /**
     * Ajax : Send the password by e-mail to a user requesting it
     * POST: string login Login of the user
     * RETURN: UNKNOWN if the login was not found, OK otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function forgetpassword() {
        $this->output->set_content_type('text/plain');
        $login = $this->input->post('login');
        $this->load->model('users_model');
        $user = $this->users_model->getUserByLogin($login);
        if (is_null($user)) {
            echo "UNKNOWN";
        } else {
            //We need to instance an different object as the languages of connected user may differ from the UI lang
            $lang_mail = new CI_Lang();
            $usr_lang = $this->polyglot->code2language($user->language);
            $lang_mail->load('email', $usr_lang);
            $lang_mail->load('global', $usr_lang);
            //Generate random password and store its hash into db
            $password = $this->users_model->resetClearPassword($user->id);
            //Prepare the e-mail content by parsing a view
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
            //Send the e-mail
            sendMailByWrapper($this,
                    $lang_mail->line('email_password_forgotten_subject'),
                    $message,
                    $user->email);
            //Tell to the frontend that we've found the login and sent the email
            echo "OK";
        }
    }
    
    /**
     * Try to authenticate the user using one of the OAuth2 providers
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function loginOAuth2() {
        require_once APPPATH . 'third_party/OAuthClient/vendor/autoload.php';
        $oauth2Enabled = $this->config->item('oauth2_enabled');
        $oauth2Provider = $this->config->item('oauth2_provider');
        $oauth2ClientId = $this->config->item('oauth2_client_id');
        $oauth2ClientSecret = $this->config->item('oauth2_client_secret');
        if ($oauth2Enabled === FALSE) {
            echo 'ERROR: OAuth2 is disabled';
            return;
        }
        $authCode = $this->input->post('auth_code');
        
        if (!is_null($authCode)) {
            $this->load->model('users_model');
            switch ($oauth2Provider) {
                case 'google':
                    $provider = new League\OAuth2\Client\Provider\Google(Array(
                        'clientId' => $oauth2ClientId,
                        'clientSecret' => $oauth2ClientSecret,
                        'redirectUri' => 'postmessage',
                        'accessType' => 'offline',
                    ));
                    $token = $provider->getAccessToken('authorization_code', Array('code' => $authCode));
                    try {
                        //We try to get the e-mail address from the Google+ API
                        $ownerDetails = $provider->getResourceOwner($token);
                        $email = $ownerDetails->getEmail();
                        //If we find the e-mail address into the database, we're good
                        $loggedin = $this->users_model->checkCredentialsEmail($email);
                        if ($loggedin === TRUE) {
                            echo 'OK';
                        } else {
                            echo lang('session_login_flash_bad_credentials');
                        }
                    } catch (Exception $e) {
                        echo 'ERROR: ' . $e->getMessage();
                    }
                    break;
                default:
                    echo 'ERROR: unsupported OAuth2 provider';
            }
        } else {
            echo 'ERROR: Invalid OAuth2 token';
        }
    }

}
