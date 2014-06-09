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

class Extra extends CI_Controller {
    
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
        $this->is_hr = $this->session->userdata('is_hr');
        $this->load->model('overtime_model');
        $this->user_id = $this->session->userdata('id');
        $this->language = $this->session->userdata('language');
        $this->language_code = $this->session->userdata('language_code');
        $this->load->helper('language');
        $this->lang->load('extra', $this->language);
    }
    
    /**
     * Prepare an array containing information about the current user
     * @return array data to be passed to the view
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function getUserContext()
    {
        $data['fullname'] = $this->fullname;
        $data['is_hr'] = $this->is_hr;
        $data['user_id'] =  $this->user_id;
        $data['language'] = $this->language;
        $data['language_code'] =  $this->language_code;
        return $data;
    }

    /**
     * Display the list of the leave requests of the connected user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->check_is_granted('list_extra');
        $data = $this->getUserContext();
        $data['extras'] = $this->overtime_model->get_user_extras($this->user_id);
        
        $this->load->model('status_model');
        for ($i = 0; $i < count($data['extras']); ++$i) {
            $data['extras'][$i]['status_label'] = $this->status_model->get_label($data['extras'][$i]['status']);
        }
        $data['title'] = lang('extra_index_title');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('extra/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display a leave request
     * @param int $id identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function view($id) {
        $this->auth->check_is_granted('view_extra');
        $data = $this->getUserContext();
        $data['leave'] = $this->overtime_model->get_extra($id);
        $this->load->model('status_model');
        if (empty($data['leave'])) {
            show_404();
        }
        $data['leave']['status_label'] = $this->status_model->get_label($data['leave']['status']);
        $data['title'] = lang('extra_view_hmtl_title');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('extra/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Create a leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        $this->auth->check_is_granted('create_extra');
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('extra_create_title');
        
        $this->form_validation->set_rules('date', lang('extra_create_field_date'), 'required|xss_clean');
        $this->form_validation->set_rules('duration', lang('extra_create_field_duration'), 'required|xss_clean');
        $this->form_validation->set_rules('cause', lang('extra_create_field_cause'), 'required|xss_clean');
        $this->form_validation->set_rules('status', lang('extra_create_field_status'), 'required|xss_clean');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('extra/create');
            $this->load->view('templates/footer');
        } else {
            $extra_id = $this->overtime_model->set_extra();
            //If the status is requested, send an email to the manager
            if ($this->input->post('status') == 2) {
                $this->sendMail($extra_id);
            }            
            $this->session->set_flashdata('msg', lang('extra_create_msg_success'));
            redirect('extra');
        }
    }
    
    /**
     * Edit a leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($id) {
        $this->auth->check_is_granted('edit_extra');
        $data = $this->getUserContext();
        $data['leave'] = $this->overtime_model->get_extra($id);
        //Check if exists
        if (empty($data['leave'])) {
            show_404();
        }
        //If the user is not its own manager and if the leave is 
        //already requested, the employee can't modify it
        if (!$this->is_hr) {
            if (($this->session->userdata('manager') != $this->user_id) &&
                    $data['leave']['status'] != 1) {
                log_message('error', 'User #' . $this->user_id . ' illegally tried to edit overtime request #' . $id);
                $this->session->set_flashdata('msg', lang('extra_edit_msg_error'));
                redirect('extra');
            }
        } //Admin
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('extra_edit_hmtl_title');
        $data['id'] = $id;      
        $this->form_validation->set_rules('date', lang('extra_edit_field_date'), 'required|xss_clean');
        $this->form_validation->set_rules('duration', lang('extra_edit_field_duration'), 'required|xss_clean');
        $this->form_validation->set_rules('cause', lang('extra_edit_field_cause'), 'required|xss_clean');
        $this->form_validation->set_rules('status', lang('extra_edit_field_status'), 'required|xss_clean');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('extra/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $extra_id = $this->overtime_model->update_extra($id);
            //If the status is requested, send an email to the manager
            if ($this->input->post('status') == 2) {
                $this->sendMail($extra_id);
            }            
            $this->session->set_flashdata('msg', lang('extra_edit_msg_success'));
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('extra');
            }
        }
    }
    
    /**
     * Send a overtime request email to the manager
     * @param int $id Leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function sendMail($id)
    {
        $this->load->model('users_model');
        $this->load->model('settings_model');
        $manager = $this->users_model->get_users($this->session->userdata('manager'));
        $acceptUrl = base_url() . 'overtime/accept/' . $id;
        $rejectUrl = base_url() . 'overtime/reject/' . $id;

        //Send an e-mail to the manager
        //See: http://www.codeigniter.fr/user_guide/libraries/email.html
        $this->load->library('email');
        $config = $this->settings_model->get_mail_config();            
        $this->email->initialize($config);
        
        $this->load->library('language');
        $usr_lang = $this->language->code2language($manager['language']);
        $this->lang->load('email', $usr_lang);

        $this->load->library('parser');
        $data = array(
            'Title' => lang('email_extra_request_validation_title'),
            'Firstname' => $this->session->userdata('firstname'),
            'Lastname' => $this->session->userdata('lastname'),
            'Date' => $this->input->post('date'),
            'Duration' => $this->input->post('duration'),
            'Cause' => $this->input->post('cause'),
            'UrlAccept' => $acceptUrl,
            'UrlReject' => $rejectUrl
        );
        $message = $this->parser->parse('emails/' . $manager['language'] . '/overtime', $data, TRUE);
        //$message = iconv(mb_detect_encoding($message, mb_detect_order(), true), "UTF-8", $message);

        $this->email->from('do.not@reply.me', 'LMS');
        $this->email->to($manager['email']);
        $this->email->subject(lang('email_extra_request_reject_subject') .
                $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname'));
        $this->email->message($message);
        $this->email->send();
    }
    
    /**
     * Delete a leave request
     * @param int $id identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) {
        $can_delete = false;
        //Test if the leave request exists
        $extra = $this->overtime_model->get_extra($id);
        if (empty($extra)) {
            show_404();
        } else {
            if ($this->is_hr) {
                $can_delete = true;
            } else {
                if ($leaves['status'] == 1 ) {
                    $can_delete = true;
                }
            }
            if ($can_delete == true) {
                $this->overtime_model->delete_extra($id);
                $this->session->set_flashdata('msg', lang('extra_delete_msg_success'));
            } else {
                $this->session->set_flashdata('msg', lang('extra_delete_msg_error'));
            }
        }
        if (isset($_GET['source'])) {
            redirect($_GET['source']);
        } else {
            redirect('extra');
        }
    }
    
    /**
     * Action: export the list of all leaves into an Excel file
     */
    public function export() {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle(lang('extra_export_title'));
        $this->excel->getActiveSheet()->setCellValue('A1', lang('extra_export_thead_id'));
        $this->excel->getActiveSheet()->setCellValue('B1', lang('extra_export_thead_date'));
        $this->excel->getActiveSheet()->setCellValue('C1', lang('extra_export_thead_duration'));
        $this->excel->getActiveSheet()->setCellValue('D1', lang('extra_export_thead_cause'));
        $this->excel->getActiveSheet()->setCellValue('E1', lang('extra_export_thead_status'));
        $this->excel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $extras = $this->overtime_model->get_user_extras($this->user_id);
        $this->load->model('status_model');
        
        $line = 2;
        foreach ($extras as $extra) {
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $extra['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $extra['date']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $extra['duration']);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $extra['cause']);
            $this->excel->getActiveSheet()->setCellValue('E' . $line, $this->status_model->get_label($extra['status']));
            $line++;
        }

        $filename = 'extra.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
}
