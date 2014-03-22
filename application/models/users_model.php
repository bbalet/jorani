<?php
class Users_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		//Load password hasher for create/update functions
		$this->load->library('bcrypt');
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
	
	public function delete_user($id = 0)
	{
		$query = $this->db->delete('users', array('id' => $id));
	}
	
	public function set_users()
	{
		$hash = $this->bcrypt->hash_password($this->input->post('password'));
		$data = array(
			'firstname' => $this->input->post('firstname'),
			'lastname' => $this->input->post('lastname'),
			'password' => $hash
		);
		return $this->db->insert('users', $data);
	}
	
	public function update_users()
	{
		//$hash = $this->bcrypt->hash_password($this->input->post('password'));
		$data = array(
			'firstname' => $this->input->post('firstname'),
			'lastname' => $this->input->post('lastname')
		);

		$this->db->where('id', $this->input->post('id'));
		return $this->db->update('users', $data);
	}
}
