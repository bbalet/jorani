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

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Session extends CI_Controller {

    /**
     * Default constructor
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('users_model');
    }

    /**
     * Login form
     */
    public function login() {
        $data['title'] = 'Login';
        $this->load->helper('form');
        $this->load->library('form_validation');
        //Note that we don't receive the password as a clear string
        $this->form_validation->set_rules('login', 'Login identifier', 'required');
        $this->form_validation->set_rules('CipheredValue', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['public_key'] = file_get_contents('./assets/keys/public.pem', true);
            $this->load->view('templates/header', $data);
            $this->load->view('session/login', $data);
            $this->load->view('templates/footer');
        } else {
            //Decipher the password value (RSA encoded -> base64 -> decode -> decrypt)
            set_include_path(get_include_path() . PATH_SEPARATOR . APPPATH . 'third_party/phpseclib');
            include(APPPATH . '/third_party/phpseclib/Crypt/RSA.php');
            define("CRYPT_RSA_MODE", CRYPT_RSA_MODE_INTERNAL);
            $private_key = file_get_contents('./assets/keys/private.pem', true);
            $rsa = new Crypt_RSA();
            $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
            $rsa->loadKey($private_key, CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
            $password = $rsa->decrypt(base64_decode($this->input->post('CipheredValue')));

            //Hash the password passed through the login form and check if it matches the stored password
            if ($this->users_model->check_credentials($this->input->post('login'), $password)) {
                redirect('home');
            } else {
                if ($this->session->userdata('last_page') != '') {
                    redirect($this->session->userdata('last_page'));
                } else {
                    redirect('home');
                }
            }
        }
    }

    /**
     * Logout the user and destroy the session data
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('session/login');
    }

}
