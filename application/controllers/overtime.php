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

class Overtime extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->load->model('overtime_model');
        $this->lang->load('overtime', $this->language);
        $this->lang->load('global', $this->language);
    }

    /**
     * Display the list of all overtime requests submitted to you (Status is submitted)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index($filter = 'requested') {
        $this->auth->check_is_granted('list_overtime');
        if ($filter == 'all') {
            $showAll = true;
        } else {
            $showAll = false;
        }
        
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['filter'] = $filter;
        $data['title'] = lang('overtime_index_title');
        $data['requests'] = $this->overtime_model->requests($this->user_id, $showAll);
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, true);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('overtime/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Accept a leave request
     * @param int $id leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function accept($id) {
        $this->auth->check_is_granted('accept_overtime');
        $this->load->model('users_model');
        $this->load->model('delegations_model');
        $extra = $this->overtime_model->getExtras($id);
        if (empty($extra)) {
            show_404();
        }
        $employee = $this->users_model->getUsers($extra['employee']);
        $is_delegate = $this->delegations_model->IsDelegate($this->user_id, $employee['manager']);
        if (($this->user_id == $employee['manager']) || ($this->is_hr)  || ($is_delegate)) {
            $this->overtime_model->accept_extra($id);
            $this->sendMail($id);
            $this->session->set_flashdata('msg', lang('overtime_accept_flash_msg_success'));
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('overtime');
            }
        } else {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to accept extra #' . $id);
            $this->session->set_flashdata('msg', lang('overtime_accept_flash_msg_error'));
            redirect('leaves');
        }
    }

    /**
     * Reject a leave request
     * @param int $id leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function reject($id) {
        $this->auth->check_is_granted('reject_overtime');
        $this->load->model('users_model');
        $this->load->model('delegations_model');
        $extra = $this->overtime_model->getExtras($id);
        if (empty($extra)) {
            show_404();
        }
        $employee = $this->users_model->getUsers($extra['employee']);
        $is_delegate = $this->delegations_model->IsDelegate($this->user_id, $employee['manager']);
        if (($this->user_id == $employee['manager']) || ($this->is_hr)  || ($is_delegate)) {
            $this->overtime_model->reject_extra($id);
            $this->sendMail($id);
            $this->session->set_flashdata('msg', lang('overtime_reject_flash_msg_success'));
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('overtime');
            }
        } else {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to reject extra #' . $id);
            $this->session->set_flashdata('msg', lang('overtime_reject_flash_msg_error'));
            redirect('leaves');
        }
    }
    
    /**
     * Send a overtime request email to the employee that requested the leave
     * The method will check if the leave request wes accepted or rejected 
     * before sending the e-mail
     * @param int $id Leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function sendMail($id)
    {
        $this->load->model('users_model');
        $this->load->model('organization_model');
        $extra = $this->overtime_model->getExtras($id);
        //Load details about the employee (manager, supervisor of entity)
        $employee = $this->users_model->getUsers($extra['employee']);
        $supervisor = $this->organization_model->get_supervisor($employee['organization']);

        //Send an e-mail to the employee
        $this->load->library('email');
        $this->load->library('polyglot');
        $usr_lang = $this->polyglot->code2language($employee['language']);
        
        //We need to instance an different object as the languages of connected user may differ from the UI lang
        $lang_mail = new CI_Lang();
        $lang_mail->load('email', $usr_lang);
        $lang_mail->load('global', $usr_lang);
        
        $date = new DateTime($extra['startdate']);
        $startdate = $date->format($lang_mail->line('global_date_format'));

        $this->load->library('parser');
        $data = array(
            'Title' => $lang_mail->line('email_overtime_request_validation_title'),
            'Firstname' => $employee['firstname'],
            'Lastname' => $employee['lastname'],
            'Date' => $startdate,
            'Duration' => $extra['duration'],
            'Cause' => $extra['cause']
        );
        
        $message = "";
        if ($this->config->item('subject_prefix') != FALSE) {
            $subject = $this->config->item('subject_prefix');
        } else {
           $subject = '[Jorani] ';
        }
        if ($extra['status'] == 3) {
            $message = $this->parser->parse('emails/' . $employee['language'] . '/overtime_accepted', $data, TRUE);
            $this->email->subject($subject . $lang_mail->line('email_overtime_request_accept_subject'));
        } else {
            $message = $this->parser->parse('emails/' . $employee['language'] . '/overtime_rejected', $data, TRUE);
            $this->email->subject($subject . $lang_mail->line('email_overtime_request_reject_subject'));
        }
        $this->email->set_encoding('quoted-printable');
        
        if ($this->config->item('from_mail') != FALSE && $this->config->item('from_name') != FALSE ) {
            $this->email->from($this->config->item('from_mail'), $this->config->item('from_name'));
        } else {
           $this->email->from('do.not@reply.me', 'LMS');
        }
        $this->email->to($employee['email']);
        if (!is_null($supervisor)) {
            $this->email->cc($supervisor->email);
        }
        $this->email->message($message);
        $this->email->send();
    }
    
    /**
     * Export the list of all overtime requests (sent to the connected user) into an Excel file
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export($filter = 'requested') {
        $this->load->library('excel');
        $data['filter'] = $filter;
        $this->load->view('overtime/export', $data);
    }
}
