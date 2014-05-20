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

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Overtime extends CI_Controller {
    
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
        $this->load->model('overtime_model');
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_admin = $this->session->userdata('is_admin');
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
        $this->language = $this->session->userdata('language');
        $this->language_code = $this->session->userdata('language_code');
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
     * Display the list of all overtime requests submitted to you
     * Status is submitted
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index($filter = 'requested') {
        $this->auth->check_is_granted('list_overtime');
        if ($filter == 'all') {
            $showAll = true;
        } else {
            $showAll = false;
        }
        
        $data = $this->getUserContext();
        $data['filter'] = $filter;
        $data['title'] = 'List of requested overtime';
        $data['requests'] = $this->overtime_model->requests($this->user_id, $showAll);
        
        $this->load->model('status_model');
        for ($i = 0; $i < count($data['requests']); ++$i) {
            $data['requests'][$i]['status_label'] = $this->status_model->get_label($data['requests'][$i]['status']);
        }
        
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
        $extra = $this->overtime_model->get_extra($id);
        if (empty($extra)) {
            show_404();
        }
        $employee = $this->users_model->get_users($extra['employee']);
        if ($this->user_id != $employee['manager']) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to accept leave #' . $id);
            $this->session->set_flashdata('msg', 'You are not the manager of this employee. You cannot validate this leave request.');
            redirect('home');
        } else {
            $this->overtime_model->accept_extra($id);
            $this->sendMail($id);
            $this->session->set_flashdata('msg', 'The overtime request has been successfully accepted.');
            log_message('debug', '{controllers/requests/accept} Leaving method (before redirect)');
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('overtime');
            }
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
        $extra = $this->overtime_model->get_extra($id);
        if (empty($extra)) {
            show_404();
        }
        $employee = $this->users_model->get_users($extra['employee']);
        if ($this->user_id != $employee['manager']) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to reject leave #' . $id);
            $this->session->set_flashdata('msg', 'You are not the manager of this employee. You cannot validate this leave request.');
            redirect('home');
        } else {
            $this->overtime_model->reject_extra($id);
            $this->sendMail($id);
            $this->session->set_flashdata('msg', 'The overtime request has been successfully rejected.');
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('overtime');
            }
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
        $this->load->model('settings_model');
        $extra = $this->overtime_model->get_extra($id);
        $employee = $this->users_model->get_users($extra['employee']);

        //Send an e-mail to the employee
        $this->load->library('email');
        $config = $this->settings_model->get_mail_config();            
        $this->email->initialize($config);

        $this->load->library('parser');
        $data = array(
            'Title' => 'Leave Request',
            'Firstname' => $employee['firstname'],
            'Lastname' => $employee['lastname'],
            'Date' => $leave['startdate'],
            'Duration' => $leave['enddate']
        );
        
        $message = "";
        if ($extra['status'] == 3) {
            $message = $this->parser->parse('emails/overtime_accepted', $data, TRUE);
            $this->email->subject('[LMS] Your overtime request has been accepted');
        } else {
            $message = $this->parser->parse('emails/overtime_rejected', $data, TRUE);
            $this->email->subject('[LMS] Your overtime request has been rejected');
        }

        $this->email->from('do.not@reply.me', 'LMS');
        $this->email->to($employee['email']);
        $this->email->message($message);
        $this->email->send();
        //echo $this->email->print_debugger();
    }
    
    /**
     * Action: export the list of all leave requests into an Excel file
     */
    public function export($filter = 'requested') {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('List of overtime resquests');
        $this->excel->getActiveSheet()->setCellValue('A1', 'ID');
        $this->excel->getActiveSheet()->setCellValue('B1', 'Fullname');
        $this->excel->getActiveSheet()->setCellValue('C1', 'Date');
        $this->excel->getActiveSheet()->setCellValue('D1', 'Duration');
        $this->excel->getActiveSheet()->setCellValue('E1', 'Cause');
        $this->excel->getActiveSheet()->setCellValue('F1', 'Status');
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
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $request['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $request['firstname'] . ' ' . $request['lastname']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $request['date']);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $request['duration']);
            $this->excel->getActiveSheet()->setCellValue('E' . $line, $request['cause']);
            $this->excel->getActiveSheet()->setCellValue('F' . $line, $this->status_model->get_label($request['status']));
            $line++;
        }

        $filename = 'overtime.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
}
