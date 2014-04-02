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

class Requests extends CI_Controller {

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
        $this->load->model('leaves_model');
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_admin = $this->session->userdata('is_admin');
        $this->is_hr = $this->session->userdata('is_hr');
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
        $data['is_hr'] = $this->is_hr;
        $data['user_id'] =  $this->user_id;
        return $data;
    }

    /**
     * Display the list of all requests submitted to you
     * Status is submitted
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index($filter = 'requested') {
        $this->auth->check_is_granted('list_requests');
        if ($filter == 'all') {
            $showAll = true;
        } else {
            $showAll = false;
        }
        
        $data = $this->getUserContext();
        $data['filter'] = $filter;
        $data['title'] = 'List of requested leaves';
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
        log_message('debug', '{controllers/requests/accept} Entering method with id=' . $id);
        $this->auth->check_is_granted('accept_requests');
        $this->load->model('users_model');
        $leave = $this->leaves_model->get_leaves($id);
        if (empty($leave)) {
            show_404();
        }
        $employee = $this->users_model->get_users($leave['employee']);
        if ($this->user_id != $employee['manager']) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to accept leave #' . $id);
            $this->session->set_flashdata('msg', 'You are not the manager of this employee. You cannot validate this leave request.');
            redirect('home');
        } else {
            $this->leaves_model->accept_leave($id);
            $this->session->set_flashdata('msg', 'The leave request has been successfully accepted.');
            log_message('debug', '{controllers/requests/accept} Leaving method (before redirect)');
            redirect('requests');
        }
        
        /*$data['title'] = 'User';
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leaves/view', $data);
        $this->load->view('templates/footer');*/
    }

    /**
     * Reject a leave request
     * @param int $id leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function reject($id) {
        log_message('debug', '{controllers/requests/reject} Entering method with id=' . $id);
        $this->auth->check_is_granted('reject_requests');
        $this->load->model('users_model');
        $leave = $this->leaves_model->get_leaves($id);
        if (empty($leave)) {
            show_404();
        }
        $employee = $this->users_model->get_users($leave['employee']);
        if ($this->user_id != $employee['manager']) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to reject leave #' . $id);
            $this->session->set_flashdata('msg', 'You are not the manager of this employee. You cannot validate this leave request.');
            redirect('home');
        } else {
            $this->leaves_model->reject_leave($id);
            $this->session->set_flashdata('msg', 'The leave request has been successfully rejected.');
            log_message('debug', '{controllers/requests/reject} Leaving method (before redirect)');
            redirect('requests');
        }
        /*$this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leaves/view', $data);
        $this->load->view('templates/footer');*/
    }
    
    /**
     * Action: export the list of all leave requests into an Excel file
     */
    public function export($filter = 'requested') {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('List of leave resquests');
        $this->excel->getActiveSheet()->setCellValue('A1', 'ID');
        $this->excel->getActiveSheet()->setCellValue('B1', 'Fullname');
        $this->excel->getActiveSheet()->setCellValue('C1', 'Start Date');
        $this->excel->getActiveSheet()->setCellValue('D1', 'Start Date type');
        $this->excel->getActiveSheet()->setCellValue('E1', 'End Date');
        $this->excel->getActiveSheet()->setCellValue('F1', 'End Date type');
        $this->excel->getActiveSheet()->setCellValue('G1', 'Duration');
        $this->excel->getActiveSheet()->setCellValue('H1', 'Type');
        $this->excel->getActiveSheet()->setCellValue('I1', 'Cause');
        $this->excel->getActiveSheet()->setCellValue('J1', 'Status');
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
}
