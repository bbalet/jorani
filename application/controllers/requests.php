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
        setUserContext($this);
        $this->load->model('leaves_model');
        $this->lang->load('requests', $this->language);
        $this->lang->load('global', $this->language);
    }

    /**
     * Display the list of all requests submitted to you
     * Status is submitted
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index($filter = 'requested') {
        $this->auth->check_is_granted('list_requests');
        expires_now();
        if ($filter == 'all') {
            $showAll = true;
        } else {
            $showAll = false;
        }
        
        $data = getUserContext($this);
        $data['filter'] = $filter;
        $data['title'] = lang('requests_index_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_leave_validation');
        $data['requests'] = $this->leaves_model->requests($this->user_id, $showAll);
        
        $this->load->model('types_model');
        $this->load->model('status_model');
        for ($i = 0; $i < count($data['requests']); ++$i) {
            $data['requests'][$i]['status_label'] = $this->status_model->get_label($data['requests'][$i]['status']);
        }
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, true);
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
        $this->load->model('delegations_model');
        $leave = $this->leaves_model->get_leaves($id);
        if (empty($leave)) {
            show_404();
        }
        $employee = $this->users_model->get_users($leave['employee']);
        $is_delegate = $this->delegations_model->IsDelegate($this->user_id, $employee['manager']);
        if (($this->user_id == $employee['manager']) || ($this->is_hr)  || ($is_delegate)) {
            $this->leaves_model->accept_leave($id);
            $this->sendMail($id);
            $this->session->set_flashdata('msg', lang('requests_accept_flash_msg_success'));
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('requests');
            }
        } else {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to accept leave #' . $id);
            $this->session->set_flashdata('msg', lang('requests_accept_flash_msg_error'));
            redirect('leaves');
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
        $this->load->model('delegations_model');
        $leave = $this->leaves_model->get_leaves($id);
        if (empty($leave)) {
            show_404();
        }
        $employee = $this->users_model->get_users($leave['employee']);
        $is_delegate = $this->delegations_model->IsDelegate($this->user_id, $employee['manager']);
        if (($this->user_id == $employee['manager']) || ($this->is_hr)  || ($is_delegate)) {
            $this->leaves_model->reject_leave($id);
            $this->sendMail($id);
            $this->session->set_flashdata('msg',  lang('requests_reject_flash_msg_success'));
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('requests');
            }
        } else {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to reject leave #' . $id);
            $this->session->set_flashdata('msg', lang('requests_reject_flash_msg_error'));
            redirect('leaves');
        }
    }
    
        /**
     * Display the list of all requests submitted to you
     * Status is submitted
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators() {
        $this->auth->check_is_granted('list_collaborators');
        expires_now();
        $data = getUserContext($this);
        $data['title'] = lang('requests_collaborators_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_collaborators_list');
        $this->load->model('users_model');
        $data['collaborators'] = $this->users_model->get_employees_manager($this->user_id);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('requests/collaborators', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the list of delegations
     * @param int $id Identifier of the manager (from HR/Employee) or 0 if self
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delegations($id = 0) {
        if ($id == 0) $id = $this->user_id;
        //Self modification or by HR
        if (($this->user_id == $id) || ($this->is_hr)) {
            expires_now();
            $data = getUserContext($this);
            $data['title'] = lang('requests_delegations_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_delegations');
            $this->load->model('users_model');
            $data['name'] = $this->users_model->get_label($id);
            $data['id'] = $id;
            $this->load->model('delegations_model');
            $data['delegations'] = $this->delegations_model->get_delegates($id);
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('requests/delegations', $data);
            $this->load->view('templates/footer');
        } else {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to access to list_delegations');
            $this->session->set_flashdata('msg', sprintf(lang('global_msg_error_forbidden'), 'list_delegations'));
            redirect('leaves');
        }
    }
    
    /**
     * Ajax endpoint : Delete a delegation for a manager
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delegations_delete() {
        $manager = $this->input->post('manager_id', TRUE);
        $delegation = $this->input->post('delegation_id', TRUE);
        if (($this->user_id != $manager) && ($this->is_hr == FALSE)) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            if (isset($manager) && isset($delegation)) {
                $this->output->set_content_type('text/plain');
                $this->load->model('delegations_model');
                $id = $this->delegations_model->delete_delegation($delegation);
                echo $id;
            } else {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            }
        }
    }
    
    /**
     * Ajax endpoint : Add a delegation for a manager
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delegations_add() {
        $manager = $this->input->post('manager_id', TRUE);
        $delegate = $this->input->post('delegate_id', TRUE);
        if (($this->user_id != $manager) && ($this->is_hr == FALSE)) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            if (isset($manager) && isset($delegate)) {
                $this->output->set_content_type('text/plain');
                $this->load->model('delegations_model');
                if (!$this->delegations_model->IsDelegate($delegate, $manager)) {
                    $id = $this->delegations_model->add_delegate($manager, $delegate);
                    echo $id;
                } else {
                    echo 'null';
                }
            } else {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            }
        }
    }
    
    /**
     * Display the details of leaves taken/entitled for a given employee
     * This page can be displayed only if the connected user is the manager of the employee
     * @param string $refTmp Timestamp (reference date)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function counters($id, $refTmp = NULL) {
        $data = getUserContext($this);
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
                $data['isDefault'] = 0;
            } else {
                $data['isDefault'] = 1;
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
                
                expires_now();
                $data['title'] = lang('requests_summary_title');
                $data['help'] = $this->help->create_help_link('global_link_doc_page_leave_balance_collaborators');
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
        $leave = $this->leaves_model->get_leave_details($id);
        //Load details about the employee (manager, supervisor of entity)
        $supervisor = $this->organization_model->get_supervisor($leave['organization']);

        //Send an e-mail to the employee
        $this->load->library('email');
        $this->load->library('polyglot');
        $usr_lang = $this->polyglot->code2language($leave['language']);
        $this->lang->load('email', $usr_lang);

        $this->lang->load('global', $usr_lang);
        $date = new DateTime($leave['startdate']);
        $startdate = $date->format(lang('global_date_format'));
        $date = new DateTime($leave['enddate']);
        $enddate = $date->format(lang('global_date_format'));

        $this->load->library('parser');
        $data = array(
            'Title' => lang('email_leave_request_validation_title'),
            'Firstname' => $leave['firstname'],
            'Lastname' => $leave['lastname'],
            'StartDate' => $startdate,
            'EndDate' => $enddate,
            'Cause' => $leave['cause'],
            'Type' => $leave['type']
        );
        
        $message = "";
        if ($this->config->item('subject_prefix') != FALSE) {
            $subject = $this->config->item('subject_prefix');
        } else {
           $subject = '[Jorani] ';
        }
        if ($leave['status'] == 3) {
            $message = $this->parser->parse('emails/' . $leave['language'] . '/request_accepted', $data, TRUE);
            $this->email->subject($subject . lang('email_leave_request_accept_subject'));
        } else {
            $message = $this->parser->parse('emails/' . $leave['language'] . '/request_rejected', $data, TRUE);
            $this->email->subject($subject . lang('email_leave_request_reject_subject'));
        }
        if ($this->email->mailer_engine== 'phpmailer') {
            $this->email->phpmailer->Encoding = 'quoted-printable';
        }
        if ($this->config->item('from_mail') != FALSE && $this->config->item('from_name') != FALSE ) {
           $this->email->from($this->config->item('from_mail'), $this->config->item('from_name'));
        } else {
           $this->email->from('do.not@reply.me', 'LMS');
        }
        $this->email->to($leave['email']);
        if (!is_null($supervisor)) {
            $this->email->cc($supervisor->email);
        }
        $this->email->message($message);
        $this->email->send();
    }
    
    /**
     * Action: export the list of all leave requests into an Excel file
     * @author Benjamin BALET <benjamin.balet@gmail.com>
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
            $date = new DateTime($request['startdate']);
            $startdate = $date->format(lang('global_date_format'));
            $date = new DateTime($request['enddate']);
            $enddate = $date->format(lang('global_date_format'));
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $request['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $request['firstname'] . ' ' . $request['lastname']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $startdate);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, lang($request['startdatetype']));
            $this->excel->getActiveSheet()->setCellValue('E' . $line, $enddate);
            $this->excel->getActiveSheet()->setCellValue('F' . $line, lang($request['enddatetype']));
            $this->excel->getActiveSheet()->setCellValue('G' . $line, $request['duration']);
            $this->excel->getActiveSheet()->setCellValue('H' . $line, $this->types_model->get_label($request['type']));
            $this->excel->getActiveSheet()->setCellValue('I' . $line, $request['cause']);
            $this->excel->getActiveSheet()->setCellValue('J' . $line, lang($this->status_model->get_label($request['status'])));
            $line++;
        }
        
        //Autofit
        foreach(range('A', 'J') as $colD) {
            $this->excel->getActiveSheet()->getColumnDimension($colD)->setAutoSize(TRUE);
        }

        $filename = 'requests.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
}
