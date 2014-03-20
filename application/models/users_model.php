<?php
class Users_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}
	
	public function get_users($id = 0)
	{
		if ($id === 0)
		{
			$query = $this->db->get('users');
			return $query->result_array();
		}

		$query = $this->db->get_where('users', array('id' => $id));
		return $query->row_array();
	}
	
	public function set_users()
	{
		$data = array(
			'firstname' => $this->input->post('firstname'),
			'lastname' => $this->input->post('lastname')
		);

		return $this->db->insert('users', $data);
	}
}
