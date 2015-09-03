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

class Calendar extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        //This controller differs from the others, because some calendars can be public
    }

    /**
     * Display the page of the individual calendar (of the connected user)
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual() {
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->check_is_granted('individual_calendar');
        $data = getUserContext($this);
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
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->check_is_granted('workmates_calendar');
        $data = getUserContext($this);
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
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->check_is_granted('collaborators_calendar');
        $data = getUserContext($this);
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
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->check_is_granted('department_calendar');
        $data = getUserContext($this);
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
        if (($this->config->item('public_calendar') == TRUE) && (!$this->session->userdata('logged_in'))) {
            $this->load->library('polyglot');;
            $data['language'] = $this->config->item('language');
            $data['language_code'] =  $this->polyglot->language2code($data['language']);
            $data['title'] = lang('calendar_organization_title');
            $data['help'] = '';
            $data['logged_in'] = FALSE;
            $this->lang->load('calendar', $data['language']);
            $this->load->view('templates/header', $data);
            $this->load->view('calendar/organization', $data);
            $this->load->view('templates/footer_simple');
        } else {
            setUserContext($this);
            $this->lang->load('calendar', $this->language);
            $this->auth->check_is_granted('organization_calendar');
            $data = getUserContext($this);
            $data['logged_in'] = TRUE;
            $data['title'] = lang('calendar_organization_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_organization');
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('calendar/organization', $data);
            $this->load->view('templates/footer');
        }
    }
    
    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * This code is duplicated from controller/leaves for public access
     * @param int $entity_id Entity identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function public_organization($entity_id) {
        expires_now();
        header("Content-Type: application/json");
        if ($this->config->item('public_calendar') == TRUE) {
            $this->load->model('leaves_model');
            $start = $this->input->get('start', TRUE);
            $end = $this->input->get('end', TRUE);
            $children = filter_var($this->input->get('children', TRUE), FILTER_VALIDATE_BOOLEAN);
            echo $this->leaves_model->department($entity_id, $start, $end, $children);
        } else {
            echo 'Forbidden';
        }
    }
    
    /**
     * Ajax endpoint : Send a list of fullcalendar events: List of all possible day offs
     * This code is duplicated from controller/contract for public access
     * @param int $entity_id Entity identifier
     */
    public function public_dayoffs() {
        expires_now();
        header("Content-Type: application/json");
        if ($this->config->item('public_calendar') == TRUE) {
            $start = $this->input->get('start', TRUE);
            $end = $this->input->get('end', TRUE);
            $entity = $this->input->get('entity', TRUE);
            $children = filter_var($this->input->get('children', TRUE), FILTER_VALIDATE_BOOLEAN);
            $this->load->model('dayoffs_model');
            echo $this->dayoffs_model->allDayoffs($start, $end, $entity, $children);
        } else {
            echo 'Forbidden';
        }
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
        if (($this->config->item('public_calendar') == TRUE) && (!$this->session->userdata('logged_in'))) {
            $this->load->library('polyglot');;
            $data['language'] = $this->config->item('language');
            $data['language_code'] =  $this->polyglot->language2code($data['language']);
            $this->load->model('leaves_model');
            $this->load->model('organization_model');
            $data['tabular'] = $this->leaves_model->tabular($id, $month, $year, $children);
            $data['entity'] = $id;
            $data['month'] = $month;
            $data['year'] = $year;
            $data['children'] = $children;
            $data['department'] = $this->organization_model->get_label($id);
            $data['title'] = lang('calendar_tabular_title');
            $data['help'] = '';
            $this->load->view('templates/header', $data);
            $this->load->view('calendar/tabular', $data);
            $this->load->view('templates/footer_simple');
        } else {
            setUserContext($this);
            $this->lang->load('calendar', $this->language);
            $this->auth->check_is_granted('organization_calendar');
            $data = getUserContext($this);
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
    }
    
    /**
     * Export the tabular calendar into Excel. The presentation differs a bit according to the limitation of Excel
     * We'll get one line for the morning and one line for the afternoon
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function tabular_export($id=-1, $month=0, $year=0, $children=TRUE) {
        expires_now();
        
        //Load the language file (the loaded language depends if it was called from the public view)
        if (($this->config->item('public_calendar') == TRUE) && (!$this->session->userdata('logged_in'))) {
            $this->load->library('polyglot');;
            $language = $this->config->item('language');
            //$language_code =  $this->polyglot->language2code($language);
        } else {
            setUserContext($this);
            $language = $this->language;
        }
        $this->lang->load('calendar', $language);
        $this->lang->load('global', $language);
        
        $this->load->library('excel');
        $sheet = $this->excel->setActiveSheetIndex(0);
        

        //Print the header with the values of the export parameters
        $sheet->setTitle(lang('calendar_tabular_export_title'));
        $sheet->setCellValue('A1', lang('calendar_tabular_export_param_entity'));
        $sheet->setCellValue('A2', lang('calendar_tabular_export_param_month'));
        $sheet->setCellValue('A3', lang('calendar_tabular_export_param_year'));
        $sheet->setCellValue('A4', lang('calendar_tabular_export_param_children'));
        $sheet->getStyle('A1:A4')->getFont()->setBold(true);
        $this->load->model('organization_model');
        $sheet->setCellValue('B1', $this->organization_model->get_label($id));
        $sheet->setCellValue('B2', $month);
        $sheet->setCellValue('B3', $year);
        if ($children == TRUE) {
            $sheet->setCellValue('B4', lang('global_true'));
        } else {
            $sheet->setCellValue('B4', lang('global_false'));
        }

        //Print two lines : the short name of all days for the selected month (horizontally aligned)
        $start = $year . '-' . $month . '-' . '1';    //first date of selected month
        $lastDay = date("t", strtotime($start));    //last day of selected month
        for ($ii = 1; $ii <=$lastDay; $ii++) {
            $dayNum = date("N", strtotime($year . '-' . $month . '-' . $ii));
            $col = $this->excel->column_name(3 + $ii);
            //Print day number
            $sheet->setCellValue($col . '9', $ii);
            //Print short name of the day
            switch ($dayNum)
            {
                case 1: $sheet->setCellValue($col . '8', lang('calendar_monday_short')); break;
                case 2: $sheet->setCellValue($col . '8', lang('calendar_tuesday_short')); break;
                case 3: $sheet->setCellValue($col . '8', lang('calendar_wednesday_short')); break;
                case 4: $sheet->setCellValue($col . '8', lang('calendar_thursday_short')); break;
                case 5: $sheet->setCellValue($col . '8', lang('calendar_friday_short')); break;
                case 6: $sheet->setCellValue($col . '8', lang('calendar_saturday_short')); break;
                case 7: $sheet->setCellValue($col . '8', lang('calendar_sunday_short')); break;
            }
        }
        //Label for employee name
        $sheet->setCellValue('C8', lang('calendar_tabular_export_thead_employee'));
        $sheet->mergeCells('C8:C9');
        //The header is horizontally aligned
        $col = $this->excel->column_name(3 + $lastDay);
        $sheet->getStyle('C8:' . $col . '9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        //Get the tabular data
        $this->load->model('leaves_model');
        $tabular = $this->leaves_model->tabular($id, $month, $year, $children);
        
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
        
        $line = 10;
        //Iterate on all employees of the selected entity
        foreach ($tabular as $employee) {
            //Merge the two line containing the name of the employee and apply a border around it
            $sheet->setCellValue('C' . $line, $employee->name);
            $sheet->mergeCells('C' . $line . ':C' . ($line + 1));
            $col = $this->excel->column_name($lastDay + 3);
            $sheet->getStyle('C' . $line . ':' . $col . ($line + 1))->applyFromArray($styleBox);
            
            //Iterate on all days of the selected month
            $dayNum = 0;
            foreach ($employee->days as $day) {
                $dayNum++;
                $col = $this->excel->column_name(3 + $dayNum);
                $overlapping = FALSE;
                if (strstr($day->display, ';')) {//Two statuses in the cell
                    $periods = explode(";", $day->display);
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
            $line += 2;
        }//Employee
        
        //Autofit for all column containing the days
        for ($ii = 1; $ii <=$lastDay; $ii++) {
            $col = $this->excel->column_name($ii + 3);
            $sheet->getStyle($col . '8:' . $col . ($line - 1))->applyFromArray($dayBox);
            $sheet->getColumnDimension($col)->setAutoSize(TRUE);
        }
        $sheet->getColumnDimension('A')->setAutoSize(TRUE);
        $sheet->getColumnDimension('B')->setAutoSize(TRUE);
        $sheet->getColumnDimension('C')->setWidth(40);
        
        $filename = 'tabular.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
