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

class Contracts extends CI_Controller {
    
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
        $this->load->model('contracts_model');
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_admin = $this->session->userdata('is_admin');
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
        $this->language = $this->session->userdata('language');
        $this->language_code = $this->session->userdata('language_code');
        $this->load->helper('language');
        $this->lang->load('contract', $this->language);
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
     * Display the list of all contracts defined in the system
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index($filter = 'requested') {
        $this->auth->check_is_granted('list_contracts');
        if ($filter == 'all') {
            $showAll = true;
        } else {
            $showAll = false;
        }
        
        $data = $this->getUserContext();
        $data['filter'] = $filter;
        $data['title'] = lang('contract_index_title');
        $data['contracts'] = $this->contracts_model->get_contracts();
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('contracts/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display details of a given contract
     * @param int $id contract identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function view($id) {
        $this->auth->check_is_granted('view_contract');
        $data = $this->getUserContext();
        $data['contract'] = $this->contracts_model->get_contracts($id);
        if (empty($data['contract'])) {
            show_404();
        }        
        $data['title'] = lang('contract_view_title');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('contracts/view', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display a form that allows updating a given contract
     * @param int $id Contract identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($id) {
        $this->auth->check_is_granted('edit_contract');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('contract_edit_title');
        
        $this->form_validation->set_rules('name', lang('contract_edit_field_name'), 'required|xss_clean');
        $this->form_validation->set_rules('startentdatemonth', lang('contract_edit_field_start_month'), 'required|xss_clean');
        $this->form_validation->set_rules('startentdateday', lang('contract_edit_field_start_day'), 'required|xss_clean');
        $this->form_validation->set_rules('endentdatemonth', lang('contract_edit_field_end_month'), 'required|xss_clean');
        $this->form_validation->set_rules('endentdateday', lang('contract_edit_field_end_day'), 'required|xss_clean');


        $data['contract'] = $this->contracts_model->get_contracts($id);
        if (empty($data['contract'])) {
            show_404();
        }

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('contracts/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $this->contracts_model->update_contract();
            $this->session->set_flashdata('msg', lang('contract_edit_msg_success'));
            redirect('contracts');
        }
    }
    
    /**
     * Display the form / action Create a new contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        $this->auth->check_is_granted('create_contract');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('contract_create_title');

        $this->form_validation->set_rules('name', lang('contract_create_field_name'), 'required|xss_clean');
        $this->form_validation->set_rules('startentdatemonth', lang('contract_create_field_start_month'), 'required|xss_clean');
        $this->form_validation->set_rules('startentdateday', lang('contract_create_field_start_day'), 'required|xss_clean');
        $this->form_validation->set_rules('endentdatemonth', lang('contract_create_field_end_month'), 'required|xss_clean');
        $this->form_validation->set_rules('endentdateday', lang('contract_create_field_end_day'), 'required|xss_clean');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('contracts/create', $data);
            $this->load->view('templates/footer');
        } else {
            $this->contracts_model->set_contracts();
            log_message('info', 'contract ' . $this->input->post('name') . ' has been created by user #' . $this->session->userdata('id'));
            $this->session->set_flashdata('msg', lang('contract_create_msg_success'));
            redirect('contracts');
        }
    }
    
    /**
     * Delete a given contract
     * @param int $id contract identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) {
        $this->auth->check_is_granted('delete_contract');
        //Test if user exists
        $data['contract'] = $this->contracts_model->get_contracts($id);
        if (empty($data['contract'])) {
            log_message('debug', '{controllers/contracts/delete} user not found');
            show_404();
        } else {
            $this->contracts_model->delete_contract($id);
        }
        log_message('info', 'contract #' . $id . ' has been deleted by user #' . $this->session->userdata('id'));
        $this->session->set_flashdata('msg', lang('contract_delete_msg_success'));
        redirect('contracts');
    }
    
    /**
     * Display an interactive calendar that allows to dynamically set the days
     * off, bank holidays, etc. for a given contract
     * @param type $id contract identifier
     * @param type $year optional year number (4 digits), current year if empty
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function calendar($id, $year = 0) {
        $this->auth->check_is_granted('calendar_contract');
        $data = $this->getUserContext();
        $data['title'] = lang('contract_calendar_title');
        if ($year <> 0) {
            $data['year'] = $year;
        } else {
            $data['year'] = date("Y");
        }
        $data['contract_id'] = $id;
        $data['dayoffs'] = $this->contracts_model->get_dayoffs($id, $data['year']);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('contracts/calendar', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Ajax endpoint : add a day off to a contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function adddayoff() {
        if ($this->auth->is_granted('adddayoff_contract') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $contract = $this->input->post('contract', TRUE);
            $timestamp = $this->input->post('timestamp', TRUE);
            $type = $this->input->post('type', TRUE);
            $title = $this->input->post('title', TRUE);  
            if (isset($contract) && isset($timestamp) && isset($type) && isset($title)) {
                $this->output->set_content_type('text/plain');
                echo $this->contracts_model->adddayoff($contract, $timestamp, $type, $title);
            } else {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            }
        }
    }

    /**
     * Ajax endpoint : delete a day off to a contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deletedayoff() {
        if ($this->auth->is_granted('deletedayoff_contract') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $contract = $this->input->post('contract', TRUE);
            $timestamp = $this->input->post('timestamp', TRUE); 
            if (isset($contract) && isset($timestamp)) {
                $this->output->set_content_type('text/plain');
                echo $this->contracts_model->deletedayoff($contract, $timestamp);
            } else {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            }
        }
    }
    
    /**
     * Action: export the list of all contracts into an Excel file
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export() {
        $this->auth->check_is_granted('export_contracts');
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle(lang('contract_index_title'));
        $this->excel->getActiveSheet()->setCellValue('A1', lang('contract_export_thead_id'));
        $this->excel->getActiveSheet()->setCellValue('B1', lang('contract_export_thead_name'));
        $this->excel->getActiveSheet()->setCellValue('C1', lang('contract_export_thead_start'));
        $this->excel->getActiveSheet()->setCellValue('D1', lang('contract_export_thead_end'));
        $this->excel->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $users = $this->contracts_model->get_contracts();
        $line = 2;
        foreach ($users as $user) {
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $user['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $user['name']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $user['startentdate']);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $user['endentdate']);
            $line++;
        }

        $filename = 'contracts.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
}
