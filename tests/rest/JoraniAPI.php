<?php
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

/**
 * JoraniAPI is a wrapper for the REST API of Jorani
 * 
 * Please note that oauth_clients table must be initialised prior using the REST API
 * 
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
class JoraniAPI {
    private $base_url = 'http://localhost/jorani/';
    private $token = NULL;
    
    /**
     * Constructor of JoraniAPI. Set a base URL and get an OAtuh2 token
     * @param string $url base URL of the REST API
     * @param string $username username of OAtuh2 user
     * @param string $password password of OAtuh2 user
     */
    function __construct($url, $username, $password) {
        $this->base_url = $url;
        $this->token = $this->getToken($username, $password);
    }

    /**
     * Get an OAuth2 token
     * @param string $username username of OAtuh2 user
     * @param string $password password of OAtuh2 user
     * @return string Authentication Token
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function getToken($username, $password) {
        $url = $this->base_url . 'api/token';
        $data = array('grant_type' => 'client_credentials');
        $cred = sprintf('Authorization: Basic %s', base64_encode("$username:$password"));
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n" . $cred ."\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $result_array = json_decode($result);
        $token = $result_array->access_token;
        return $token;
    }
    
    /**
     * Get the list of employess
     * @return array list of employees
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getEmployeesList() {
        $url = $this->base_url . 'api/users';
        $data = array('access_token' => $this->token);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $result_array = json_decode($result);
        return $result_array;
    }
    
    /**
     * Return the list of entitled days credited to an employee
     * @param int $employee Identifier of the employee into the database
     * @return array list of entitled days
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getEntitledDaysEmployeeList($employee) {
        $url = $this->base_url . 'api/entitleddaysemployee/' . $employee;
        $data = array('access_token' => $this->token);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $result_array = json_decode($result);
        return $result_array;
    }
    
    /**
     * Add entitled days to an employee
     * @param int $employee Identifier of the employee into the database
     * @param string $startdate String formatted start date
     * @param string $enddate String formatted end date
     * @param int $days Number of days to be credited
     * @param int $type Leave type
     * @param string $description Description of the record (eg 'inserted by Robot')
     * @return int Id of the inserted record
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function addEntitledDaysEmployee($employee, $startdate, $enddate, $days, $type, $description) {
        $url = $this->base_url . 'api/addentitleddaysemployee/' . $employee;
        $data = array('access_token' => $this->token,
                'startdate' => $startdate,
                'enddate' => $enddate,
                'days' => $days,
                'type' => $type,
                'description' => $description,);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        //Get the result (of the SQL execution) => it should be the last inserted ID
        $result_int = json_decode($result);
        return $result_int;
    }
}
