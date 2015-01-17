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

class Requests extends CI_Controller {
    
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
        $this->load->model('leaves_model');
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
        $this->language = $this->session->userdata('language');
        $this->language_code = $this->session->userdata('language_code');
        $this->load->helper('language');
        $this->lang->load('requests', $this->language);
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
     * Display the list of all requests submitted to you
     * Status is submitted
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index($filter = 'requested') {
        $this->auth->check_is_granted('list_requests');
        $this->expires_now();
        if ($filter == 'all') {
            $showAll = true;
        } else {
            $showAll = false;
        }
        
        $data = $this->getUserContext();
        $data['filter'] = $filter;
        $data['title'] = lang('requests_index_title');
        $data['requests'] = $this->leaves_model->requests($this->user_id, $showAll);
        
        $this->load->model('types_model');
        for ($i = 0; $i < count($data['requests']); ++$i) {
            $data['requests'][$i]['type_label'] = $this->types_model->get_label($data['requests'][$i]['type']);
        }
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('requests/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Accept a leave request
     * @param int $id leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function accept($id) {
        $this->auth->check_is_granted('accept_requests');
        $this->load->model('users_model');
        $leave = $this->leaves_model->get_leaves($id);
        if (empty($leave)) {
            show_404();
        }
        $employee = $this->users_model->get_users($leave['employee']);
        if (($this->user_id != $employee['manager']) && ($this->is_hr == false)) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to accept leave #' . $id);
            $this->session->set_flashdata('msg', lang('requests_accept_flash_msg_error'));
            redirect('home');
        } else {
            $this->leaves_model->accept_leave($id);
            $this->sendMail($id);
            $this->session->set_flashdata('msg', lang('requests_accept_flash_msg_success'));
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('requests');
            }
        }
    }

    /**
     * Reject a leave request
     * @param int $id leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function reject($id) {
        $this->auth->check_is_granted('reject_requests');
        $this->load->model('users_model');
        $leave = $this->leaves_model->get_leaves($id);
        if (empty($leave)) {
            show_404();
        }
        $employee = $this->users_model->get_users($leave['employee']);
        if (($this->user_id != $employee['manager']) && ($this->is_hr == false)) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to reject leave #' . $id);
            $this->session->set_flashdata('msg', lang('requests_reject_flash_msg_error'));
            redirect('home');
        } else {
            $this->leaves_model->reject_leave($id);
            $this->sendMail($id);
            $this->session->set_flashdata('msg',  lang('requests_reject_flash_msg_success'));
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('requests');
            }
        }
    }
    
        /**
     * Display the list of all requests submitted to you
     * Status is submitted
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators() {
        $this->auth->check_is_granted('list_collaborators');
        $this->expires_now();
        $data = $this->getUserContext();
        $data['title'] = lang('requests_collaborators_title');
        $this->load->model('users_model');
        $data['collaborators'] = $this->users_model->get_employees_manager($this->user_id);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('requests/collaborators', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the details of leaves taken/entitled for a given employee
     * This page can be displayed only if the connected user is the manager of the employee
     * @param string $refTmp Timestamp (reference date)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function counters($id, $refTmp = NULL) {
        $data = $this->getUserContext();
        $this->load->model('users_model');
        $employee = $this->users_model->get_users($id);
        if (($this->user_id != $employee['manager']) && ($this->is_hr == false)) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to access to leave counter of employee #' . $id);
            $this->session->set_flashdata('msg', lang('requests_summary_flash_msg_forbidden'));
            redirect('requests/collaborators');
        } else {
            $refDate = date("Y-m-d");
            if ($refTmp != NULL) {
                $refDate = date("Y-m-d", $refTmp);
            }

            $data['refDate'] = $refDate;
            $data['summary'] = $this->leaves_model->get_user_leaves_summary($id, FALSE, $refDate);
            
            if (!is_null($data['summary'])) {
                $this->load->model('entitleddays_model');
                $this->load->model('types_model');
                $data['types'] = $this->types_model->get_types();
                $this->load->model('users_model');
                $data['employee_name'] = $this->users_model->get_label($id);
                $user = $this->users_model->get_users($id);
                $this->load->model('contracts_model');
                $contract = $this->contracts_model->get_contracts($user['contract']);
                $data['contract_name'] = $contract['name'];
                $data['contract_start'] = $contract['startentdate'];
                $data['contract_end'] = $contract['endentdate'];
                $data['employee_id'] = $id;
                $data['contract_id'] = $user['contract'];
                $data['entitleddayscontract'] = $this->entitleddays_model->get_entitleddays_contract($user['contract']);
                $data['entitleddaysemployee'] = $this->entitleddays_model->get_entitleddays_employee($id);
                
                $this->expires_now();
                $data['title'] = lang('hr_summary_title');
                $this->load->view('templates/header', $data);
                $this->load->view('menu/index', $data);
                $this->load->view('requests/counters', $data);
                $this->load->view('templates/footer');
            } else {
                $this->session->set_flashdata('msg', lang('requests_summary_flash_msg_error'));
                redirect('requests/collaborators');
            }
        }
    }

    /**
     * Send a leave request email to the employee that requested the leave
     * The method will check if the leave request wes accepted or rejected 
     * before sending the e-mail
     * @param int $id Leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function sendMail($id)
    {
        $this->load->model('users_model');
        $this->load->model('organization_model');
        $this->load->model('settings_model');
        $leave = $this->leaves_model->get_leaves($id);
        //Load details about the employee (manager, supervisor of entity)
        $employee = $this->users_model->get_users($leave['employee']);
        $supervisor = $this->organization_model->get_supervisor($employee['organization']);

        //Send an e-mail to the employee
        $this->load->library('email');
        $this->load->library('polyglot');
        $usr_lang = $this->polyglot->code2language($employee['language']);
        $this->lang->load('email', $usr_lang);

        $this->lang->load('global', $usr_lang);
        $date = new DateTime($leave['startdate']);
        $startdate = $date->format(lang('global_date_format'));
        $date = new DateTime($leave['enddate']);
        $enddate = $date->format(lang('global_date_format'));

        $this->load->library('parser');
        $data = array(
            'Title' => lang('email_leave_request_validation_title'),
            'Firstname' => $employee['firstname'],
            'Lastname' => $employee['lastname'],
            'StartDate' => $startdate,
            'EndDate' => $enddate
        );
        
        $message = "";
        if ($leave['status'] == 3) {
            $message = $this->parser->parse('emails/' . $employee['language'] . '/request_accepted', $data, TRUE);
            $this->email->subject(lang('email_leave_request_accept_subject'));
        } else {
            $message = $this->parser->parse('emails/' . $employee['language'] . '/request_rejected', $data, TRUE);
            $this->email->subject(lang('email_leave_request_reject_subject'));
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
     * Action: export the list of all leave requests into an Excel file
     */
    public function export($filter = 'requested') {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);

