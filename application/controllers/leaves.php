<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
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
    }

    /**
     * Display the list of the leave requests of the connected user
     */
    public function index() {
        $this->auth->check_is_granted('list_leaves');
        $data['leaves'] = $this->leaves_model->get_user_leaves($this->session->userdata('id'));
        $data['title'] = 'My Leave Requests';
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leaves/index', $data);
        $this->load->view('templates/footer');
    }

    public function view($id) {
        $this->auth->check_is_granted('view_leaves');
        $data['leaves_item'] = $this->users_model->get_leaves($id);
        if (empty($data['leaves_item'])) {
            show_404();
        }
        $data['title'] = 'Leave details';
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leaves/view', $data);
        $this->load->view('templates/footer');
    }

    public function create() {
        $this->auth->check_is_granted('create_leaves');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Request a leave';
        $data['fullname'] = $this->fullname;
        $data['is_admin'] = $this->is_admin;

        $this->form_validation->set_rules('startdate', 'Start Date', 'required');
        $this->form_validation->set_rules('startdatetype', 'Start Date type', 'required');
        $this->form_validation->set_rules('enddate', 'End Date', 'required');
        $this->form_validation->set_rules('enddatetype', 'End Date type', 'required');
        $this->form_validation->set_rules('duration', 'Duration', 'required');
        $this->form_validation->set_rules('cause', 'Cause', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/create');
            $this->load->view('templates/footer');
        } else {
            $this->leaves_model->set_leaves();
            $this->load->model('users_model');
            $this->load->model('settings_model');
            $manager = $this->users_model->get_users($this->session->userdata('manager'));
            
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
                'EndDate' => $this->input->post('enddate')
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
            $this->session->set_flashdata('msg', 'The leave request has been succesfully created');
            redirect('leaves/index');
        }
    }
    
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
        $this->excel->getActiveSheet()->setCellValue('G1', 'Cause');
        $this->excel->getActiveSheet()->setCellValue('H1', 'Status');
        $this->excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $leaves = $this->leaves_model->get_leaves();
        $line = 2;
        foreach ($leaves as $leave) {
            $this->excel->getActiveSheet()->setCellValue('A' . $line, $leave['id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $line, $leave['startdate']);
            $this->excel->getActiveSheet()->setCellValue('C' . $line, $leave['startdatetype']);
            $this->excel->getActiveSheet()->setCellValue('D' . $line, $leave['enddate']);
            $this->excel->getActiveSheet()->setCellValue('E' . $line, $leave['enddatetype']);
            $this->excel->getActiveSheet()->setCellValue('F' . $line, $leave['duration']);
            $this->excel->getActiveSheet()->setCellValue('G' . $line, $leave['cause']);
            $this->excel->getActiveSheet()->setCellValue('H' . $line, $leave['status']);
            $line++;
        }

        /*//For debuging purposes
        $filename = 'testFile.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'CSV');
        $objWriter->save('php://output');*/
        $filename = 'leaves.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
    
    	public function team()
	{//TODO: filter start/end
		header("Content-Type: application/json");
		echo $this->leaves_model->team($this->session->userdata('manager'));
	}

	public function individual()
	{//TODO: filter start/end
		header("Content-Type: application/json");
		echo $this->leaves_model->individual($this->session->userdata('id'));
	}
}
