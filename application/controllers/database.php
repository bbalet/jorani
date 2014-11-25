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
 * along with lms.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Database extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        //Check if user is connected
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_userdata('last_page', current_url());
            redirect('session/login');
        }
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_admin = $this->session->userdata('is_admin');
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
        $this->language = $this->session->userdata('language');
        $this->language_code = $this->session->userdata('language_code');
        $this->load->helper('language');
        $this->lang->load('database', $this->language);
    }
    
    /**
     * Prepare an array containing information about the current user
     * @return array data to be passed to the view
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function getUserContext()
    {
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;
        $data['is_hr'] = $this->is_hr;
        $data['user_id'] =  $this->user_id;
        $data['language'] = $this->language;
        $data['language_code'] =  $this->language_code;
        return $data;
    }

    /**
     * Display the list of tables that can be purged and their number of rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->check_is_granted('purge_database');
        $data = $this->getUserContext();
        
        $this->load->model('dayoffs_model');
        $this->load->model('entitleddays_model');
        $this->load->model('history_model');
        $this->load->model('leaves_model');
        $this->load->model('overtime_model');
        $this->load->model('time_model');
        
        $data['dayoffs_count'] = $this->dayoffs_model->count();
        $data['entitleddays_count'] = $this->entitleddays_model->count();
        $data['leaves_count'] = $this->leaves_model->count();
        $data['overtime_count'] = $this->overtime_model->count();
        
        if ($this->config->item('enable_history') == true) {
            $data['contracts_history_count'] = $this->history_model->count('contracts');
            $data['entitleddays_history_count'] = $this->history_model->count('entitleddays');
            $data['organization_history_count'] = $this->history_model->count('organization');
            $data['overtime_history_count'] = $this->history_model->count('overtime');
            $data['positions_history_count'] = $this->history_model->count('positions');
            $data['types_history_count'] = $this->history_model->count('types');
            $data['users_history_count'] = $this->history_model->count('users');
        }
        
        if ($this->config->item('enable_time') == true) {
            $data['time_count'] = $this->time_model->count();
            if ($this->config->item('enable_history') == true) {
                $data['activities_employee_history_count'] = $this->history_model->count('activities_employee');
                $data['activities_history_count'] = $this->history_model->count('activities');
            }
        }
        
        $data['title'] = lang('database_index_title');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('database/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Purge database action
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function purge() {
        $this->auth->check_is_granted('purge_database');
        $data = $this->getUserContext();
        $this->load->model('history_model');

        //Iterate through the selected tables to be purged
        if (!empty($_POST['chkTable'])) {
            foreach ($_POST['chkTable'] as $table) {
                echo $table . '<br />';
                //$this->history_model->purge_history($table, $_POST['chkTable']);
            }
            die();
        }
        $this->session->set_flashdata('msg', lang('database_purge_msg_success'));
        redirect('database');
    }

}
