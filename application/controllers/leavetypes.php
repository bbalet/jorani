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

class LeaveTypes extends CI_Controller {
    
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
        $this->load->model('types_model');
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_admin = $this->session->userdata('is_admin');
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
        $this->language = $this->session->userdata('language');
        $this->language_code = $this->session->userdata('language_code');
        $this->lang->load('leavetypes', $this->language);
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
     * Display list of leave types
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->check_is_granted('leavetypes_list');
        expires_now();
        $data = $this->getUserContext();
        $data['leavetypes'] = $this->types_model->get_types();
        $data['title'] = lang('leavetypes_type_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_edit_leave_type');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leavetypes/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display a form that allows adding a leave type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        $this->auth->check_is_granted('leavetypes_create');
        expires_now();
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('leavetypes_popup_create_title');
        
        $this->form_validation->set_rules('name', lang('leavetypes_popup_create_field_name'), 'required|xss_clean');        
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('leavetypes/create', $data);
        } else {
            $this->types_model->set_types();
            $this->session->set_flashdata('msg', lang('leavetypes_popup_create_flash_msg'));
            redirect('leavetypes');
        }
    }

    /**
     * Display a form that allows editing a leave type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($id) {
        $this->auth->check_is_granted('leavetypes_edit');
        expires_now();
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('leavetypes_popup_update_title');
        $data['id'] = $id;
        $data['type_name'] = $this->types_model->get_label($id);
        
        $this->form_validation->set_rules('name', lang('leavetypes_popup_update_field_name'), 'required|xss_clean');        
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('leavetypes/edit', $data);
        } else {
            $this->types_model->update_types();
            $this->session->set_flashdata('msg', lang('leavetypes_popup_update_flash_msg'));
            redirect('leavetypes');
        }
    }
    
    /**
     * Action : delete a leave type
     * @param int $id leave type identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) {
        $this->auth->check_is_granted('leavetypes_delete');
        if ($id != 0) {
            if ($this->types_model->usage($id) > 0) {
                $this->session->set_flashdata('msg', lang('leavetypes_popup_delete_flash_forbidden'));
            } else {
                $this->types_model->delete_type($id);
                $this->session->set_flashdata('msg', lang('leavetypes_popup_delete_flash_msg'));
            }
        } else {
            $this->session->set_flashdata('msg', lang('leavetypes_popup_delete_flash_error'));
        }
        redirect('leavetypes');
    }

    /**
     * Action: export the list of all leave types into an Excel file
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export() {
        $this->auth->check_is_granted('leavetypes_export');
        expires_now();
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle(lang('leavetypes_type_export_title'));
        $this->excel->getActiveSheet()->setCellValue('A1', lang('leavetypes_type_export_thead_id'));
        $this->excel->getActiveSheet()->setCellValue('B1', lang('leavetypes_type_export_thead_name'));
        $this->excel->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $types = $this->types_model->get_types();
        $line = 2;
        foreach ($types as $type) {
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $type['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $type['name']);
            $line++;
        }

        //Autofit
        foreach(range('A', 'B') as $colD) {
            $this->excel->getActiveSheet()->getColumnDimension($colD)->setAutoSize(TRUE);
        }
        
        $filename = 'leave_types.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
}
