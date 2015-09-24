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

class Extra extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->load->model('overtime_model');
        $this->lang->load('extra', $this->language);
        $this->lang->load('global', $this->language);
    }

    /**
     * Display the list of the overtime requests of the connected user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->check_is_granted('list_extra');
        $data = getUserContext($this);
        $data['extras'] = $this->overtime_model->get_user_extras($this->user_id);
        $this->load->model('status_model');
        $len = count($data['extras']);
        for ($i = 0; $i < $len; ++$i) {
            $data['extras'][$i]['status_label'] = $this->status_model->get_label($data['extras'][$i]['status']);
        }
        $data['title'] = lang('extra_index_title');
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, true);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('extra/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display an overtime request
     * @param int $id identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function view($id) {
        $this->auth->check_is_granted('view_extra');
        $data = getUserContext($this);
        $data['extra'] = $this->overtime_model->get_extra($id);
        $this->load->model('status_model');
        if (empty($data['extra'])) {
            show_404();
        }
        
        //If the user is not its not HR, not manager and not the creator of the overtime
        //the employee can't see it, redirect to LR list
        if ($data['extra']['employee'] != $this->user_id) {
            if ((!$this->is_hr)) {
                $this->load->model('users_model');
                $employee = $this->users_model->get_users($data['extra']['employee']);
                if ($employee['manager'] != $this->user_id) {
                    log_message('error', 'User #' . $this->user_id . ' illegally tried to view overtime #' . $id);
                    redirect('extra');
                }
            } //Admin
        } //Current employee
        
        $data['extra']['status_label'] = $this->status_model->get_label($data['extra']['status']);
        $data['title'] = lang('extra_view_hmtl_title');
        $this->load->model('users_model');
        $data['name'] = $this->users_model->get_label($data['extra']['employee']);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('extra/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Create an overtime request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        $this->auth->check_is_granted('create_extra');
        $data = getUserContext($this);
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('date', lang('extra_create_field_date'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('duration', lang('extra_create_field_duration'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('cause', lang('extra_create_field_cause'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('status', lang('extra_create_field_status'), 'required|xss_clean|strip_tags');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = lang('extra_create_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_create_overtime');
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('extra/create');
            $this->load->view('templates/footer');
        } else {
            $extra_id = $this->overtime_model->set_extra();
            $this->session->set_flashdata('msg', lang('extra_create_msg_success'));
            //If the status is requested, send an email to the manager
            if ($this->input->post('status') == 2) {
                $this->sendMail($extra_id);
            }
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('extra');
            }
        }
    }
    
    /**
     * Edit an overtime request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($id) {
        $this->auth->check_is_granted('edit_extra');
        $data = getUserContext($this);
        $data['extra'] = $this->overtime_model->get_extra($id);
        //Check if exists
        if (empty($data['extra'])) {
            show_404();
        }
        //If the user is not its own manager and if the overtime is 
        //already requested, the employee can't modify it
        if (!$this->is_hr) {
            if (($this->session->userdata('manager') != $this->user_id) &&
                    $data['extra']['status'] != 1) {
                log_message('error', 'User #' . $this->user_id . ' illegally tried to edit overtime request #' . $id);
                $this->session->set_flashdata('msg', lang('extra_edit_msg_error'));
                redirect('extra');
            }
        } //Admin
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('date', lang('extra_edit_field_date'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('duration', lang('extra_edit_field_duration'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('cause', lang('extra_edit_field_cause'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('status', lang('extra_edit_field_status'), 'required|xss_clean|strip_tags');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = lang('extra_edit_hmtl_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_create_overtime');
            $data['id'] = $id;
            $this->load->model('users_model');
            $data['name'] = $this->users_model->get_label($data['extra']['employee']);
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('extra/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $this->overtime_model->update_extra($id);       //We don't use the return value
            $this->session->set_flashdata('msg', lang('extra_edit_msg_success'));
            //If the status is requested, send an email to the manager
            if ($this->input->post('status') == 2) {
                $this->sendMail($id);
            }
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('extra');
            }
        }
    }
    
    /**
     * Send a overtime request email to the manager of the connected employee
     * @param int $id overtime request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function sendMail($id) {
        $this->load->model('users_model');
        $this->load->model('delegations_model');
        $manager = $this->users_model->get_users($this->session->userdata('manager'));

        //Test if the manager hasn't been deleted meanwhile
        if (empty($manager['email'])) {
            $this->session->set_flashdata('msg', lang('extra_create_msg_error'));
        } else {
            $acceptUrl = base_url() . 'overtime/accept/' . $id;
            $rejectUrl = base_url() . 'overtime/reject/' . $id;

            //Send an e-mail to the manager
            $this->load->library('email');
            $this->load->library('polyglot');
            $usr_lang = $this->polyglot->code2language($manager['language']);
            //We need to instance an different object as the languages of connected user may differ from the UI lang
            $lang_mail = new CI_Lang();
            $lang_mail->load('email', $usr_lang);
            $lang_mail->load('global', $usr_lang);

            $date = new DateTime($this->input->post('date'));
            $startdate = $date->format($lang_mail->line('global_date_format'));

            $this->load->library('parser');
            $data = array(
                'Title' => $lang_mail->line('email_extra_request_validation_title'),
                'Firstname' => $this->session->userdata('firstname'),
                'Lastname' => $this->session->userdata('lastname'),
                'Date' => $startdate,
                'Duration' => $this->input->post('duration'),
                'Cause' => $this->input->post('cause'),
                'UrlAccept' => $acceptUrl,
                'UrlReject' => $rejectUrl
            );
            $message = $this->parser->parse('emails/' . $manager['language'] . '/overtime', $data, TRUE);
            $this->email->set_encoding('quoted-printable');
            
            if ($this->config->item('from_mail') != FALSE && $this->config->item('from_name') != FALSE ) {
                $this->email->from($this->config->item('from_mail'), $this->config->item('from_name'));
            } else {
               $this->email->from('do.not@reply.me', 'LMS');
            }
            $this->email->to($manager['email']);
            if ($this->config->item('subject_prefix') != FALSE) {
                $subject = $this->config->item('subject_prefix');
            } else {
               $subject = '[Jorani] ';
            }
            //Copy to the delegates, if any
            $delegates = $this->delegations_model->get_delegates_mails($manager['id']);
            if ($delegates != '') {
                $this->email->cc($delegates);
            }
            $this->email->subject($subject . $lang_mail->line('email_extra_request_reject_subject') . ' ' .
                    $this->session->userdata('firstname') . ' ' .
                    $this->session->userdata('lastname'));
            $this->email->message($message);
            $this->email->send();
        }
    }

    /**
     * Delete an overtime request
     * @param int $id identifier of the overtime request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) {
        $can_delete = false;
        //Test if the overtime request exists
        $extra = $this->overtime_model->get_extra($id);
        if (empty($extra)) {
            show_404();
        } else {
            if ($this->is_hr) {
                $can_delete = true;
            } else {
                if ($extra['status'] == 1 ) {
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
     * Action: export the list of all extra times into an Excel file
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export() {
        $this->load->library('excel');
        $sheet = $this->excel->setActiveSheetIndex(0);
        $sheet->setTitle(mb_strimwidth(lang('extra_export_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
        $sheet->setCellValue('A1', lang('extra_export_thead_id'));
        $sheet->setCellValue('B1', lang('extra_export_thead_date'));
        $sheet->setCellValue('C1', lang('extra_export_thead_duration'));
        $sheet->setCellValue('D1', lang('extra_export_thead_cause'));
        $sheet->setCellValue('E1', lang('extra_export_thead_status'));
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $extras = $this->overtime_model->get_user_extras($this->user_id);
        $this->load->model('status_model');
        
        $line = 2;
        foreach ($extras as $extra) {
            $date = new DateTime($extra['date']);
            $startdate = $date->format(lang('global_date_format'));
            $sheet->setCellValue('A' . $line, $extra['id']);
            $sheet->setCellValue('B' . $line, $startdate);
            $sheet->setCellValue('C' . $line, $extra['duration']);
            $sheet->setCellValue('D' . $line, $extra['cause']);
            $sheet->setCellValue('E' . $line, lang($this->status_model->get_label($extra['status'])));
            $line++;
        }
        
        //Autofit
        foreach(range('A', 'E') as $colD) {
            $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
        }

        $filename = 'extra.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
}
