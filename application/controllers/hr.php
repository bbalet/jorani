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
        $this->auth->check_is_granted('list_employees');
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
        if ($this->auth->is_granted('list_employees') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $children = filter_var($children, FILTER_VALIDATE_BOOLEAN);
            $this->load->model('users_model');
            $employees = $this->users_model->employees_of_entity($id, $children);
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
        $this->auth->check_is_granted('list_employees');
        $data = getUserContext($this);
        $this->load->model('users_model');
        $data['name'] = $this->users_model->get_label($id);
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
        $this->auth->check_is_granted('list_employees');
        $data = getUserContext($this);
        $this->load->model('users_model');
        $data['name'] = $this->users_model->get_label($id);
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
        $this->auth->check_is_granted('list_employees');
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
            $this->load->model('types_model');
            $data['types'] = $this->types_model->get_types();
            $this->load->model('users_model');
            $data['employee_name'] = $this->users_model->get_label($id);
            $user = $this->users_model->get_users($id);
            $this->load->model('contracts_model');
            $contract = $this->contracts_model->get_contracts($user['contract']); 
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
        $this->auth->check_is_granted('list_employees');
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
            $data['types'] = $this->types_model->get_types();
            foreach ($data['types'] as $type) {
                if ($type['id'] == 0) {
                    $data['credit'] = $this->leaves_model->getLeavesTypeBalanceForEmployee($id, $type['name']);
                    break;
                }
            }
            $this->load->model('users_model');
            $data['name'] = $this->users_model->get_label($id);
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
        if ($source == 'collaborators') { $this->auth->check_is_granted('list_collaborators'); }
        if ($source == 'employees') { $this->auth->check_is_granted('list_employees'); }
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
        $employee = $this->users_model->get_users($id);
        if (($this->user_id != $employee['manager']) && ($this->is_hr === false)) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to access to hr/presence  #' . $id);
            $this->session->set_flashdata('msg', sprintf(lang('global_msg_error_forbidden'), 'hr/presence'));
            redirect('leaves');
        }
        $data['employee_name'] =  $employee['firstname'] . ' ' . $employee['lastname'];
        $contract = $this->contracts_model->get_contracts($employee['contract']);
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
     * Action: export the list of all leaves into an Excel file
     * @param int $id employee id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export_leaves($id) {
        $this->load->library('excel');
        $sheet = $this->excel->setActiveSheetIndex(0);
        $sheet->setTitle(mb_strimwidth(lang('hr_export_leaves_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
        $sheet->setCellValue('A3', lang('hr_export_leaves_thead_id'));
        $sheet->setCellValue('B3', lang('hr_export_leaves_thead_status'));
        $sheet->setCellValue('C3', lang('hr_export_leaves_thead_start'));
        $sheet->setCellValue('D3', lang('hr_export_leaves_thead_end'));
        $sheet->setCellValue('E3', lang('hr_export_leaves_thead_duration'));
        $sheet->setCellValue('F3', lang('hr_export_leaves_thead_type'));
        
        $sheet->getStyle('A3:F3')->getFont()->setBold(true);
        $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->load->model('leaves_model');
        $leaves = $this->leaves_model->getLeavesOfEmployee($id);
        $this->load->model('users_model');
        $fullname = $this->users_model->get_label($id);
        $sheet->setCellValue('A1', $fullname);
        
        $line = 4;
        foreach ($leaves as $leave) {
            $date = new DateTime($leave['startdate']);
            $startdate = $date->format(lang('global_date_format'));
            $date = new DateTime($leave['enddate']);
            $enddate = $date->format(lang('global_date_format'));
            $sheet->setCellValue('A' . $line, $leave['id']);
            $sheet->setCellValue('B' . $line, lang($leave['status_name']));
            $sheet->setCellValue('C' . $line, $startdate);
            $sheet->setCellValue('D' . $line, $enddate);
            $sheet->setCellValue('E' . $line, $leave['duration']);
            $sheet->setCellValue('F' . $line, $leave['type_name']);
            $line++;
        }
        
        //Autofit
        foreach(range('A', 'F') as $colD) {
            $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
        }

        $filename = 'leaves.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
    
    /**
     * Action: export the list of all overtime requests into an Excel file
     * @param int $id employee id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export_overtime($id) {
        $this->load->library('excel');
        $sheet = $this->excel->setActiveSheetIndex(0);
        $sheet->setTitle(mb_strimwidth(lang('hr_export_overtime_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
        $sheet->setCellValue('A3', lang('hr_export_overtime_thead_id'));
        $sheet->setCellValue('B3', lang('hr_export_overtime_thead_status'));
        $sheet->setCellValue('C3', lang('hr_export_overtime_thead_date'));
        $sheet->setCellValue('D3', lang('hr_export_overtime_thead_duration'));
        $sheet->setCellValue('E3', lang('hr_export_overtime_thead_cause'));
        $sheet->getStyle('A3:E3')->getFont()->setBold(true);
        $sheet->getStyle('A3:E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->load->model('overtime_model');
        $requests = $this->overtime_model->getExtrasOfEmployee($id);
        $this->load->model('users_model');
        $fullname = $this->users_model->get_label($id);
        $sheet->setCellValue('A1', $fullname);
        
        $line = 4;
        foreach ($requests as $request) {
            $date = new DateTime($request['date']);
            $startdate = $date->format(lang('global_date_format'));
            $sheet->setCellValue('A' . $line, $request['id']);
            $sheet->setCellValue('B' . $line, lang($request['status_name']));
            $sheet->setCellValue('C' . $line, $startdate);
            $sheet->setCellValue('D' . $line, $request['duration']);
            $sheet->setCellValue('E' . $line, $request['cause']);
            $line++;
        }

        //Autofit
        foreach(range('A', 'E') as $colD) {
            $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
        }
        
        $filename = 'overtime.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
    
    /**
     * Action: export the list of all employees into an Excel file
     * @param int $id optional id of the entity, all entities if 0
     * @param bool $children true : include sub entities, false otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export_employees($id = 0, $children = TRUE) {
        $this->load->library('excel');
        $sheet = $this->excel->setActiveSheetIndex(0);
        $sheet->setTitle(mb_strimwidth(lang('hr_export_employees_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
        $sheet->setCellValue('A1', lang('hr_export_employees_thead_id'));
        $sheet->setCellValue('B1', lang('hr_export_employees_thead_firstname'));
        $sheet->setCellValue('C1', lang('hr_export_employees_thead_lastname'));
        $sheet->setCellValue('D1', lang('hr_export_employees_thead_email'));
        $sheet->setCellValue('E1', lang('hr_export_employees_thead_entity'));
        $sheet->setCellValue('F1', lang('hr_export_employees_thead_contract'));
        $sheet->setCellValue('G1', lang('hr_export_employees_thead_manager'));
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $children = filter_var($children, FILTER_VALIDATE_BOOLEAN);
        $this->load->model('users_model');
        $employees = $this->users_model->employees_of_entity($id, $children);
        
        $line = 2;
        foreach ($employees as $employee) {
            $sheet->setCellValue('A' . $line, $employee->id);
            $sheet->setCellValue('B' . $line, $employee->firstname);
            $sheet->setCellValue('C' . $line, $employee->lastname);
            $sheet->setCellValue('D' . $line, $employee->email);
            $sheet->setCellValue('E' . $line, $employee->entity);
            $sheet->setCellValue('F' . $line, $employee->contract);
            $sheet->setCellValue('G' . $line, $employee->manager_name);
            $line++;
        }
        
        //Autofit
        foreach(range('A', 'G') as $colD) {
            $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
        }

        $filename = 'employees.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
    
    /**
     * Action: export the presence details for a given employee
     * @param string $source page calling the report (employees, collaborators)
     * @param int $id employee id
     * @param int $month Month number or 0 for last month (default)
     * @param int $year Year number or 0 for current year (default)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export_presence($source,$id, $month=0, $year=0) {
        if ($source == 'collaborators') { $this->auth->check_is_granted('list_collaborators'); }
        if ($source == 'employees') { $this->auth->check_is_granted('list_employees'); }
        setUserContext($this);
        
        $this->lang->load('calendar', $this->language);
        $this->load->model('leaves_model');
        $this->load->model('users_model');
        $this->load->model('dayoffs_model');
        $this->load->model('contracts_model');
        $this->load->library('excel');
        
        //Details about the employee
        $employee = $this->users_model->get_users($id);
        if (($this->user_id != $employee['manager']) && ($this->is_hr === false)) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to access to hr/presence  #' . $id);
            $this->session->set_flashdata('msg', sprintf(lang('global_msg_error_forbidden'), 'hr/presence'));
            redirect('leaves');
        }
        $employee_name =  $employee['firstname'] . ' ' . $employee['lastname'];
        $contract = $this->contracts_model->get_contracts($employee['contract']);
        if (!empty($contract)) {
            $contract_name = $contract['name'];
        } else {
            $contract_name = '';
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
        $month_name = lang(date('F', strtotime($start)));
        
        //tabular view of the leaves
        $linear = $this->leaves_model->linear($id, $month, $year, FALSE, FALSE, TRUE, FALSE);
        $leave_duration = $this->leaves_model->monthlyLeavesDuration($linear);
        $work_duration = $opened_days - $leave_duration;
        $leaves_detail = $this->leaves_model->monthlyLeavesByType($linear);
        //Leave balance of the employee
        $summary = $this->leaves_model->getLeaveBalanceForEmployee($id, FALSE, $end);
        
        //Print the header with the facts of the presence report
        $sheet = $this->excel->setActiveSheetIndex(0);
        $sheet->setTitle(mb_strimwidth(lang('hr_presence_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
        
        $sheet->setCellValue('A1', lang('hr_presence_employee'));
        $sheet->setCellValue('A2', lang('hr_presence_month'));
        $sheet->setCellValue('A3', lang('hr_presence_days'));
        $sheet->setCellValue('A4', lang('hr_presence_contract'));
        $sheet->setCellValue('A5', lang('hr_presence_working_days'));
        $sheet->setCellValue('A6', lang('hr_presence_non_working_days'));
        $sheet->setCellValue('A7', lang('hr_presence_work_duration'));
        $sheet->setCellValue('A8', lang('hr_presence_leave_duration'));
        $sheet->getStyle('A1:A8')->getFont()->setBold(true);
        
        $sheet->setCellValue('B1', $employee_name);
        $sheet->setCellValue('B2', $month_name);
        $sheet->setCellValue('B3', $total_days);
        $sheet->setCellValue('B4', $contract_name);
        $sheet->setCellValue('B5', $opened_days);
        $sheet->setCellValue('B6', $non_working_days);
        $sheet->setCellValue('B7', $work_duration);
        $sheet->setCellValue('B8', $leave_duration);
        
        if (count($leaves_detail) > 0) {
            $line = 9;
            foreach ($leaves_detail as $leaves_type_name => $leaves_type_sum) {
                $sheet->setCellValue('A' . $line, $leaves_type_name);
                $sheet->setCellValue('B' . $line, $leaves_type_sum);
                $sheet->getStyle('A' . $line)->getAlignment()->setIndent(2);
                $line++;
            }
        }

        //Print two lines : the short name of all days for the selected month (horizontally aligned)
        $start = $year . '-' . $month . '-' . '1';    //first date of selected month
        $lastDay = date("t", strtotime($start));    //last day of selected month
        for ($ii = 1; $ii <=$lastDay; $ii++) {
            $dayNum = date("N", strtotime($year . '-' . $month . '-' . $ii));
            $col = $this->excel->column_name(3 + $ii);
            //Print day number
            $sheet->setCellValue($col . '11', $ii);
            //Print short name of the day
            switch ($dayNum)
            {
                case 1: $sheet->setCellValue($col . '10', lang('calendar_monday_short')); break;
                case 2: $sheet->setCellValue($col . '10', lang('calendar_tuesday_short')); break;
                case 3: $sheet->setCellValue($col . '10', lang('calendar_wednesday_short')); break;
                case 4: $sheet->setCellValue($col . '10', lang('calendar_thursday_short')); break;
                case 5: $sheet->setCellValue($col . '10', lang('calendar_friday_short')); break;
                case 6: $sheet->setCellValue($col . '10', lang('calendar_saturday_short')); break;
                case 7: $sheet->setCellValue($col . '10', lang('calendar_sunday_short')); break;
            }
        }
        //The header is horizontally aligned
        $col = $this->excel->column_name(3 + $lastDay);
        $sheet->getStyle('C8:' . $col . '9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        //Box around the lines for each employee
        $styleBox = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
          );
        
        $dayBox =  array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
                    'rgb' => '808080'
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
                    'rgb' => '808080'
                )
            )
         );
        
        //Background colors for the calendar according to the type of leave
        $styleBgPlanned = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'DDD')
            )
        );
        $styleBgRequested = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'F89406')
            )
        );
        $styleBgAccepted = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '468847')
            )
        );
        $styleBgRejected = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FF0000')
            )
        );
        $styleBgDayOff = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000000')
            )
        );
        
        $line = 12;
        $col = $this->excel->column_name($lastDay + 3);
        $sheet->getStyle('D' . $line . ':' . $col . ($line + 1))->applyFromArray($styleBox);

        //Iterate on all days of the selected month
        $dayNum = 0;
        foreach ($linear->days as $day) {
            $dayNum++;
            $col = $this->excel->column_name(3 + $dayNum);
            if (strstr($day->display, ';')) {//Two statuses in the cell
                $statuses = explode(";", $day->status);
                $types = explode(";", $day->type);
                    //0 - Working day  _
                    //1 - All day           []
                    //2 - Morning        |\
                    //3 - Afternoon      /|
                    //4 - All Day Off       []
                    //5 - Morning Day Off   |\
                    //6 - Afternoon Day Off /|
                  $sheet->getComment($col . $line)->getText()->createTextRun($types[0]);
                  $sheet->getComment($col . ($line + 1))->getText()->createTextRun($types[1]);
                  switch (intval($statuses[0]))
                  {
                    case 1: $sheet->getStyle($col . $line)->applyFromArray($styleBgPlanned); break;  // Planned
                    case 2: $sheet->getStyle($col . $line)->applyFromArray($styleBgRequested); break;  // Requested
                    case 3: $sheet->getStyle($col . $line)->applyFromArray($styleBgAccepted); break;  // Accepted
                    case 4: $sheet->getStyle($col . $line)->applyFromArray($styleBgRejected); break;  // Rejected
                    case '5': $sheet->getStyle($col . $line)->applyFromArray($styleBgDayOff); break;    //Day off
                    case '6': $sheet->getStyle($col . $line)->applyFromArray($styleBgDayOff); break;    //Day off
                  }
                  switch (intval($statuses[1]))
                  {
                    case 1: $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgPlanned); break;  // Planned
                    case 2: $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgRequested); break;  // Requested
                    case 3: $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgAccepted); break;  // Accepted
                    case 4: $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgRejected); break;  // Rejected
                    case '5': $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgDayOff); break;    //Day off
                    case '6': $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgDayOff); break;    //Day off
                  }//Two statuses in the cell
            } else {//Only one status in the cell
                switch ($day->display) {
                    case '1':   //All day
                            $sheet->getComment($col . $line)->getText()->createTextRun($day->type);
                            $sheet->getComment($col . ($line + 1))->getText()->createTextRun($day->type);
                            switch ($day->status)
                            {
                                // 1 : 'Planned';
                                // 2 : 'Requested';
                                // 3 : 'Accepted';
                                // 4 : 'Rejected';
                                case 1: $sheet->getStyle($col . $line . ':' . $col . ($line + 1))->applyFromArray($styleBgPlanned); break;  // Planned
                                case 2: $sheet->getStyle($col . $line . ':' . $col . ($line + 1))->applyFromArray($styleBgRequested); break; // Requested
                                case 3: $sheet->getStyle($col . $line . ':' . $col . ($line + 1))->applyFromArray($styleBgAccepted); break;  // Accepted
                                case 4: $sheet->getStyle($col . $line . ':' . $col . ($line + 1))->applyFromArray($styleBgRejected); break;  // Rejected
                            }
                            break;
                    case '2':   //AM
                        $sheet->getComment($col . $line)->getText()->createTextRun($day->type);
                        switch ($day->status)
                          {
                              case 1: $sheet->getStyle($col . $line)->applyFromArray($styleBgPlanned); break;  // Planned
                              case 2: $sheet->getStyle($col . $line)->applyFromArray($styleBgRequested); break;  // Requested
                              case 3: $sheet->getStyle($col . $line)->applyFromArray($styleBgAccepted); break;  // Accepted
                              case 4: $sheet->getStyle($col . $line)->applyFromArray($styleBgRejected); break;  // Rejected
                          }
                        break;
                    case '3':   //PM
                        $sheet->getComment($col . ($line + 1))->getText()->createTextRun($day->type);
                        switch ($day->status)
                          {
                              case 1: $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgPlanned); break;  // Planned
                              case 2: $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgRequested); break;  // Requested
                              case 3: $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgAccepted); break;  // Accepted
                              case 4: $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgRejected); break;  // Rejected
                          }
                        break;
                    case '4': //Full day off
                        $sheet->getStyle($col . $line . ':' . $col . ($line + 1))->applyFromArray($styleBgDayOff);
                        $sheet->getComment($col . $line)->getText()->createTextRun($day->type);
                        $sheet->getComment($col . ($line + 1))->getText()->createTextRun($day->type);
                        break;
                    case '5':  //AM off
                        $sheet->getStyle($col . $line)->applyFromArray($styleBgDayOff);
                        $sheet->getComment($col . $line)->getText()->createTextRun($day->type);
                        break;
                    case '6':   //PM off
                        $sheet->getStyle($col . ($line + 1))->applyFromArray($styleBgDayOff);
                        $sheet->getComment($col . ($line + 1))->getText()->createTextRun($day->type);
                        break;
                }
              }//Only one status in the cell
        }//day
        
        //Autofit for all column containing the days
        for ($ii = 1; $ii <=$lastDay; $ii++) {
            $col = $this->excel->column_name($ii + 3);
            $sheet->getStyle($col . '10:' . $col . '13')->applyFromArray($dayBox);
            $sheet->getColumnDimension($col)->setAutoSize(TRUE);
        }
        $sheet->getColumnDimension('A')->setAutoSize(TRUE);
        $sheet->getColumnDimension('B')->setAutoSize(TRUE);
        
        //Leave Balance
        $sheet->setCellValue('C16', lang('hr_summary_thead_type'));
        $sheet->setCellValue('J16', lang('hr_summary_thead_available'));
        $sheet->setCellValue('P16', lang('hr_summary_thead_taken'));
        $sheet->setCellValue('V16', lang('hr_summary_thead_entitled'));
        $sheet->setCellValue('AB16', lang('hr_summary_thead_description'));
        $sheet->getStyle('C16:AH16')->getFont()->setBold(true);
        $sheet->mergeCells('C16:I16');
        $sheet->mergeCells('J16:O16');
        $sheet->mergeCells('P16:U16');
        $sheet->mergeCells('V16:AA16');
        $sheet->mergeCells('AB16:AK16');
        
        $line = 17;
        foreach ($summary as $key => $value) {
            $sheet->setCellValue('C' . $line, $key);
            $sheet->setCellValue('J' . $line, ((float) $value[1] - (float) $value[0]));
            if ($value[2] == '') {
                $sheet->setCellValue('P' . $line, ((float) $value[0]));
            } else {
                $sheet->setCellValue('P' . $line, '-');
            }
            if ($value[2] == '') {
                $sheet->setCellValue('V' . $line, ((float) $value[1]));
            } else {
                $sheet->setCellValue('V' . $line, '-');
            }
            $sheet->setCellValue('AB' . $line, $value[2]);

            $sheet->getStyle('C' . $line . ':AK' . $line)->applyFromArray($styleBox);
            $sheet->mergeCells('C' . $line . ':I' . $line);
            $sheet->mergeCells('J' . $line . ':O' . $line);
            $sheet->mergeCells('P' . $line . ':U' . $line);
            $sheet->mergeCells('V' . $line . ':AA' . $line);
            $sheet->mergeCells('AB' . $line . ':AK' . $line);

            $line++;
        }

        //Set layout to landscape and make the Excel sheet fit to the page
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        
        $filename = 'presence.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
