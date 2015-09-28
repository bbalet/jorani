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

class Reports extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->lang->load('reports', $this->language);
        $this->lang->load('global', $this->language);
    }

    /**
     * List the available custom reports (provided they are discribed into local/reports/*.ini)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->checkIfOperationIsAllowed('report_list');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        
        $reports = array();
        //List all the available reports
        $files = glob(FCPATH . '/local/reports/*.ini');
        foreach($files as $file) {
            $ini_array = parse_ini_file($file, true);
            //Test if the report is available for the language being used
            if (array_key_exists($this->language_code, $ini_array)) {
                //If available, push the report into the list to be displayed with a description
                $reports[$ini_array[$this->language_code]['name']] = array(
                    basename($file),
                    $ini_array[$this->language_code]['description'],
                    $ini_array['configuration']['path'],
                );
            }
        }
        
        $data['title'] = lang('reports_index_title');
        $data['reports'] = $reports;
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('reports/index', $data);
        $this->load->view('templates/footer'); 
    }

    
    /**
     * Landing page of the shipped-in balance report
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function balance($refTmp = NULL) {
        $this->auth->checkIfOperationIsAllowed('native_report_balance');
        $data = getUserContext($this);
		$refDate = date("Y-m-d");
        if ($refTmp != NULL) {
            $refDate = date("Y-m-d", $refTmp);
        }
        $data['refDate'] = $refDate;
        $data['title'] = lang('reports_balance_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_leave_balance_report');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('reports/balance/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Ajax end-point : execute the balance report
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function executeBalanceReport() {
        $this->auth->checkIfOperationIsAllowed('native_report_balance');
        
        $this->load->model('leaves_model');
        $this->load->model('types_model');
        $this->load->model('organization_model');
        $result = array();
        $types = $this->types_model->getTypes();
        $this->lang->load('global', $this->language);
		
        $refDate = date("Y-m-d");
        if (isset($_GET['refDate']) && $_GET['refDate'] != NULL) {
            $refDate = date("Y-m-d", $_GET['refDate']);
        }
        $include_children = filter_var($_GET['children'], FILTER_VALIDATE_BOOLEAN);
        $users = $this->organization_model->allEmployees($_GET['entity'], $include_children);
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
            
            $summary = $this->leaves_model->getLeaveBalanceForEmployee($user->id, TRUE, $refDate);
            if (count($summary) > 0 ) {
                foreach ($summary as $key => $value) {
                    $result[$user->id][$key] = round($value[1] - $value[0], 3, PHP_ROUND_HALF_DOWN);
                }
            }
        }
        
        $table = '';
        $thead = '';
        $tbody = '';
        $line = 2;
        $i18n = array("identifier", "firstname", "lastname", "datehired", "department", "position", "contract");
        foreach ($result as $row) {
            $index = 1;
            $tbody .= '<tr>';
            foreach ($row as $key => $value) {
                if ($line == 2) {
                    if (in_array($key, $i18n)) {
                        $thead .= '<th>' . lang($key) . '</th>';
                    } else {
                        $thead .= '<th>' . $key . '</th>';
                    }
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
    public function exportBalanceReport() {
        $this->auth->checkIfOperationIsAllowed('native_report_balance');
        $this->load->model('leaves_model');
        $this->load->model('types_model');
        $this->load->model('organization_model');
        $this->load->library('excel');
        $data['refDate'] = date("Y-m-d");
        if (isset($_GET['refDate']) && $_GET['refDate'] != NULL) {
            $data['refDate'] = date("Y-m-d", $_GET['refDate']);
        }
        $data['include_children'] = filter_var($_GET['children'], FILTER_VALIDATE_BOOLEAN);
        $this->load->view('reports/balance/export', $data);
    }
}
