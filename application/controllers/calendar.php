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
 */

class Calendar extends CI_Controller {
    
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
        $this->lang->load('calendar', $this->language);
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
     * Display the page of the individual calendar (of the connected user)
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual() {
        $this->auth->check_is_granted('individual_calendar');
        $data = $this->getUserContext();
        $data['title'] = lang('calendar_individual_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_individual');
        $data['googleApi'] = false;
        $data['clientId'] = 'key';
        $data['apiKey'] = 'key';
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/individual', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the page of the team calendar (users having the same manager
     * than the connected user)
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function workmates() {
        $this->auth->check_is_granted('workmates_calendar');
        $data = $this->getUserContext();
        $data['title'] = lang('calendar_workmates_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_workmates');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/workmates', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display the calendar of the employees managed by the connected user
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators() {
        $this->auth->check_is_granted('collaborators_calendar');
        $data = $this->getUserContext();
        $data['title'] = lang('calendar_collaborators_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_collaborators');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/collaborators', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the calendar of the employees working in the same department
     * than the connected user.
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function department() {
        $this->auth->check_is_granted('department_calendar');
        $data = $this->getUserContext();
        $data['title'] = lang('calendar_department_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_department');
        $this->load->model('organization_model');
        $department = $this->organization_model->get_department($this->user_id);
        if (empty($department)) {
            $this->session->set_flashdata('msg', lang('contract_department_msg_error'));
            redirect('leaves');
        } else {
            $data['department'] = $department[0]['name'];
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('calendar/department', $data);
            $this->load->view('templates/footer');
        }
    }
    
    /**
     * Display a global calendar filtered by organization/entity
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function organization() {
        $this->auth->check_is_granted('organization_calendar');
        $data = $this->getUserContext();
        $data['title'] = lang('calendar_organization_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_organization');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/organization', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display a global tabular calendar
     * @param int $id identifier of the entity
     * @param int $month Month number
     * @param int $year Year number
     * @param bool $children If TRUE, includes children entity, FALSE otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function tabular($id=-1, $month=0, $year=0, $children=TRUE) {
        $this->auth->check_is_granted('organization_calendar');
        $data = $this->getUserContext();
        $this->load->model('leaves_model');
        $this->load->model('organization_model');
        $data['tabular'] = $this->leaves_model->tabular($id, $month, $year, $children);
        $data['entity'] = $id;
        $data['month'] = $month;
        $data['year'] = $year;
        $data['children'] = $children;
        $data['department'] = $this->organization_model->get_label($id);
        $data['title'] = lang('calendar_tabular_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_tabular');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/tabular', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Export a global tabular calendar
     * @param int $id identifier of the entity
     * @param int $month Month number
     * @param int $year Year number
     * @param bool $children If TRUE, includes children entity, FALSE otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function tabular_export($id=0, $month=0, $year=0, $children=TRUE) {
        $this->expires_now();
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->lang->load('global', $this->language);
        $this->load->model('leaves_model');
        $this->load->model('organization_model');
        
        $tabular = $this->leaves_model->tabular($id, $month, $year, $children);
        $start = $year . '-' . $month . '-' . '1';    //first date of selected month
        $lastDay = date("t", strtotime($start));    //last day of selected month
        $this->excel->getActiveSheet()->setTitle(lang('calendar_tabular_export_title'));
        
        //Write parameters of execution
        $this->excel->getActiveSheet()->setCellValue('A1', lang('calendar_tabular_export_param_entity'));
        $this->excel->getActiveSheet()->setCellValue('B1', lang('calendar_tabular_export_param_month'));
        $this->excel->getActiveSheet()->setCellValue('C1', lang('calendar_tabular_export_param_year'));
        $this->excel->getActiveSheet()->setCellValue('D1', lang('calendar_tabular_export_param_children'));
        $this->excel->getActiveSheet()->setCellValue('A2', $this->organization_model->get_label($id));
        $this->excel->getActiveSheet()->setCellValue('B2', $month);
        $this->excel->getActiveSheet()->setCellValue('C2', $year);
        if ($children) {
            $this->excel->getActiveSheet()->setCellValue('D2', lang('global_true'));
        } else {
            $this->excel->getActiveSheet()->setCellValue('D2', lang('global_false'));
        }
        
        //One column per day of the selected month, one line per employee
        $this->excel->getActiveSheet()->setCellValue('A4', lang('calendar_tabular_export_thead_employee'));
        for ($ii = 2; $ii <= $lastDay + 1; $ii++) {
            $col = $this->excel->column_name($ii);
            $this->excel->getActiveSheet()->setCellValue($col . '4', $ii - 1);
        }
        $this->excel->getActiveSheet()->getStyle('A4:' . $col . '4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A4:' . $col . '4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $line = 5;
        foreach ($tabular as $employee) {
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $employee->name);
            
            
            foreach ($employee->days as $day) {
                //Overlapping cases
                if (strstr($day->display, ';')) {
                    $periods = explode(";", $day->display);
                    $statuses = explode(";", $day->status);
                    switch ($statuses[0]) {
                        case 1: $class = "planned";
                            break;  // Planned
                        case 2: $class = "requested";
                            break;  // Requested
                        case 3: $class = "accepted";
                            break;  // Accepted
                        case 4: $class = "rejected";
                            break;  // Rejected
                    }
                    switch ($statuses[1]) {
                        case 1: $class .= "planned";
                            break;  // Planned
                        case 2: $class .= "requested";
                            break;  // Requested
                        case 3: $class .= "accepted";
                            break;  // Accepted
                        case 4: $class .= "rejected";
                            break;  // Rejected
                    }
                } else {    //No overlapping
                    switch ($day->display) {
                        case '0': //$this->XLSFillSolid('A30', ''); working day
                            break;
                        case '4': $this->XLSFillSolid('A30', '000000');
                            break;
                        case '1':
                            switch ($day->status) {
                                case 1: $this->XLSFillSolid('A30', '999000');
                                    break;  // Planned
                                case 2: $this->XLSFillSolid('A30', 'F89406');
                                    break;  // Requested
                                case 3: $this->XLSFillSolid('A30', '468847');
                                    break;  // Accepted
                                case 4: $this->XLSFillSolid('A30', 'FF0000');
                                    break;  // Rejected
                            }
                            break;
                        case '2':
                            switch ($day->status) {
                                case 1: $class = "amplanned";
                                    break;  // Planned
                                case 2: $class = "amrequested";
                                    break;  // Requested
                                case 3: $class = "amaccepted";
                                    break;  // Accepted
                                case 4: $class = "amrejected";
                                    break;  // Rejected
                            }
                            break;
                        case '3':
                            switch ($day->status) {
                                case 1: $class = "pmplanned";
                                    break;  // Planned
                                case 2: $class = "pmrequested";
                                    break;  // Requested
                                case 3: $class = "pmaccepted";
                                    break;  // Accepted
                                case 4: $class = "pmrejected";
                                    break;  // Rejected
                            }
                            break;
                    }
                }
            }

            /*$this->excel->getActiveSheet()->setCellValue('B' . $line, $leave['startdate']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $leave['startdatetype']);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $leave['enddate']);
            $this->excel->getActiveSheet()->setCellValue('E' . $line, $leave['enddatetype']);
            $this->excel->getActiveSheet()->setCellValue('F' . $line, $leave['duration']);
            $this->excel->getActiveSheet()->setCellValue('G' . $line, $this->types_model->get_label($leave['type']));
            $this->excel->getActiveSheet()->setCellValue('H' . $line, $leave['cause']);
            $this->excel->getActiveSheet()->setCellValue('I' . $line, $this->status_model->get_label($leave['status']));*/
/*            

*/
            
      /* //Diagonal gradient
        $worksheet->getStyle('A1')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR)
    ->setStartColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK))
    ->setEndColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_WHITE))
    ->setRotation(45);
         */
            
            
            $line++;
        }

        $filename = 'calendar-tabular.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
    
    /**
     * Internal utility function
     * Set background color of an Excel cell (fill solid)
     * @param string $cell XLS coordinate of the cell
     * @param string $color RGB value of the color
     */
    private function XLSDiagonalGradient($cell, $color1, $color2) {
        $this->excel->getActiveSheet()->getStyle($cell)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR)
            ->setStartColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK))
            ->setEndColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_WHITE))
            ->setRotation(45);
    }
    
    /**
     * Internal utility function
     * Set background color of an Excel cell (fill solid)
     * @param string $cell XLS coordinate of the cell
     * @param string $color RGB value of the color
     */
    private function XLSFillSolid($cell, $color) {
        $this->excel->getActiveSheet()->getStyle($cell)->applyFromArray(
                array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => $color))));
    }
    
    /**
     * Internal utility function
     * make sure a resource is reloaded every time
     */
    private function expires_now() {
        // Date in the past
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        // always modified
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        // HTTP/1.1
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        // HTTP/1.0
        header("Pragma: no-cache");
    }
}
