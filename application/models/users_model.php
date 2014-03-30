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

class Users_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {

    }

    /**
     * Get the list of users or one user
     * @param int $id optional id of one user
     * @return array record of users
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_users($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('users');
            return $query->result_array();
        }
        $query = $this->db->get_where('users', array('id' => $id));
        return $query->row_array();
    }

    /**
     * Check if a login can be used before creating the user
     * @param type $login login identifier
     * @return bool true if available, false otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function is_login_available($login) {
        $this->db->from('users');
        $this->db->where('login', $login);
        $query = $this->db->get();

        if ($query->num_rows() == 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Delete a user from the database
     * @param int $id identifier of the user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_user($id) {
        $query = $this->db->delete('users', array('id' => $id));
    }

    /**
     * Insert a new user into the database. Inserted data are coming from an
     * HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_users() {
        //Load password hasher for create/update functions
        $this->load->library('bcrypt');
        
        //Decipher the password value (RSA encoded -> base64 -> decode -> decrypt)
        set_include_path(get_include_path() . PATH_SEPARATOR . APPPATH . 'third_party/phpseclib');
        include(APPPATH . '/third_party/phpseclib/Crypt/RSA.php');
        define("CRYPT_RSA_MODE", CRYPT_RSA_MODE_INTERNAL);
        $private_key = file_get_contents('./assets/keys/private.pem', true);
        $rsa = new Crypt_RSA();
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        $rsa->loadKey($private_key, CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        $password = $rsa->decrypt(base64_decode($this->input->post('CipheredValue')));
        
        //Hash the clear password using bcrypt
        $hash = $this->bcrypt->hash_password($password);
        
        $data = array(
            'firstname' => $this->input->post('firstname'),
            'lastname' => $this->input->post('lastname'),
            'login' => $this->input->post('login'),
            'email' => $this->input->post('email'),
            'password' => $hash,
            'role' => $this->input->post('role'),
            'manager' => $this->input->post('manager')
        );
        return $this->db->insert('users', $data);
    }

    /**
     * Update a given user in the database. Update data are coming from an
     * HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function update_users() {
        $data = array(
            'firstname' => $this->input->post('firstname'),
            'lastname' => $this->input->post('lastname'),
            'login' => $this->input->post('login'),
            'email' => $this->input->post('email'),
            'role' => $this->input->post('role'),
            'manager' => $this->input->post('manager')
        );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('users', $data);
    }

    /**
     * Update a given user in the database. Update data are coming from an
     * HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function reset_password($id, $CipheredNewPassword) {
        log_message('debug', '{models/users_model/reset_password} Entering function id=' . $id . ' / Ciphered password=' . $CipheredNewPassword);
        //Load password hasher for create/update functions
        $this->load->library('bcrypt');
        
        //Decipher the password value (RSA encoded -> base64 -> decode -> decrypt)
        set_include_path(get_include_path() . PATH_SEPARATOR . APPPATH . 'third_party/phpseclib');
        include(APPPATH . '/third_party/phpseclib/Crypt/RSA.php');
        define("CRYPT_RSA_MODE", CRYPT_RSA_MODE_INTERNAL);
        $private_key = file_get_contents('./assets/keys/private.pem', true);
        $rsa = new Crypt_RSA();
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        $rsa->loadKey($private_key, CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        $password = $rsa->decrypt(base64_decode($CipheredNewPassword));
        log_message('debug', '{models/users_model/reset_password} Password=' . $password);
        
        //Hash the clear password using bcrypt
        $hash = $this->bcrypt->hash_password($password);
        log_message('debug', '{models/users_model/reset_password} Hash=' . $hash);
        
        $data = array(
            'password' => $hash
        );
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
        log_message('debug', '{models/users_model/reset_password} Leaving function');
    }
    
    /**
     * Check the provided credentials
     * @param type $login user login
     * @param type $password password
     * @return bool true if the user is succesfully authenticated, false otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function check_credentials($login, $password) {
        //Load password hasher for create/update functions
        $this->load->library('bcrypt');
        $this->db->from('users');
        $this->db->where('login', $login);
        $query = $this->db->get();

        if ($query->num_rows() == 0) {
            //No match found
            return false;
        } else {
            $row = $query->row();
            if ($this->bcrypt->check_password($password, $row->password)) {
                // Password does match stored password.                
                if ($row->role == 1) {
                    $is_admin = true;
                }
                else {
                    $is_admin = false;
                }
                $newdata = array(
                    'login' => $row->login,
                    'id' => $row->id,
                    'firstname' => $row->firstname,
                    'lastname' => $row->lastname,
                    'is_admin' => $is_admin,
                    'manager' => $row->manager,
                    'logged_in' => TRUE
                );                
                $this->session->set_userdata($newdata);
                return true;
            } else {
                // Password does not match stored password.
                return false;
            }
        }
    }
}
