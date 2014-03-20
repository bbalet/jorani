<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('leaves_model');
		
		/*
		//See: http://www.codeigniter.fr/user_guide/libraries/email.html
		$this->load->library('email');

		$config['protocol'] = 'sendmail';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['charset'] = 'iso-8859-1';
		$config['wordwrap'] = TRUE;

		$this->email->initialize($config);
		
		$this->email->from('your@example.com', 'Your Name');
		$this->email->to('someone@example.com'); 
		$this->email->cc('another@another-example.com'); 
		$this->email->bcc('them@their-example.com'); 

		$this->email->subject('Email Test');
		$this->email->message('Testing the email class.');	

		$this->email->send();

		echo $this->email->print_debugger();*/
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
			$this->load->view('users/success');
		}
	}
}