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
     * Get the list of employees
     * @return array record of users
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_all_employees() {
        $this->db->select('id, firstname, lastname, email');
        $query = $this->db->get('users');
        return $query->result_array();
    }
    
    /**
     * Get the label of a given user id
     * @param type $id
     * @return string label
     */
    public function get_label($id) {
        $record = $this->get_users($id);
        if (count($record) > 0) {
            return $record['firstname'] . ' ' . $record['lastname'];
        }
    }
    
    /**
     * Get the list of employees belonging to an entity
     * @param int $id identifier of the entity
     * @return array record of users
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_employees_entity($id = 0) {
        $query = $this->db->get_where('users', array('organization' => $id));
        return $query->result_array();
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
        //Trace the modification if the feature is enabled
        if ($this->config->item('enable_history') == TRUE) {
            $this->load->model('history_model');
            $this->history_model->set_history(3, 'users', $id, $this->session->userdata('id'));
        }
        
        $query = $this->db->delete('users', array('id' => $id));
        $this->load->model('entitleddays_model');
        $this->entitleddays_model->delete_entitleddays_cascade_user($id);
        //Cascade delete line manager role
        $data = array(
            'manager' => NULL
        );
        $this->db->where('manager', $id);
        $this->db->update('users', $data);
    }

    /**
     * Insert a new user into the database. Inserted data are coming from an
     * HTML form
     * @return string deciphered password (so as to send it by e-mail in clear)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_users() {
        //Load password hasher for create/update functions
        $this->load->library('bcrypt');
        
        //Decipher the password value (RSA encoded -> base64 -> decode -> decrypt)
        set_include_path(get_include_path() . PATH_SEPARATOR . APPPATH . 'third_party/phpseclib');
        include(APPPATH . '/third_party/phpseclib/Crypt/RSA.php');
        if (extension_loaded('openssl') && file_exists(CRYPT_RSA_OPENSSL_CONFIG)) {
            define("CRYPT_RSA_MODE", CRYPT_RSA_MODE_OPENSSL);
        } else {
            define("CRYPT_RSA_MODE", CRYPT_RSA_MODE_INTERNAL);
        }
        $private_key = file_get_contents('./assets/keys/private.pem', true);
        $rsa = new Crypt_RSA();
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        $rsa->loadKey($private_key, CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        $password = $rsa->decrypt(base64_decode($this->input->post('CipheredValue')));
        
        //Hash the clear password using bcrypt
        $hash = $this->bcrypt->hash_password($password);
        
        //Role field is a binary mask
        $role = 0;
        foreach($this->input->post("role") as $role_bit){
            $role = $role | $role_bit;
        }        
        
        if ($this->input->post('datehired') == "") {
            $datehired = NULL;
        } else {
            $datehired = $this->input->post('datehired');
        }
        
        $data = array(
            'firstname' => $this->input->post('firstname'),
            'lastname' => $this->input->post('lastname'),
            'login' => $this->input->post('login'),
            'email' => $this->input->post('email'),
            'password' => $hash,
            'role' => $role,
            'manager' => $this->input->post('manager'),
            'contract' => $this->input->post('contract'),
            'organization' => $this->input->post('entity'),
            'position' => $this->input->post('position'),
            'datehired' => $datehired,
            'identifier' => $this->input->post('identifier'),
            'language' => $this->input->post('language')
        );
        $this->db->insert('users', $data);
        
        //Trace the modification if the feature is enabled
        if ($this->config->item('enable_history') == TRUE) {
            $this->load->model('history_model');
            $this->history_model->set_history(1, 'users', $this->db->insert_id(), $this->session->userdata('id'));
        }
        
        //Deal with user having no line manager
        if ($this->input->post('manager') == -1) {
            $id = $this->db->insert_id();
            $data = array(
                'manager' => $id
            );
            $this->db->where('id', $id);
            $this->db->update('users', $data);
        }
        
        return $password;
    }
    
    /**
     * Create a user record in the database. the difference with set_users function is that it doesn't
     * values posted by en HTML form. Can be used by a mass importer for example.
     * @param type $firstname User firstname
     * @param type $lastname User lastname
     * @param type $login User login
     * @param type $email User e-mail
     * @param type $password User password
     * @param int $role role mask
     * @param int $manager Id of the manager or 0
     * @return int Inserted User Identifier
     */
    public function insert_user($firstname, $lastname, $login, $email, $password, $role, $manager) {
        //Hash the clear password using bcrypt
        $this->load->library('bcrypt');
        $hash = $this->bcrypt->hash_password($password);
        $data = array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'login' => $login,
            'email' => $email,
            'password' => $hash,
            'role' => $role,
            'manager' => $manager
        );
        $this->db->insert('users', $data);
        
        //Trace the modification if the feature is enabled
        if ($this->config->item('enable_history') == TRUE) {
            $this->load->model('history_model');
            $this->history_model->set_history(1, 'users', $this->db->insert_id(), $this->session->userdata('id'));
        }
        
        return $this->db->insert_id();
    }

    /**
     * Update a given user in the database. Update data are coming from an
     * HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function update_users() {
        
        //Role field is a binary mask
        $role = 0;
        foreach($this->input->post("role") as $role_bit){
            $role = $role | $role_bit;
        }
        
        //Deal with user having no line manager
        if ($this->input->post('manager') == -1) {
            $manager = $this->input->post('id');
        } else {
            $manager = $this->input->post('manager');
        }
        
        if ($this->input->post('datehired') == "") {
            $datehired = NULL;
        } else {
            $datehired = $this->input->post('datehired');
        }
        
        $data = array(
            'firstname' => $this->input->post('firstname'),
            'lastname' => $this->input->post('lastname'),
            'login' => $this->input->post('login'),
            'email' => $this->input->post('email'),
            'role' => $role,
            'manager' => $manager,
            'contract' => $this->input->post('contract'),
            'organization' => $this->input->post('entity'),
            'position' => $this->input->post('position'),
            'datehired' => $datehired,
            'identifier' => $this->input->post('identifier'),
            'language' => $this->input->post('language')
        );

        $this->db->where('id', $this->input->post('id'));
        $result = $this->db->update('users', $data);
        
        //Trace the modification if the feature is enabled
        if ($this->config->item('enable_history') == TRUE) {
            $this->load->model('history_model');
            $this->history_model->set_history(2, 'users', $this->input->post('id'), $this->session->userdata('id'));
        }
        
        return $result;
    }

    /**
     * Update a given user in the database. Update data are coming from an
     * HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function reset_password($id, $CipheredNewPassword) {
        //log_message('debug', '{models/users_model/reset_password} Entering function id=' . $id . ' / Ciphered password=' . $CipheredNewPassword);
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
        $result = $this->db->update('users', $data);
        
        //Trace the modification if the feature is enabled
        if ($this->config->item('enable_history') == TRUE) {
            $this->load->model('history_model');
            $this->history_model->set_history(2, 'users', $id, $this->session->userdata('id'));
        }
        
        //log_message('debug', '{models/users_model/reset_password} Leaving function');
        return $result;
    }
    
    /**
     * Reset a password. Generate a new password and store its hash into db.
     * @param int $id User identifier
     * @return string clear password
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function resetClearPassword($id) {
        //Load password hasher for create/update functions
        $this->load->library('bcrypt');
        //generate a random password of length 10
        $password = $this->randomPassword(10);
        //Hash the clear password using bcrypt
        $hash = $this->bcrypt->hash_password($password);
        //Store the new password into db
        $data = array(
            'password' => $hash
        );
        $this->db->where('id', $id);
        $this->db->update('users', $data);
        
        //Trace the modification if the feature is enabled
        if ($this->config->item('enable_history') == TRUE) {
            $this->load->model('history_model');
            $this->history_model->set_history(2, 'users', $id, $this->session->userdata('id'));
        }
        
        return $password;
    }
    
    /**
     * Generate a random password
     * @param int $length length of the generated password
     * @return string generated password
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function randomPassword($length) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
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
                if (((int) $row->role & 1)) {
                    $is_admin = true;
                }
                else {
                    $is_admin = false;
                }
                
               /*
                00000001 1  Admin
                00000100 8  HR Officier / Local HR Manager
                00001000 16 HR Manager
              = 00001101 25 Can access to HR functions
                */
                if (((int) $row->role & 25)) {
                    $is_hr = true;
                }
                else {
                    $is_hr = false;
                }
                
                $newdata = array(
                    'login' => $row->login,
                    'id' => $row->id,
                    'firstname' => $row->firstname,
                    'lastname' => $row->lastname,
                    'is_admin' => $is_admin,
                    'is_hr' => $is_hr,
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
    
    /**
     * Load the profile of a user
     * @param type $login user login
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function load_profile($login) {
        $this->db->from('users');
        $this->db->where('login', $login);
        $query = $this->db->get();
        $row = $query->row();
        // Password does match stored password.
        if (((int) $row->role & 1)) {
            $is_admin = true;
        } else {
            $is_admin = false;
        }

        /*
          00000001 1  Admin
          00000100 8  HR Officier / Local HR Manager
          00001000 16 HR Manager
          = 00001101 25 Can access to HR functions
         */
        if (((int) $row->role & 25)) {
            $is_hr = true;
        } else {
            $is_hr = false;
        }

        $newdata = array(
            'login' => $row->login,
            'id' => $row->id,
            'firstname' => $row->firstname,
            'lastname' => $row->lastname,
            'is_admin' => $is_admin,
            'is_hr' => $is_hr,
            'manager' => $row->manager,
            'logged_in' => TRUE
        );
        $this->session->set_userdata($newdata);
    }

    /**
     * Get the list of employees or one employee
     * @param int $id optional id of one user
     * @return array record of users
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_employees($id = 0) {
        if ($id === 0) {
            $this->db->select('users.id as id,'
                        . ' users.firstname as firstname,'
                        . ' users.lastname as lastname,'
                        . ' users.email as email,'
                        . ' contracts.name as contract,'
                        . ' managers.firstname as manager_firstname,'
                        . ' managers.lastname as manager_lastname');
            $this->db->from('users');
            $this->db->join('contracts', 'contracts.id = users.contract', 'left outer');
            $this->db->join('users as managers', 'managers.id = users.manager', 'left outer');
            return $this->db->get()->result_array();
        } else {
            $this->db->select('users.id as id,'
                        . ' users.firstname as firstname,'
                        . ' users.lastname as lastname,'
                        . ' users.email as email,'
                        . ' contracts.name as contract');
            $this->db->from('users');
            $this->db->join('contracts', 'contracts.id = users.contract', 'left outer');
            $this->db->where('users.id = ', $id);
        return $this->db->get()->row_array();
        }
    }
    
    /**
     * Get the list of employees or one employee
     * @param int $id optional id of the entity, all entities if 0
     * @param bool $children true : include sub entities, false otherwise
     * @return array record of users
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function employeesEntity($id = 0, $children = TRUE) {
        $entities = null;
        $this->db->select('users.id as id,'
                . ' users.firstname as firstname,'
                . ' users.lastname as lastname,'
                . ' users.email as email,'
                . ' contracts.name as contract,'
                . ' CONCAT_WS(\' \',managers.firstname,  managers.lastname) as manager_name', FALSE);
        $this->db->from('users');
        $this->db->join('contracts', 'contracts.id = users.contract', 'left outer');
        $this->db->join('users as managers', 'managers.id = users.manager', 'left outer');

        if ($id != 0) {
            $this->db->join('organization', 'organization.id = users.organization');
            if ($children == true) {
                $this->load->model('organization_model');
                $list = $this->organization_model->get_all_children($id);
                $ids = array();
                if (count($list) > 0) {
                    if ($list[0]['id'] != '') {
                        $ids = explode(",", $list[0]['id']);
                    }
                }
                array_push($ids, $id);
                $this->db->where_in('organization.id', $ids);
            } else {
                $this->db->where('organization.id', $id);
            }
        }
        return $this->db->get()->result();
    }

    /**
     * Update a given employee in the database with the contract ID. 
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_contract() {
        $data = array(
            'contract' => $this->input->post('contract')
        );
        $this->db->where('id', $this->input->post('id'));
        $result = $this->db->update('users', $data);
        
        //Trace the modification if the feature is enabled
        if ($this->config->item('enable_history') == TRUE) {
            $this->load->model('history_model');
            $this->history_model->set_history(2, 'users', $id, $this->session->userdata('id'));
        }
        
        return $result;
    }
    
    /**
     * Update a given employee in the database with the manager ID. 
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_manager() {
        $data = array(
            'manager' => $this->input->post('manager')
        );
        $this->db->where('id', $this->input->post('id'));
        $result = $this->db->update('users', $data);
        
        //Trace the modification if the feature is enabled
        if ($this->config->item('enable_history') == TRUE) {
            $this->load->model('history_model');
            $this->history_model->set_history(2, 'users', $id, $this->session->userdata('id'));
        }
        
        return $result;
    }
    
    /**
     * Try to return the user information from the login field
     * @param type $login
     * @return User data row or null if no user was found
     */
    public function getUserByLogin($login) {
        $this->db->from('users');
        $this->db->where('login', $login);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            //No match found
            return null;
        } else {
            return $query->row();
        }
    }
}
