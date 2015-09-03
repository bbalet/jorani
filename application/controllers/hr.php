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
            $employees = $this->users_model->employeesEntity($id, $children);
            $msg = '{"iTotalRecords":' . count($employees);
            $msg .= ',"iTotalDisplayRecords":' . count($employees);
            $msg .= ',"aaData":[';
            foreach ($employees as $employee) {
                $msg .= '["' . $employee->id . '",';
                $msg .= '"' . $employee->firstname . '",';
                $msg .= '"' . $employee->lastname . '",';
                $msg .= '"' . $employee->email . '",';
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
        $this->lang->load('datatable', $this->language);
        $data['title'] = lang('hr_leaves_title');
        $data['user_id'] = $id;
        $this->load->model('leaves_model');
        $data['leaves'] = $this->leaves_model->get_employee_leaves($id);
        $this->load->model('users_model');
        $data['name'] = $this->users_model->get_label($id);
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
        $this->lang->load('datatable', $this->language);
        $data['title'] = lang('hr_overtime_title');
        $data['user_id'] = $id;
        $this->load->model('overtime_model');
        $data['extras'] = $this->overtime_model->get_employee_extras($id);
        $this->load->model('users_model');
        $data['name'] = $this->users_model->get_label($id);
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
        $data['summary'] = $this->leaves_model->get_user_leaves_summary($id, FALSE, $refDate);
        //$this->output->enable_profiler(TRUE);
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
            
            expires_now();
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
        expires_now();
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
                    $data['credit'] = $this->leaves_model->get_user_leaves_credit($id, $type['name']);
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
            $leave_id = $this->leaves_model->set_leaves($id);
            $this->session->set_flashdata('msg', lang('hr_leaves_create_flash_msg_success'));
            //No mail is sent, because the HR Officer would set the leave status to accepted
            redirect('hr/employees');
        }
    }
    
    /**
     * Display presence details for a given employee
     * @param int $id employee id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function presence($id, $month=0, $year=0) {
        $this->auth->check_is_granted('list_employees');
        $data = getUserContext($this);
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
        $data['leave_duration'] = $this->leaves_model->monthly_leaves_duration($data['linear']);
        $data['work_duration'] = $opened_days - $data['leave_duration'];
        
        //List of accepted leave requests taken into account
        $data['leaves'] = $this->leaves_model->get_accepted_leaves_in_dates($id, $start, $end);
        
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('hr/presence', $data);
        $this->load->view('templates/footer');
    }
        
    /**
     * Action: export the list of all leaves into an Excel file
     * @param int $id employee id
     */
    public function export_leaves($id) {
        $this->load->library('excel');
        $sheet = $this->excel->setActiveSheetIndex(0);
        $sheet->setTitle(lang('hr_export_leaves_title'));
        $sheet->setCellValue('A3', lang('hr_export_leaves_thead_id'));
        $sheet->setCellValue('B3', lang('hr_export_leaves_thead_status'));
        $sheet->setCellValue('C3', lang('hr_export_leaves_thead_start'));
        $sheet->setCellValue('D3', lang('hr_export_leaves_thead_end'));
        $sheet->setCellValue('E3', lang('hr_export_leaves_thead_duration'));
        $sheet->setCellValue('F3', lang('hr_export_leaves_thead_type'));
        
        $sheet->getStyle('A3:F3')->getFont()->setBold(true);
        $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->load->model('leaves_model');
        $leaves = $this->leaves_model->get_employee_leaves($id);
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
     */
    public function export_overtime($id) {
        $this->load->library('excel');
        $sheet = $this->excel->setActiveSheetIndex(0);
        $sheet->setTitle(lang('hr_export_overtime_title'));
        $sheet->setCellValue('A3', lang('hr_export_overtime_thead_id'));
        $sheet->setCellValue('B3', lang('hr_export_overtime_thead_status'));
        $sheet->setCellValue('C3', lang('hr_export_overtime_thead_date'));
        $sheet->setCellValue('D3', lang('hr_export_overtime_thead_duration'));
        $sheet->setCellValue('E3', lang('hr_export_overtime_thead_cause'));
        $sheet->getStyle('A3:E3')->getFont()->setBold(true);
        $sheet->getStyle('A3:E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->load->model('overtime_model');
        $requests = $this->overtime_model->get_employee_extras($id);
        $this->load->model('users_model');
        $fullname = $this->users_model->get_label($id);
        $sheet->setCellValue('A1', $fullname);
        
        $line = 4;
        foreach ($requests as $request) {
            $date = new DateTime($request['date']);
            $startdate = $date->format(lang('global_date_format'));
            $sheet->setCellValue('A' . $line, $request['id']);
            $sheet->setCellValue('B' . $line, lang($request['status']));
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
     */
    public function export_employees($id = 0, $children = TRUE) {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $sheet->setTitle(lang('hr_export_employees_title'));
        $sheet->setCellValue('A1', lang('hr_export_employees_thead_id'));
        $sheet->setCellValue('B1', lang('hr_export_employees_thead_firstname'));
        $sheet->setCellValue('C1', lang('hr_export_employees_thead_lastname'));
        $sheet->setCellValue('D1', lang('hr_export_employees_thead_email'));
        $sheet->setCellValue('E1', lang('hr_export_employees_thead_contract'));
        $sheet->setCellValue('F1', lang('hr_export_employees_thead_manager'));
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $children = filter_var($children, FILTER_VALIDATE_BOOLEAN);
        $this->load->model('users_model');
        $employees = $this->users_model->employeesEntity($id, $children);
        
        $line = 2;
        foreach ($employees as $employee) {
            $sheet->setCellValue('A' . $line, $employee->id);
            $sheet->setCellValue('B' . $line, $employee->firstname);
            $sheet->setCellValue('C' . $line, $employee->lastname);
            $sheet->setCellValue('D' . $line, $employee->email);
            $sheet->setCellValue('E' . $line, $employee->contract);
            $sheet->setCellValue('F' . $line, $employee->manager_name);
            $line++;
        }
        
        //Autofit
        foreach(range('A', 'F') as $colD) {
            $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
        }

        $filename = 'employees.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
}