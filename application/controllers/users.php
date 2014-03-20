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