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

class Leaves extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->load->model('leaves_model');
        $this->load->model('types_model');
        $this->lang->load('leaves', $this->language);
        $this->lang->load('global', $this->language);
    }
    
    /**
     * Display the list of the leave requests of the connected user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->check_is_granted('list_leaves');
        expires_now();
        $data = getUserContext($this);
        $data['leaves'] = $this->leaves_model->get_user_leaves($this->session->userdata('id'));
        $this->load->model('status_model');
        for ($i = 0; $i < count($data['leaves']); ++$i) {
            $data['leaves'][$i]['status_label'] = $this->status_model->get_label($data['leaves'][$i]['status']);
            $data['leaves'][$i]['type_label'] = $this->types_model->get_label($data['leaves'][$i]['type']);
        }
        $data['title'] = lang('leaves_index_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_leave_requests_list');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leaves/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the details of leaves taken/entitled for the connected user
     * @param string $refTmp Timestamp (reference date)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function counters($refTmp = NULL) {
        $this->auth->check_is_granted('counters_leaves');
        $data = getUserContext($this);
        $refDate = date("Y-m-d");
        if ($refTmp != NULL) {
            $refDate = date("Y-m-d", $refTmp);
            $data['isDefault'] = 0;
        } else {
            $data['isDefault'] = 1;
        }
        $data['refDate'] = $refDate;
        $data['summary'] = $this->leaves_model->get_user_leaves_summary($this->user_id, FALSE, $refDate);

        if (!is_null($data['summary'])) {
            expires_now();
            $data['title'] = lang('leaves_summary_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_my_summary');
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/counters', $data);
            $this->load->view('templates/footer');
        } else {
            $this->session->set_flashdata('msg', lang('leaves_summary_flash_msg_error'));
            redirect('leaves');
        }
    }

    /**
     * Display a leave request
     * @param int $id identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function view($id) {
        $this->auth->check_is_granted('view_leaves');
        expires_now();
        $data = getUserContext($this);
        $data['leave'] = $this->leaves_model->get_leaves($id);
        $this->load->model('status_model');
        if (empty($data['leave'])) {
            show_404();
        }
        $data['types'] = $this->types_model->get_types();
        $data['leave']['status_label'] = $this->status_model->get_label($data['leave']['status']);
        $data['leave']['type_label'] = $this->types_model->get_label($data['leave']['type']);
        $data['title'] = lang('leaves_view_html_title');
        $this->load->model('users_model');
        $data['name'] = $this->users_model->get_label($data['leave']['employee']);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leaves/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Create a leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        $this->auth->check_is_granted('create_leaves');
        expires_now();
        $data = getUserContext($this);
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('leaves_create_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_request_leave');
        
        $this->form_validation->set_rules('startdate', lang('leaves_create_field_start'), 'required|xss_clean');
        $this->form_validation->set_rules('startdatetype', 'Start Date type', 'required|xss_clean');
        $this->form_validation->set_rules('enddate', lang('leaves_create_field_end'), 'required|xss_clean');
        $this->form_validation->set_rules('enddatetype', 'End Date type', 'required|xss_clean');
        $this->form_validation->set_rules('duration', lang('leaves_create_field_duration'), 'required|xss_clean');
        $this->form_validation->set_rules('type', lang('leaves_create_field_type'), 'required|xss_clean');
        $this->form_validation->set_rules('cause', lang('leaves_create_field_cause'), 'xss_clean');
        $this->form_validation->set_rules('status', lang('leaves_create_field_status'), 'required|xss_clean');

        $data['credit'] = 0;
        $default_type = $this->config->item('default_leave_type');
        $default_type = $default_type == FALSE ? 0 : $default_type;
        if ($this->form_validation->run() === FALSE) {
            $data['types'] = $this->types_model->get_types();
            foreach ($data['types'] as $type) {
                if ($type['id'] == $default_type) {
                    $data['credit'] = $this->leaves_model->get_user_leaves_credit($this->user_id, $type['name']);
                    break;
                }
            }
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/create');
            $this->load->view('templates/footer');
        } else {
            $leave_id = $this->leaves_model->set_leaves($this->session->userdata('id'));
            $this->session->set_flashdata('msg', lang('leaves_create_flash_msg_success'));
            //If the status is requested, send an email to the manager
            if ($this->input->post('status') == 2) {
                $this->sendMail($leave_id);
            }
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('leaves');
            }
        }
    }
    
    /**
     * Edit a leave request
     * @param int $id Identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($id) {
        $this->auth->check_is_granted('edit_leaves');
        expires_now();
        $data = getUserContext($this);
        $data['leave'] = $this->leaves_model->get_leaves($id);
        //Check if exists
        if (empty($data['leave'])) {
            show_404();
        }
        //If the user is not its own manager and if the leave is 
        //already requested, the employee can't modify it
        if (!$this->is_hr) {
            if (($this->session->userdata('manager') != $this->user_id) &&
                    $data['leave']['status'] != 1) {
                log_message('error', 'User #' . $this->user_id . ' illegally tried to edit leave #' . $id);
                $this->session->set_flashdata('msg', lang('leaves_edit_flash_msg_error'));
                redirect('leaves');
            }
        } //Admin
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('leaves_edit_html_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_request_leave');
        $data['id'] = $id;
        
        $data['credit'] = 0;
        $data['types'] = $this->types_model->get_types();
        foreach ($data['types'] as $type) {
            if ($type['id'] == $data['leave']['type']) {
                $data['credit'] = $this->leaves_model->get_user_leaves_credit($data['leave']['employee'], $type['name']);
                break;
            }
        }
        
        $this->form_validation->set_rules('startdate', lang('leaves_edit_field_start'), 'required|xss_clean');
        $this->form_validation->set_rules('startdatetype', 'Start Date type', 'required|xss_clean');
        $this->form_validation->set_rules('enddate', lang('leaves_edit_field_end'), 'required|xss_clean');
        $this->form_validation->set_rules('enddatetype', 'End Date type', 'required|xss_clean');
        $this->form_validation->set_rules('duration', lang('leaves_edit_field_duration'), 'required|xss_clean');
        $this->form_validation->set_rules('type', lang('leaves_edit_field_type'), 'required|xss_clean');
        $this->form_validation->set_rules('cause', lang('leaves_edit_field_cause'), 'xss_clean');
        $this->form_validation->set_rules('status', lang('leaves_edit_field_status'), 'required|xss_clean');

        if ($this->form_validation->run() === FALSE) {
            $this->load->model('users_model');
            $data['name'] = $this->users_model->get_label($data['leave']['employee']);
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $leave_id = $this->leaves_model->update_leaves($id);
            $this->session->set_flashdata('msg', lang('leaves_edit_flash_msg_success'));
            //If the status is requested, send an email to the manager
            if ($this->input->post('status') == 2) {
                $this->sendMail($id);
            }
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('leaves');
            }
        }
    }
    
    /**
     * Send a leave request email to the manager of the connected employee
     * @param int $id Leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function sendMail($id) {
        $this->load->model('users_model');
        $this->load->model('delegations_model');
        $manager = $this->users_model->get_users($this->session->userdata('manager'));

        //Test if the manager hasn't been deleted meanwhile
        if (empty($manager['email'])) {
            $this->session->set_flashdata('msg', lang('leaves_create_flash_msg_error'));
        } else {
            //Send an e-mail to the manager
            $this->load->library('email');
            $this->load->library('polyglot');
            $usr_lang = $this->polyglot->code2language($manager['language']);
            $this->lang->load('email', $usr_lang);

            $this->lang->load('global', $usr_lang);
            $date = new DateTime($this->input->post('startdate'));
            $startdate = $date->format(lang('global_date_format'));
            $date = new DateTime($this->input->post('enddate'));
            $enddate = $date->format(lang('global_date_format'));

            $this->load->library('parser');
            $data = array(
                'Title' => lang('email_leave_request_title'),
                'Firstname' => $this->session->userdata('firstname'),
                'Lastname' => $this->session->userdata('lastname'),
                'StartDate' => $startdate,
                'EndDate' => $enddate,
                'Type' => $this->types_model->get_label($this->input->post('type')),
                'Reason' => $this->input->post('cause'),
                'BaseUrl' => $this->config->base_url(),
                'LeaveId' => $id,
                'UserId' => $this->user_id
            );
            $message = $this->parser->parse('emails/' . $manager['language'] . '/request', $data, TRUE);
            if ($this->email->mailer_engine == 'phpmailer') {
                $this->email->phpmailer->Encoding = 'quoted-printable';
            }
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
            
            $this->email->subject($subject . lang('email_leave_request_subject') .
                    $this->session->userdata('firstname') . ' ' .
                    $this->session->userdata('lastname'));
            $this->email->message($message);
            $this->email->send();
        }
    }

    /**
     * Delete a leave request
     * @param int $id identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) {
        $can_delete = false;
        //Test if the leave request exists
        $leaves = $this->leaves_model->get_leaves($id);
        if (empty($leaves)) {
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
                $this->leaves_model->delete_leave($id);
            } else {
                $this->session->set_flashdata('msg', lang('leaves_delete_flash_msg_error'));
                if (isset($_GET['source'])) {
                    redirect($_GET['source']);
                } else {
                    redirect('leaves');
                }
            }
        }
        $this->session->set_flashdata('msg', lang('leaves_delete_flash_msg_success'));
        if (isset($_GET['source'])) {
            redirect($_GET['source']);
        } else {
            redirect('leaves');
        }
    }

    /**
     * Export the list of all leaves into an Excel file
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export() {
        expires_now();
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);

        $this->excel->getActiveSheet()->setTitle(lang('leaves_export_title'));
        $this->excel->getActiveSheet()->setCellValue('A1', lang('leaves_export_thead_id'));
        $this->excel->getActiveSheet()->setCellValue('B1', lang('leaves_export_thead_start_date'));
        $this->excel->getActiveSheet()->setCellValue('C1', lang('leaves_export_thead_start_date_type'));
        $this->excel->getActiveSheet()->setCellValue('D1', lang('leaves_export_thead_end_date'));
        $this->excel->getActiveSheet()->setCellValue('E1', lang('leaves_export_thead_end_date_type'));
        $this->excel->getActiveSheet()->setCellValue('F1', lang('leaves_export_thead_duration'));
        $this->excel->getActiveSheet()->setCellValue('G1', lang('leaves_export_thead_type'));
        $this->excel->getActiveSheet()->setCellValue('H1', lang('leaves_export_thead_status'));
        $this->excel->getActiveSheet()->setCellValue('I1', lang('leaves_export_thead_cause'));
        $this->excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $leaves = $this->leaves_model->get_user_leaves($this->user_id);
        $this->load->model('status_model');
        
        $line = 2;
        foreach ($leaves as $leave) {
            $date = new DateTime($leave['startdate']);
            $startdate = $date->format(lang('global_date_format'));
            $date = new DateTime($leave['enddate']);
            $enddate = $date->format(lang('global_date_format'));
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $leave['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $startdate);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, lang($leave['startdatetype']));
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $enddate);
            $this->excel->getActiveSheet()->setCellValue('E' . $line, lang($leave['enddatetype']));
            $this->excel->getActiveSheet()->setCellValue('F' . $line, $leave['duration']);
            $this->excel->getActiveSheet()->setCellValue('G' . $line, $this->types_model->get_label($leave['type']));
            $this->excel->getActiveSheet()->setCellValue('H' . $line, lang($this->status_model->get_label($leave['status'])));
            $this->excel->getActiveSheet()->setCellValue('I' . $line, $leave['cause']);
            $line++;
        }
        
        //Autofit
        foreach(range('A', 'I') as $colD) {
            $this->excel->getActiveSheet()->getColumnDimension($colD)->setAutoSize(TRUE);
        }

        $filename = 'leaves.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @param int $id employee id or connected user (from session)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual($id = 0) {
        expires_now();
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        if ($id == 0) $id =$this->session->userdata('id');
        echo $this->leaves_model->individual($id, $start, $end);
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function workmates() {
        expires_now();
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        echo $this->leaves_model->workmates($this->session->userdata('manager'), $start, $end);
    }
    
    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators() {
        expires_now();
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        echo $this->leaves_model->collaborators($this->user_id, $start, $end);
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @param int $entity_id Entity identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function organization($entity_id) {
        expires_now();
        header("Content-Type: application/json");
        $this->load->model('organization_model');
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        $children = filter_var($this->input->get('children', TRUE), FILTER_VALIDATE_BOOLEAN);
        echo $this->leaves_model->department($entity_id, $start, $end, $children);
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function department() {
        expires_now();
        header("Content-Type: application/json");
        $this->load->model('organization_model');
        $department = $this->organization_model->get_department($this->user_id);
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        echo $this->leaves_model->department($department[0]['id'], $start, $end);
    }
    
    /**
     * Ajax endpoint. Result varies according to input :
     *  - difference between the entitled and the taken days
     *  - try to calculate the duration of the leave
     *  - try to detect overlapping leave requests
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function validate() {
        expires_now();
        header("Content-Type: application/json");
        $id = $this->input->post('id', TRUE);
        $type = $this->input->post('type', TRUE);
        $startdate = $this->input->post('startdate', TRUE);
        $enddate = $this->input->post('enddate', TRUE);
        $startdatetype = $this->input->post('startdatetype', TRUE);
        $enddatetype = $this->input->post('enddatetype', TRUE);
        $leave_id = $this->input->post('leave_id', TRUE);
        $leaveValidator = new stdClass;
        if (isset($id) && isset($type)) {
            $leaveValidator->credit = $this->leaves_model->get_user_leaves_credit($id, $type);
        }
        if (isset($id) && isset($startdate) && isset($enddate)) {
            $leaveValidator->length = $this->leaves_model->length($id, $startdate, $enddate);
            if (isset($startdatetype) && isset($enddatetype)) {
                if (isset($leave_id)) {
                    $leaveValidator->overlap = $this->leaves_model->detect_overlapping_leaves($id, $startdate, $enddate, $startdatetype, $enddatetype, $leave_id);
                } else {
                    $leaveValidator->overlap = $this->leaves_model->detect_overlapping_leaves($id, $startdate, $enddate, $startdatetype, $enddatetype);
                }
            }
        }
        echo json_encode($leaveValidator);
    }
}
