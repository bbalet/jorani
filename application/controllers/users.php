<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('users_model');
	}

	public function index()
	{
		$data['users'] = $this->users_model->get_users();
		$data['title'] = 'Users';
		$this->load->view('templates/header', $data);
		$this->load->view('users/index', $data);
		$this->load->view('templates/footer');
	}

	public function view($id)
	{
		$data['users_item'] = $this->users_model->get_users($id);
		if (empty($data['users_item']))
		{
			show_404();
		}
		$data['title'] = 'User';
		$this->load->view('templates/header', $data);
		$this->load->view('users/view', $data);
		$this->load->view('templates/footer');
	}
	
	public function edit($id)
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Create a new user';

		$this->form_validation->set_rules('firstname', 'Firstname', 'required');
		$this->form_validation->set_rules('lastname', 'Lastname', 'required');
		
		$data['users_item'] = $this->users_model->get_users($id);
		if (empty($data['users_item']))
		{
			show_404();
		}
		$data['title'] = 'User';
		$this->load->view('templates/header', $data);
		$this->load->view('users/edit', $data);
		$this->load->view('templates/footer');
	}
	
	public function delete($id)
	{
		//Test if user exists
		$data['users_item'] = $this->users_model->get_users($id);
		if (empty($data['users_item']))
		{
			show_404();
		}
		else
		{
			$this->users_model->delete_user($id);
		}
		$this->index();
	}
	
	public function create()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Create a new user';

		$this->form_validation->set_rules('firstname', 'Firstname', 'required');
		$this->form_validation->set_rules('lastname', 'Lastname', 'required');

		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('templates/header', $data);
			$this->load->view('users/create');
			$this->load->view('templates/footer');
		}
		else
		{
			$this->users_model->set_users();
			$this->index();
		}
	}
	
	public function update()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Create a new user';

		$this->form_validation->set_rules('firstname', 'Firstname', 'required');
		$this->form_validation->set_rules('lastname', 'Lastname', 'required');

		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('templates/header', $data);
			$this->load->view('users/edit/' . $this->input->post('id'));
			$this->load->view('templates/footer');
		}
		else
		{
			$this->users_model->update_users();
			$this->index();
		}
	}
	
	public function export()
	{
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('List of users');
		$this->excel->getActiveSheet()->setCellValue('A1', 'ID');
		$this->excel->getActiveSheet()->setCellValue('B1', 'Firstname');
		$this->excel->getActiveSheet()->setCellValue('C1', 'Lastname');
		$this->excel->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle('A1:C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$users = $this->users_model->get_users();
		$line = 2;
		foreach ($users as $user)
		{
			$this->excel->getActiveSheet()->setCellValue('A' . $line, $user['id']);
			$this->excel->getActiveSheet()->setCellValue('B' . $line, $user['firstname']);
			$this->excel->getActiveSheet()->setCellValue('C' . $line, $user['lastname']);
			$line++;
		}
		 
		$filename='users.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		$objWriter->save('php://output');
	}	
	
}