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

class Reports extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        //$this->output->enable_profiler($this->config->item('enable_profiling'));
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
    public function balance($refTmp = NULL) {
        $this->auth->check_is_granted('native_report_balance');
        $data = $this->getUserContext();
		$refDate = date("Y-m-d");
        if ($refTmp != NULL) {
            $refDate = date("Y-m-d", $refTmp);
        }
		$data['refDate'] = $refDate;
        $data['title'] = lang('reports_balance_title');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('reports/balance/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Ajax end-point : execute the balance report
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function balance_execute() {
        $this->auth->check_is_granted('native_report_balance');
        $data = $this->getUserContext();
        
        $this->load->model('leaves_model');
        $this->load->model('types_model');
        $this->load->model('organization_model');
        $result = array();
        $types = $this->types_model->get_types();
        $this->lang->load('global', $this->language);
		
		$refDate = date("Y-m-d");
        if (isset($_GET['refDate']) && $_GET['refDate'] != NULL) {
            $refDate = date("Y-m-d", $_GET['refDate']);
        }
        
        $include_children = filter_var($_GET['children'], FILTER_VALIDATE_BOOLEAN);
        $users = $this->organization_model->all_employees($_GET['entity'], $include_children);
        foreach ($users as $user) {
            $result[$user->id]['identifier'] = $user->identifier;
            $result[$user->id]['firstname'] = $user->firstname;
            $result[$user->id]['lastname'] = $user->lastname;
            $date = new DateTime($user->datehired);
            $result[$user->id]['datehired'] = $date->format(lang('global_date_format'));
            $result[$user->id]['department'] = $user->department;
            $result[$user->id]['position'] = $user->position;
            $result[$user->id]['contract'] = $user->contract;
            //Init type columns
            foreach ($types as $type) {
                $result[$user->id][$type['name']] = '';
            }
            
            $summary = $this->leaves_model->get_user_leaves_summary($user->id, TRUE, $refDate);
            if (count($summary) > 0 ) {
                foreach ($summary as $key => $value) {
                    $result[$user->id][$key] = $value[1] - $value[0];
                }
            }
        }
        
        $table = '';
        $thead = '';
        $tbody = '';
        $line = 2;
        foreach ($result as $row) {
            $index = 1;
            $tbody .= '<tr>';
            foreach ($row as $key => $value) {
                if ($line == 2) {
                    $thead .= '<th>' . $key . '</th>';
                }
                $tbody .= '<td>' . $value . '</td>';
                $index++;
            }
            $tbody .= '</tr>';
            $line++;
        }
        $table = '<table class="table table-bordered table-hover">' .
                    '<thead>' .
                        '<tr>' .
                            $thead .
                        '</tr>' .
                    '</thead>' .
                    '<tbody>' .
                        $tbody .
                    '</tbody>' .
                '</table>';
        
        echo $table;
    }
    
    /**
     * Export the balance report into Excel
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function balance_export() {
        $this->auth->check_is_granted('native_report_balance');
        $data = $this->getUserContext();
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle(lang('reports_export_balance_title'));

        $this->load->model('leaves_model');
        $this->load->model('types_model');
        $this->load->model('organization_model');
        $result = array();
        $summary = array();
        $types = $this->types_model->get_types();
        
        $include_children = filter_var($_GET['children'], FILTER_VALIDATE_BOOLEAN);
        $users = $this->organization_model->all_employees($_GET['entity'], $include_children);
        foreach ($users as $user) {
            $result[$user->id]['identifier'] = $user->identifier;
            $result[$user->id]['firstname'] = $user->firstname;
            $result[$user->id]['lastname'] = $user->lastname;
            $result[$user->id]['datehired'] = $user->datehired;
            $result[$user->id]['department'] = $user->department;
            $result[$user->id]['position'] = $user->position;
            $result[$user->id]['contract'] = $user->contract;
            //Init type columns
            foreach ($types as $type) {
                $result[$user->id][$type['name']] = '';
            }
            
            $summary = $this->leaves_model->get_user_leaves_summary($user->id, TRUE);
            if (count($summary) > 0 ) {
                foreach ($summary as $key => $value) {
                    $result[$user->id][$key] = $value[1] - $value[0];
                }
            }
        }
        
        $max = 0;
        $line = 2;
        foreach ($result as $row) {
            $index = 1;
            foreach ($row as $key => $value) {
                if ($line == 2) {
                    $colidx = $this->excel->column_name($index) . '1';
                    $this->excel->getActiveSheet()->setCellValue($colidx, $key);
                    $max++;
                }
                $colidx = $this->excel->column_name($index) . $line;
                $this->excel->getActiveSheet()->setCellValue($colidx, $value);
                $index++;
            }
            $line++;
        }

        $colidx = $this->excel->column_name($max) . '1';
        $this->excel->getActiveSheet()->getStyle('A1:' . $colidx)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:' . $colidx)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        
        $filename = 'leave_balance.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
    
    /**
     * Execute the shipped-in carried over report
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function carried_over() {
        $this->auth->check_is_granted('native_report_balance');
        $data = $this->getUserContext();
        $data['title'] = lang('reports_carried_over_title');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('reports/carried_over/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Ajax end-point : execute the carried over report
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function carried_over_execute() {
        $this->auth->check_is_granted('native_report_balance');
        $data = $this->getUserContext();
        
        $this->load->model('leaves_model');
        $this->load->model('types_model');
        $this->load->model('organization_model');
        $result = array();
        $types = $this->types_model->get_types();
        $this->lang->load('global', $this->language);
        
        $include_children = filter_var($_GET['children'], FILTER_VALIDATE_BOOLEAN);
        $users = $this->organization_model->all_employees($_GET['entity'], $include_children);
        foreach ($users as $user) {
            $result[$user->id]['identifier'] = $user->identifier;
            $result[$user->id]['firstname'] = $user->firstname;
            $result[$user->id]['lastname'] = $user->lastname;
            $date = new DateTime($user->datehired);
            $result[$user->id]['datehired'] = $date->format(lang('global_date_format'));
            $result[$user->id]['department'] = $user->department;
            $result[$user->id]['position'] = $user->position;
            $result[$user->id]['contract'] = $user->contract;
            //Init type columns
            foreach ($types as $type) {
                $result[$user->id][$type['name']] = '';
            }
            
            $summary = $this->leaves_model->get_user_leaves_carried_over($user->id, TRUE);
            if (count($summary) > 0 ) {
                foreach ($summary as $key => $value) {
                    $result[$user->id][$key] = $value[1] - $value[0];
                }
            }
        }
        
        $table = '';
        $thead = '';
        $tbody = '';
        $line = 2;
        foreach ($result as $row) {
            $index = 1;
            $tbody .= '<tr>';
            foreach ($row as $key => $value) {
                if ($line == 2) {
                    $thead .= '<th>' . $key . '</th>';
                }
                $tbody .= '<td>' . $value . '</td>';
                $index++;
            }
            $tbody .= '</tr>';
            $line++;
        }
        $table = '<table class="table table-bordered table-hover">' .
                    '<thead>' .
                        '<tr>' .
                            $thead .
                        '</tr>' .
                    '</thead>' .
                    '<tbody>' .
                        $tbody .
                    '</tbody>' .
                '</table>';
        
        echo $table;
    }
    
    /**
     * Export the carried over report into Excel
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function carried_over_export() {
        $this->auth->check_is_granted('native_report_balance');
        $data = $this->getUserContext();
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle(lang('reports_export_carried_over_title'));

        $this->load->model('leaves_model');
        $this->load->model('types_model');
        $this->load->model('organization_model');
        $result = array();
        $summary = array();
        $types = $this->types_model->get_types();
        
        $include_children = filter_var($_GET['children'], FILTER_VALIDATE_BOOLEAN);
        $users = $this->organization_model->all_employees($_GET['entity'], $include_children);
        foreach ($users as $user) {
            $result[$user->id]['identifier'] = $user->identifier;
            $result[$user->id]['firstname'] = $user->firstname;
            $result[$user->id]['lastname'] = $user->lastname;
            $result[$user->id]['datehired'] = $user->datehired;
            $result[$user->id]['department'] = $user->department;
            $result[$user->id]['position'] = $user->position;
            $result[$user->id]['contract'] = $user->contract;
            //Init type columns
            foreach ($types as $type) {
                $result[$user->id][$type['name']] = '';
            }
            
            $summary = $this->leaves_model->get_user_leaves_carried_over($user->id, TRUE);
            if (count($summary) > 0 ) {
                foreach ($summary as $key => $value) {
                    $result[$user->id][$key] = $value[1] - $value[0];
                }
            }
        }
        
        $max = 0;
        $line = 2;
        foreach ($result as $row) {
            $index = 1;
            foreach ($row as $key => $value) {
                if ($line == 2) {
                    $colidx = $this->excel->column_name($index) . '1';
                    $this->excel->getActiveSheet()->setCellValue($colidx, $key);
                    $max++;
                }
                $colidx = $this->excel->column_name($index) . $line;
                $this->excel->getActiveSheet()->setCellValue($colidx, $value);
                $index++;
            }
            $line++;
        }

        $colidx = $this->excel->column_name($max) . '1';
        $this->excel->getActiveSheet()->getStyle('A1:' . $colidx)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:' . $colidx)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        
        $filename = 'carried_over_leaves.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    
    /**
     * Execute the shipped-in history report
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function history() {
        $this->auth->check_is_granted('native_report_history');
        $data = $this->getUserContext();
        $data['title'] = lang('reports_history_title');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('reports/history/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Ajax end-point : execute the history report
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function history_execute() {
        $this->auth->check_is_granted('native_report_history');
        $data = $this->getUserContext();
        $this->load->model('history_model');
        
        $table = $_GET['table'];
        $modification_type = $_GET['modification_type'];
        $modified_by = isset($_GET['modified_by'])?$_GET['modified_by']:null;
        $startdate = isset($_GET['startdate'])?$_GET['startdate']:null;
        $enddate = isset($_GET['enddate'])?$_GET['enddate']:null;
        $result = $this->history_model->get_history($modification_type, $table, $modified_by, $startdate, $enddate);
        
        $table = '';
        $thead = '';
        $tbody = '';
        $line = 2;
        foreach ($result as $row) {
            $index = 1;
            $tbody .= '<tr>';
            foreach ($row as $key => $value) {
                if ($line == 2) {
                    $thead .= '<th>' . $key . '</th>';
                }
                $tbody .= '<td>' . $value . '</td>';
                $index++;
            }
            $tbody .= '</tr>';
            $line++;
        }
        $table = '<table class="table table-bordered table-hover">' .
                    '<thead>' .
                        '<tr>' .
                            $thead .
                        '</tr>' .
                    '</thead>' .
                    '<tbody>' .
                        $tbody .
                    '</tbody>' .
                '</table>';
        
        echo $table;
    }
    
}
