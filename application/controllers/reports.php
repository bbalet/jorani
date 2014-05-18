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
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

class Reports extends CI_Controller {
    
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
        $this->lang->load('reports', $this->language);
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
     * List the available reports (all folders into local/reports
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->check_is_granted('report_list');
        $data = $this->getUserContext();
        
        $reports = array();
        $files = glob(FCPATH . '/local/reports/*', GLOB_ONLYDIR);
        foreach($files as $file) {
            $ini_array = parse_ini_file($file . '/report.ini', true);
            $reports[$ini_array[$this->language_code]['name']] = array(
                basename($file),
                $ini_array[$this->language_code]['description']
                );
        }
        
        $data['title'] = lang('reports_index_title');
        $data['reports'] = $reports;
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('reports/index', $data);
        $this->load->view('templates/footer'); 
    }

    /**
     * Execute a report
     * @param string $report Name of the folder containing the report
     * @param string $action PHP file to be executed
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function execute($report, $action = "index.php") {
        $this->auth->check_is_granted('report_execute');
        $data = $this->getUserContext();
        $data['title'] = lang('reports_execute_title');
        $data['report'] = $report;
        $data['action'] = $action;
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('reports/execute', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Execute the shipped-in balance report
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function balance() {
        $this->auth->check_is_granted('native_report_balance');
        $data = $this->getUserContext();
        $data['title'] = 'Leave Balance Report';
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('reports/balance/index', $data);
        $this->load->view('templates/footer');
    }
}
