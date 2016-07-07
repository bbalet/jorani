<?php
/**
 * This controller is the entry point for a mobile endpoint
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.3.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class implements a REST API for a mobile endpoint.
 * This is an experimental feature
 */
class Mobile extends CI_Controller {
    /**
     * Get the public RSA key of the Jorani instance
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getPublicKey() {
        if ($this->config->item('enable_mobile') != FALSE) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Credentials : true');
            header('Access-Control-Allow-Methods: GET');
            header('Content-Type:application/json');
            $this->session->sess_create();
            $this->security->csrf_set_cookie();
            $salt = $this->generateRandomString(rand(5, 20));
            $this->session->set_userdata('salt', $salt);
            $security = new stdClass;
            $security->salt = $salt;
            $security->publicKey = file_get_contents('./assets/keys/public.pem', TRUE);
            $security->csrfProtection = $this->config->item('csrf_protection');
            $security->csrfTokenName = $this->security->get_csrf_token_name();
            $security->csrfHash = $this->security->get_csrf_hash();
            echo json_encode($security);
        }
    }
    
    /**
     * Try to login with the e-mail address and the password provided
     * @param string $email Email of the user trying to connect
     * @param string $password Ciphered value of the password
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function login() {
        if ($this->config->item('enable_mobile') != FALSE) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Credentials : true');
            header('Access-Control-Allow-Methods: GET');
            header('Content-Type:application/json');
            //Decipher the password (mind to remove the salt) and attempt to login
            $password = '';
            $keyPEM = file_get_contents('./assets/keys/private.pem', TRUE);
            $privateKey = openssl_pkey_get_private($keyPEM);
            openssl_private_decrypt(base64_decode($this->input->get('password')), 
                                    $password, $privateKey, OPENSSL_PKCS1_OAEP_PADDING);
            $len_salt = strlen($this->session->userdata('salt')) * (-1);
            $password = substr($password, 0, $len_salt);
            $this->load->model('users_model');
            $loggedin = $this->users_model->checkCredentialsEmail($this->input->get('email'), $password);
            //Return user's details (some fields might be empty if not logged in)
            $userDetails = new stdClass;
            $userDetails->id = $this->session->userdata('id');
            $userDetails->firstname = $this->session->userdata('firstname');
            $userDetails->lastname = $this->session->userdata('lastname');
            $userDetails->isManager = $this->session->userdata('is_manager');
            $userDetails->isAdmin = $this->session->userdata('is_admin');
            $userDetails->isHR = $this->session->userdata('is_hr');
            $userDetails->managerId = $this->session->userdata('manager');
            $userDetails->isLoggedIn = $loggedin;
            echo json_encode($userDetails);
        }
    }
    
    /**
     * Collect notifications
     * @param int $employeeId Email of the user trying to connect
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function notifications() {
        if ($this->config->item('enable_mobile') != FALSE) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Credentials : true');
            header('Access-Control-Allow-Methods: GET');
            header('Content-Type:application/json');
            $notifications = new stdClass;
            $notifications->requestedLeavesCount = 0;
            $notifications->requestedExtraCount = 0;
            if ($this->session->userdata('is_manager') === TRUE) {
                $this->load->model('leaves_model');
                $notifications->requestedLeavesCount = $this->leaves_model->countLeavesRequestedToManager($this->input->get('employeeId'));
                if ($this->config->item('disable_overtime') == FALSE) {
                    $this->load->model('overtime_model');
                    $notifications->requestedExtraCount = $this->overtime_model->countExtraRequestedToManager($this->input->get('employeeId'));
                } else {
                    $notifications->requestedExtraCount = 0;
                }
            }
            echo json_encode($notifications);
        }
    }
    
    /**
     * Disconnect the user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function logout() {
        if ($this->config->item('enable_mobile') != FALSE) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Credentials : true');
            header('Access-Control-Allow-Methods: GET');
            $this->session->sess_destroy();
            echo 'BYE';
        }
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
}
