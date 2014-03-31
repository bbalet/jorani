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

class Leaves extends CI_Controller {

    /**
     * Connected user fullname
     * @var string $fullname
     */
    private $fullname;
    
    /**
     * Connected user privilege
     * @var bool true if admin, false otherwise  
     */
    private $is_admin;    
    
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
        $this->is_admin = $this->session->userdata('is_admin');
        $this->load->model('leaves_model');
        $this->user_id = $this->session->userdata('id');
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
        $data['user_id'] =  $this->user_id;
        return $data;
    }

    /**
     * Display the list of the leave requests of the connected user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->check_is_granted('list_leaves');
        $data = $this->getUserContext();
        $data['leaves'] = $this->leaves_model->get_user_leaves($this->session->userdata('id'));
        
        $this->load->model('status_model');
        $this->load->model('types_model');
        for ($i = 0; $i < count($data['leaves']); ++$i) {
            $data['leaves'][$i]['status_label'] = $this->status_model->get_label($data['leaves'][$i]['status']);
            $data['leaves'][$i]['type_label'] = $this->types_model->get_label($data['leaves'][$i]['type']);
        }
        
        $data['title'] = 'My Leave Requests';
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leaves/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display a leave request
     * @param int $id identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function view($id) {
        $this->auth->check_is_granted('view_leaves');
        $data = $this->getUserContext();
        $data['leave'] = $this->leaves_model->get_leaves($id);
        $this->load->model('status_model');
        $this->load->model('types_model');
        if (empty($data['leave'])) {
            show_404();
        }
        $this->load->model('types_model');
        $data['types'] = $this->types_model->get_types();
        $data['leave']['status_label'] = $this->status_model->get_label($data['leave']['status']);
        $data['leave']['type_label'] = $this->types_model->get_label($data['leave']['type']);
        
        $data['title'] = 'Leave details';
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
        $data = $this->getUserContext();
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Request a leave';
        $this->load->model('types_model');
        $data['types'] = $this->types_model->get_types();
        
        $this->form_validation->set_rules('startdate', 'Start Date', 'required|xss_clean');
        $this->form_validation->set_rules('startdatetype', 'Start Date type', 'required|xss_clean');
        $this->form_validation->set_rules('enddate', 'End Date', 'required|xss_clean');
        $this->form_validation->set_rules('enddatetype', 'End Date type', 'required|xss_clean');
        $this->form_validation->set_rules('duration', 'Duration', 'required|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'required|xss_clean');
        $this->form_validation->set_rules('cause', 'Cause', 'xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'required|xss_clean');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/create');
            $this->load->view('templates/footer');
        } else {
            $leave_id = $this->leaves_model->set_leaves();
            //If the status is requested, send an email to the manager
            if ($this->input->post('status') == 2) {
                $this->sendMail($leave_id);
            }            
            $this->session->set_flashdata('msg', 'The leave request has been succesfully created');
            redirect('leaves/index');
        }
    }
    
    /**
     * Edit a leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($id) {
        $this->auth->check_is_granted('edit_leaves');
        $data = $this->getUserContext();
        $data['leave'] = $this->leaves_model->get_leaves($id);
        //Check if exists
        if (empty($data['leave'])) {
            show_404();
        }
        //If the user is not its own manager and if the leave is 
        //already requested, the employee can't modify it
        if (($this->session->userdata('manager') != $this->user_id) &&
                $data['leave']['status'] != 1) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to edit leave #' . $id);
            $this->session->set_flashdata('msg', 'You cannot edit a leave request already submitted');
            redirect('leaves');
        }      
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Edit a leave request';        
        
        $this->load->model('types_model');  
        $data['types'] = $this->types_model->get_types();
                
        $this->form_validation->set_rules('startdate', 'Start Date', 'required|xss_clean');
        $this->form_validation->set_rules('startdatetype', 'Start Date type', 'required|xss_clean');
        $this->form_validation->set_rules('enddate', 'End Date', 'required|xss_clean');
        $this->form_validation->set_rules('enddatetype', 'End Date type', 'required|xss_clean');
        $this->form_validation->set_rules('duration', 'Duration', 'required|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'required|xss_clean');
        $this->form_validation->set_rules('cause', 'Cause', 'xss_clean');
        $this->form_validation->set_rules('status', 'Status', 'required|xss_clean');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $leave_id = $this->leaves_model->set_leaves();
            //If the status is requested, send an email to the manager
            if ($this->input->post('status') == 2) {
                $this->sendMail($leave_id);
            }            
            $this->session->set_flashdata('msg', 'The leave request has been succesfully updated');
            redirect('leaves/index');
        }
    }
    
    /**
     * Send a leave request email to the manager
     * @param int $id Leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function sendMail($id)
    {
        log_message('debug', '{controllers/leaves/sendMail} Entering method with id=' . $id);
        $this->load->model('users_model');
        $this->load->model('settings_model');
        $manager = $this->users_model->get_users($this->session->userdata('manager'));
        $acceptUrl = base_url() . 'requests/accept/' . $id;
        $rejectUrl = base_url() . 'requests/reject/' . $id;

        //Send an e-mail to the manager
        //See: http://www.codeigniter.fr/user_guide/libraries/email.html
        $this->load->library('email');
        $config = $this->settings_model->get_mail_config();            
        $this->email->initialize($config);

        $this->load->library('parser');
        $data = array(
            'Title' => 'Leave Request',
            'Firstname' => $this->session->userdata('firstname'),
            'Lastname' => $this->session->userdata('lastname'),
            'StartDate' => $this->input->post('startdate'),
            'EndDate' => $this->input->post('enddate'),
            'UrlAccept' => $acceptUrl,
            'UrlReject' => $rejectUrl
        );
        $message = $this->parser->parse('emails/request', $data, TRUE);

        $this->email->from('do.not@reply.me', 'LMS');
        $this->email->to($manager['email']);
        $this->email->subject('[LMS] Leave Request from ' .
                $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname'));
        $this->email->message($message);
        $this->email->send();
        //echo $this->email->print_debugger();
        log_message('debug', '{controllers/leaves/sendMail} Leaving method.');
    }
    
    /**
     * Delete a leave request
     * @param int $id identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) {
        //Test if the leave request exists
        $data['leaves_item'] = $this->leaves_model->get_leaves($id);
        if (empty($data['leaves_item'])) {
            show_404();
        } else {
            $this->leaves_model->delete_leave($id);
        }
        $this->session->set_flashdata('msg', 'The leave request has been succesfully deleted');
        redirect('leaves/index');
    }

    /**
     * Action: export the list of all leaves into an Excel file
     */
    public function export() {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('List of leaves');
        $this->excel->getActiveSheet()->setCellValue('A1', 'ID');
        $this->excel->getActiveSheet()->setCellValue('B1', 'Start Date');
        $this->excel->getActiveSheet()->setCellValue('C1', 'Start Date type');
        $this->excel->getActiveSheet()->setCellValue('D1', 'End Date');
        $this->excel->getActiveSheet()->setCellValue('E1', 'End Date type');
        $this->excel->getActiveSheet()->setCellValue('F1', 'Duration');
        $this->excel->getActiveSheet()->setCellValue('G1', 'Type');
        $this->excel->getActiveSheet()->setCellValue('H1', 'Cause');
        $this->excel->getActiveSheet()->setCellValue('I1', 'Status');
        $this->excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $leaves = $this->leaves_model->get_leaves();
        $this->load->model('status_model');
        $this->load->model('types_model');
        
        $line = 2;
        foreach ($leaves as $leave) {
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $leave['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $leave['startdate']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $leave['startdatetype']);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $leave['enddate']);
            $this->excel->getActiveSheet()->setCellValue('E' . $line, $leave['enddatetype']);
            $this->excel->getActiveSheet()->setCellValue('F' . $line, $leave['duration']);
            $this->excel->getActiveSheet()->setCellValue('G' . $line, $this->types_model->get_label($leave['type']));
            $this->excel->getActiveSheet()->setCellValue('H' . $line, $leave['cause']);
            $this->excel->getActiveSheet()->setCellValue('I' . $line, $this->status_model->get_label($leave['status']));
            $line++;
        }

        $filename = 'leaves.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
    
    /**
     * REST endpoint : Send a list of fullcalendar events
     */
    public function team() {
        header("Content-Type: application/json");
        echo $this->leaves_model->team($this->session->userdata('manager'));
    }

    /**
     * REST endpoint : Send a list of fullcalendar events
     */
    public function individual() {
        header("Content-Type: application/json");
        echo $this->leaves_model->individual($this->session->userdata('id'));
    }

}
