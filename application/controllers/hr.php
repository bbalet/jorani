<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hr extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->load->model('leaves_model');
        $this->lang->load('hr', $this->language);
        $this->lang->load('global', $this->language);
    }
    
    /**
     * Display the list of all employees
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function employees() {
        $this->auth->checkIfOperationIsAllowed('list_employees');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['title'] = lang('hr_employees_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_list_employees');
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, true);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('hr/employees', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Returns the list of the employees attached to an entity
     * Prints the table content in a JSON format expected by jQuery Datatable
     * @param int $id optional id of the entity, all entities if 0
     * @param bool $children true : include sub entities, false otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function employees_entity($id = 0, $children = TRUE) {
        header("Content-Type: application/json");
        if ($this->auth->isAllowed('list_employees') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $children = filter_var($children, FILTER_VALIDATE_BOOLEAN);
            $this->load->model('users_model');
            $employees = $this->users_model->employeesOfEntity($id, $children);
            $msg = '{"iTotalRecords":' . count($employees);
            $msg .= ',"iTotalDisplayRecords":' . count($employees);
            $msg .= ',"aaData":[';
            foreach ($employees as $employee) {
                $msg .= '["' . $employee->id . '",';
                $msg .= '"' . $employee->firstname . '",';
                $msg .= '"' . $employee->lastname . '",';
                $msg .= '"' . $employee->email . '",';
                $msg .= '"' . $employee->entity . '",';
                $msg .= '"' . $employee->contract . '",';
                $msg .= '"' . $employee->manager_name . '"';
                $msg .= '],';
            }
            $msg = rtrim($msg, ",");
            $msg .= ']}';
            echo $msg;
        }
    }

    /**
     * Display the list of leaves for a given employee
     * @param int $id employee id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function leaves($id) {
        $this->auth->checkIfOperationIsAllowed('list_employees');
        $data = getUserContext($this);
        $this->load->model('users_model');
        $data['name'] = $this->users_model->getName($id);
        //Check if exists
        if ($data['name'] == "") {
            show_404();
        }
        $this->lang->load('datatable', $this->language);
        $data['title'] = lang('hr_leaves_title');
        $data['user_id'] = $id;
        $this->load->model('leaves_model');
        $data['leaves'] = $this->leaves_model->getLeavesOfEmployee($id);
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, true);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('hr/leaves', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the list of overtime requests for a given employee
     * @param int $id employee id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function overtime($id) {
        $this->auth->checkIfOperationIsAllowed('list_employees');
        $data = getUserContext($this);
        $this->load->model('users_model');
        $data['name'] = $this->users_model->getName($id);
        //Check if exists
        if ($data['name'] == "") {
            show_404();
        }
        $this->lang->load('datatable', $this->language);
        $data['title'] = lang('hr_overtime_title');
        $data['user_id'] = $id;
        $this->load->model('overtime_model');
        $data['extras'] = $this->overtime_model->getExtrasOfEmployee($id);
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, true);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('hr/overtime', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the details of leaves taken/entitled for a given employee
     * @param string $refTmp Timestamp (reference date)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function counters($id, $refTmp = NULL) {
        $this->auth->checkIfOperationIsAllowed('list_employees');
        $data = getUserContext($this);
        $this->lang->load('entitleddays', $this->language);
        $this->lang->load('datatable', $this->language);
        $refDate = date("Y-m-d");
        if ($refTmp != NULL) {
            $refDate = date("Y-m-d", $refTmp);
            $data['isDefault'] = 0;
        } else {
            $data['isDefault'] = 1;
        }
        
        $data['refDate'] = $refDate;
        $data['summary'] = $this->leaves_model->getLeaveBalanceForEmployee($id, FALSE, $refDate);
        if (!is_null($data['summary'])) {
            $this->load->model('entitleddays_model');
            $this->load->model('users_model');
            $user = $this->users_model->getUsers($id);
            $data['employee_name'] = $user['firstname'] . ' ' . $user['lastname'];
            $this->load->model('contracts_model');
            $contract = $this->contracts_model->getContracts($user['contract']); 
            $data['contract_name'] = $contract['name'];
            $data['contract_start'] = $contract['startentdate'];
            $data['contract_end'] = $contract['endentdate'];
            $data['employee_id'] = $id;
            $data['contract_id'] = $user['contract'];
            $data['entitleddayscontract'] = $this->entitleddays_model->get_entitleddays_contract($user['contract']);
            $data['entitleddaysemployee'] = $this->entitleddays_model->get_entitleddays_employee($id);
            $data['title'] = lang('hr_summary_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_leave_balance_employee');
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('hr/counters', $data);
            $this->load->view('templates/footer');
        } else {
            $this->session->set_flashdata('msg', lang('hr_summary_flash_msg_error'));
            redirect('hr/employees');
        }
    }
    
    /**
     * Create a leave request in behalf of an employee
     * @param int $id Identifier of the employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function createleave($id) {
        $this->auth->checkIfOperationIsAllowed('list_employees');
        $data = getUserContext($this);
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('hr_leaves_create_title');
        $data['form_action'] = 'hr/leaves/create/' . $id;
        $data['source'] = 'hr/employees';
        $data['employee'] = $id;
        
        $this->form_validation->set_rules('startdate', lang('hr_leaves_create_field_start'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('startdatetype', 'Start Date type', 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('enddate', lang('hr_leaves_create_field_end'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('enddatetype', 'End Date type', 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('duration', lang('hr_leaves_create_field_duration'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('type', lang('hr_leaves_create_field_type'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('cause', lang('hr_leaves_create_field_cause'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('status', lang('hr_leaves_create_field_status'), 'required|xss_clean|strip_tags');

        $data['credit'] = 0;
        if ($this->form_validation->run() === FALSE) {
            $this->load->model('types_model');
            $data['types'] = $this->types_model->getTypes();
            foreach ($data['types'] as $type) {
                if ($type['id'] == 0) {
                    $data['credit'] = $this->leaves_model->getLeavesTypeBalanceForEmployee($id, $type['name']);
                    break;
                }
            }
            $this->load->model('users_model');
            $data['name'] = $this->users_model->getName($id);
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('hr/createleave');
            $this->load->view('templates/footer');
        } else {
            $this->leaves_model->setLeaves($id);   //Return not used
            $this->session->set_flashdata('msg', lang('hr_leaves_create_flash_msg_success'));
            //No mail is sent, because the HR Officer would set the leave status to accepted
            redirect('hr/employees');
        }
    }
    
    /**
     * Display presence details for a given employee
     * @param string $source page calling the report (employees, collaborators)
     * @param int $id employee id
     * @param int $month Month number or 0 for last month (default)
     * @param int $year Year number or 0 for current year (default)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function presence($source, $id, $month=0, $year=0) {
        if ($source == 'collaborators') { $this->auth->checkIfOperationIsAllowed('list_collaborators'); }
        if ($source == 'employees') { $this->auth->checkIfOperationIsAllowed('list_employees'); }
        $data = getUserContext($this);
        if ($source == 'collaborators') { $data['source'] = 'collaborators'; }
        if ($source == 'employees') { $data['source'] = 'employees'; }
        $this->lang->load('datatable', $this->language);
        $this->lang->load('calendar', $this->language);
        $data['title'] = lang('hr_presence_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_presence_report');
        
        $data['user_id'] = $id;
        $this->load->model('leaves_model');
        $this->load->model('users_model');
        $this->load->model('dayoffs_model');
        $this->load->model('contracts_model');
        
        //Details about the employee
        $employee = $this->users_model->getUsers($id);
        if (($this->user_id != $employee['manager']) && ($this->is_hr === false)) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to access to hr/presence  #' . $id);
            $this->session->set_flashdata('msg', sprintf(lang('global_msg_error_forbidden'), 'hr/presence'));
            redirect('leaves');
        }
        $data['employee_name'] =  $employee['firstname'] . ' ' . $employee['lastname'];
        $contract = $this->contracts_model->getContracts($employee['contract']);
        if (!empty($contract)) {
            $data['contract_id'] = $contract['id'];
            $data['contract_name'] = $contract['name'];
        } else {
            $data['contract_id'] = '';
            $data['contract_name'] = '';
        }
        
        //Compute facts about dates and the selected month
        if ($month == 0) $month = date('m', strtotime('last month'));
        if ($year == 0) $year = date('Y', strtotime('last month'));
        $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $start = sprintf('%d-%02d-01', $year, $month);
        $lastDay = date("t", strtotime($start));    //last day of selected month
        $end = sprintf('%d-%02d-%02d', $year, $month, $lastDay);
        //Number of non working days during the selected month
        $non_working_days = $this->dayoffs_model->sumdayoffs($employee['contract'], $start, $end);
        $opened_days = $total_days - $non_working_days;
        $data['month'] = $month;
        $data['month_name'] = date('F', strtotime($start));
        $data['year'] = $year;
        $data['default_date'] = $start;
        $data['total_days'] = $total_days;
        $data['opened_days'] = $opened_days;
        $data['non_working_days'] = $non_working_days;
        
        //tabular view of the leaves
        $data['linear'] = $this->leaves_model->linear($id, $month, $year, FALSE, FALSE, TRUE, FALSE);
        $data['leave_duration'] = $this->leaves_model->monthlyLeavesDuration($data['linear']);
        $data['work_duration'] = $opened_days - $data['leave_duration'];
        $data['leaves_detail'] = $this->leaves_model->monthlyLeavesByType($data['linear']);
        
        //List of accepted leave requests
        $data['leaves'] = $this->leaves_model->getAcceptedLeavesBetweenDates($id, $start, $end);
        
        //Leave balance of the employee
        $data['employee_id'] = $id;
        $refDate = new DateTime($end);
        $data['refDate'] = $refDate->format(lang('global_date_format'));
        $data['summary'] = $this->leaves_model->getLeaveBalanceForEmployee($id, FALSE, $end);
        
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('hr/presence', $data);
        $this->load->view('templates/footer');
    }
        
    /**
     * Export the list of all leave requests of an employee into an Excel file
     * @param int $id employee id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function exportLeaves($id) {
        $this->load->library('excel');
        $this->load->model('leaves_model');
        $this->load->model('users_model');
        $data['id'] = $id;
        $this->load->view('hr/export_leaves', $data);
    }
    
    /**
     * Export the list of all overtime requests of an employee into an Excel file
     * @param int $id employee id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function exportOvertime($id) {
        $this->load->library('excel');
        $this->load->model('overtime_model');
        $this->load->model('users_model');
        $data['id'] = $id;
        $this->load->view('hr/export_overtime', $data);
    }
    
    /**
     * Export the list of all employees into an Excel file
     * @param int $id optional id of the entity, all entities if 0
     * @param bool $children true : include sub entities, false otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function exportEmployees($id = 0, $children = TRUE) {
        $this->load->model('users_model');
        $this->load->library('excel');
        $data['id'] = $id;
        $data['children'] = filter_var($children, FILTER_VALIDATE_BOOLEAN);
        $this->load->view('hr/export_employees', $data);
    }
    
    /**
     * Action: export the presence details for a given employee
     * @param string $source page calling the report (employees, collaborators)
     * @param int $id employee id
     * @param int $month Month number or 0 for last month (default)
     * @param int $year Year number or 0 for current year (default)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function exportPresence($source,$id, $month=0, $year=0) {
        if ($source == 'collaborators') { $this->auth->checkIfOperationIsAllowed('list_collaborators'); }
        if ($source == 'employees') { $this->auth->checkIfOperationIsAllowed('list_employees'); }
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->load->model('leaves_model');
        $this->load->model('users_model');
        $this->load->model('dayoffs_model');
        $this->load->model('contracts_model');
        
        $employee = $this->users_model->getUsers($id);
        if (($this->user_id != $employee['manager']) && ($this->is_hr === false)) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to access to hr/presence  #' . $id);
            $this->session->set_flashdata('msg', sprintf(lang('global_msg_error_forbidden'), 'hr/presence'));
            redirect('leaves');
        }
        
        $this->load->library('excel');       
        $data['employee'] = $employee;
        $data['month'] = $month;
        $data['year'] = $year;
        $data['id'] = $id;
        $this->load->view('hr/export_presence', $data);
    }
}
