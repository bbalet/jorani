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

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hr extends CI_Controller {
    
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
        $this->load->model('leaves_model');
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_admin = $this->session->userdata('is_admin');
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
        $this->language = $this->session->userdata('language');
        $this->language_code = $this->session->userdata('language_code');
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
     * Display the list of all requests submitted to you
     * Status is submitted
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index($filter = 'requested') {
        $this->auth->check_is_granted('list_requests');
        if ($filter == 'all') {
            $showAll = true;
        } else {
            $showAll = false;
        }
        
        $data = $this->getUserContext();
        $data['filter'] = $filter;
        $data['title'] = 'List of requested leaves';
        $data['requests'] = $this->leaves_model->requests($this->user_id, $showAll);
        
        $this->load->model('types_model');
        for ($i = 0; $i < count($data['requests']); ++$i) {
            $data['requests'][$i]['type_label'] = $this->types_model->get_label($data['requests'][$i]['type']);
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('hr/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the list of all employees
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function employees() {
        $this->auth->check_is_granted('list_employees');
        $data = $this->getUserContext();
        $data['title'] = 'List of employees';
        $this->load->model('users_model');
        $data['users'] = $this->users_model->get_employees();
        
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('hr/employees', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display the list of leaves for a given employee
     * @param int $id employee id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function leaves($id) {
        $this->auth->check_is_granted('list_employees');
        $data = $this->getUserContext();
        $data['title'] = 'List of leaves';
        $data['user_id'] = $id;
        $this->load->model('leaves_model');
        $data['leaves'] = $this->leaves_model->get_employee_leaves($id);
        
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('hr/leaves', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display a form that allows updating the contract of a given user
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function contract($id) {
        $this->auth->check_is_granted('employee_contract');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Attach a contract';
        $this->form_validation->set_rules('contract', 'contract', 'required|xss_clean');
        if ($this->form_validation->run() === FALSE) {
            $data['id'] = $id;
            $this->load->model('users_model');
            $data['user'] = $this->users_model->get_users($id);
            $this->load->model('contracts_model');
            $data['contracts'] = $this->contracts_model->get_contracts();
            $this->load->view('hr/contract', $data);
        } else {
            $this->load->model('users_model');
            $this->users_model->set_contract();
            $this->session->set_flashdata('msg', 'The contract has been succesfully attached to the employee');
            redirect('hr/employees');
        }
    }
    
    /**
     * Display a form that allows updating the manager of a given user
     * @param int $id User identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function manager($id) {
        $this->auth->check_is_granted('employee_manager');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Attach a manager';
        $this->form_validation->set_rules('manager', 'manager', 'required|xss_clean');
        if ($this->form_validation->run() === FALSE) {
            $data['id'] = $id;
            $this->load->model('users_model');
            $data['user'] = $this->users_model->get_users($id);
            $data['users'] = $this->users_model->get_users();
            $this->load->view('hr/manager', $data);
        } else {
            $this->load->model('users_model');
            $this->users_model->set_manager();
            $this->session->set_flashdata('msg', 'The manager has been succesfully attached to the employee');
            redirect('hr/employees');
        }
    }
    
    /**
     * Action: export the list of all leaves into an Excel file
     * @param int $id employee id
     */
    public function export_leaves($id) {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('List of leaves');
        $this->excel->getActiveSheet()->setCellValue('A3', 'ID');
        $this->excel->getActiveSheet()->setCellValue('B3', 'Status');
        $this->excel->getActiveSheet()->setCellValue('C3', 'Start Date');
        $this->excel->getActiveSheet()->setCellValue('D3', 'End Date');
        $this->excel->getActiveSheet()->setCellValue('E3', 'Duration');
        $this->excel->getActiveSheet()->setCellValue('F3', 'Type');
        
        $this->excel->getActiveSheet()->getStyle('A3:F3')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A3:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->load->model('leaves_model');
        $leaves = $this->leaves_model->get_employee_leaves($id);
        $this->load->model('users_model');
        $fullname = $this->users_model->get_label($id);
        $this->excel->getActiveSheet()->setCellValue('A1', $fullname);
        
        $line = 4;
        foreach ($leaves as $leave) {
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $leave['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $leave['status']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $leave['startdate']);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $leave['enddate']);
            $this->excel->getActiveSheet()->setCellValue('E' . $line, $leave['duration']);
            $this->excel->getActiveSheet()->setCellValue('F' . $line, $leave['type']);
            $line++;
        }

        $filename = 'leaves.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
}