        $this->excel->getActiveSheet()->setTitle(lang('requests_export_title'));
        $this->excel->getActiveSheet()->setCellValue('A1', lang('requests_export_thead_id'));
        $this->excel->getActiveSheet()->setCellValue('B1', lang('requests_export_thead_fullname'));
        $this->excel->getActiveSheet()->setCellValue('C1', lang('requests_export_thead_startdate'));
        $this->excel->getActiveSheet()->setCellValue('D1', lang('requests_export_thead_startdate_type'));
        $this->excel->getActiveSheet()->setCellValue('E1', lang('requests_export_thead_enddate'));
        $this->excel->getActiveSheet()->setCellValue('F1', lang('requests_export_thead_enddate_type'));
        $this->excel->getActiveSheet()->setCellValue('G1', lang('requests_export_thead_duration'));
        $this->excel->getActiveSheet()->setCellValue('H1', lang('requests_export_thead_type'));
        $this->excel->getActiveSheet()->setCellValue('I1', lang('requests_export_thead_cause'));
        $this->excel->getActiveSheet()->setCellValue('J1', lang('requests_export_thead_status'));
        $this->excel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        if ($filter == 'all') {
            $showAll = true;
        } else {
            $showAll = false;
        }
        $requests = $this->leaves_model->requests($this->user_id, $showAll);
        $this->load->model('status_model');
        $this->load->model('types_model');
        
        $line = 2;
        foreach ($requests as $request) {
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $request['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $request['firstname'] . ' ' . $request['lastname']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $request['startdate']);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $request['startdatetype']);
            $this->excel->getActiveSheet()->setCellValue('E' . $line, $request['enddate']);
            $this->excel->getActiveSheet()->setCellValue('F' . $line, $request['enddatetype']);
            $this->excel->getActiveSheet()->setCellValue('G' . $line, $request['duration']);
            $this->excel->getActiveSheet()->setCellValue('H' . $line, $this->types_model->get_label($request['type']));
            $this->excel->getActiveSheet()->setCellValue('I' . $line, $request['cause']);
            $this->excel->getActiveSheet()->setCellValue('J' . $line, $this->status_model->get_label($request['status']));
            $line++;
        }

        $filename = 'requests.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
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
