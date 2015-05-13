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
     * Display the list of all overtime requests submitted to you
     * Status is submitted
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index($filter = 'requested') {
        $this->auth->check_is_granted('list_overtime');
        expires_now();
        if ($filter == 'all') {
            $showAll = true;
        } else {
            $showAll = false;
        }
        
        $data = getUserContext($this);
        $data['filter'] = $filter;
        $data['title'] = lang('overtime_index_title');
        $data['requests'] = $this->overtime_model->requests($this->user_id, $showAll);
        $this->load->model('status_model');
        for ($i = 0; $i < count($data['requests']); ++$i) {
            $data['requests'][$i]['status_label'] = $this->status_model->get_label($data['requests'][$i]['status']);
        }
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
        $extra = $this->overtime_model->get_extra($id);
        if (empty($extra)) {
            show_404();
        }
        $employee = $this->users_model->get_users($extra['employee']);
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
        $extra = $this->overtime_model->get_extra($id);
        if (empty($extra)) {
            show_404();
        }
        $employee = $this->users_model->get_users($extra['employee']);
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
        $extra = $this->overtime_model->get_extra($id);
        //Load details about the employee (manager, supervisor of entity)
        $employee = $this->users_model->get_users($extra['employee']);
        $supervisor = $this->organization_model->get_supervisor($employee['organization']);

        //Send an e-mail to the employee
        $this->load->library('email');
        $this->load->library('polyglot');
        $usr_lang = $this->polyglot->code2language($employee['language']);
        $this->lang->load('email', $usr_lang);
        
        $this->lang->load('global', $usr_lang);
        $date = new DateTime($extra['startdate']);
        $startdate = $date->format(lang('global_date_format'));

        $this->load->library('parser');
        $data = array(
            'Title' => lang('email_overtime_request_validation_title'),
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
            $this->email->subject($subject . lang('email_overtime_request_accept_subject'));
        } else {
            $message = $this->parser->parse('emails/' . $employee['language'] . '/overtime_rejected', $data, TRUE);
            $this->email->subject($subject . lang('email_overtime_request_reject_subject'));
        }
        if ($this->email->mailer_engine== 'phpmailer') {
            $this->email->phpmailer->Encoding = 'quoted-printable';
        }
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
     * Action: export the list of all overtime requests into an Excel file
     */
    public function export($filter = 'requested') {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle(lang('overtime_export_title'));
        $this->excel->getActiveSheet()->setCellValue('A1', lang('overtime_export_thead_id'));
        $this->excel->getActiveSheet()->setCellValue('B1', lang('overtime_export_thead_fullname'));
        $this->excel->getActiveSheet()->setCellValue('C1', lang('overtime_export_thead_date'));
        $this->excel->getActiveSheet()->setCellValue('D1', lang('overtime_export_thead_duration'));
        $this->excel->getActiveSheet()->setCellValue('E1', lang('overtime_export_thead_cause'));
        $this->excel->getActiveSheet()->setCellValue('F1', lang('overtime_export_thead_status'));
        $this->excel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        if ($filter == 'all') {
            $showAll = true;
        } else {
            $showAll = false;
        }
        $requests = $this->overtime_model->requests($this->user_id, $showAll);
        $this->load->model('status_model');
        
        $line = 2;
        foreach ($requests as $request) {
            $date = new DateTime($request['date']);
            $startdate = $date->format(lang('global_date_format'));
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $request['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $request['firstname'] . ' ' . $request['lastname']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $startdate);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $request['duration']);
            $this->excel->getActiveSheet()->setCellValue('E' . $line, $request['cause']);
            $this->excel->getActiveSheet()->setCellValue('F' . $line, lang($this->status_model->get_label($request['status'])));
            $line++;
        }
        
        //Autofit
        foreach(range('A', 'F') as $colD) {
            $this->excel->getActiveSheet()->getColumnDimension($colD)->setAutoSize(TRUE);
        }

        $filename = 'overtime.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
}
