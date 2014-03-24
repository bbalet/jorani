<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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

class Auth {

	private $CI;

    public function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->library('session');
    } 
	
	/**
     * Check if the current user can perform a given action on the system.
	 * Note that other business rules may be implemented in the controllers.
	 * For instance, a user can approve a leave only if it is a manager of the submitter.
	 * Or a user may delete a leave only if the leave is at the planned status
	 * This function only prevents gross security issues when a user try to access 
	 * a restricted screen.
	 * Note that any operation needs the user to be connected.
	 * @param int $id  optional object identifier of the operation (e.g. user id)
	 * @return bool true if the user is granted, false otherwise
     */
    public function is_granted($operation, $object_id = 0)
    {
		//is_admin
		//id
		//operation
		//id of object (e.g. user id)
        switch ($operation) {
			//User management
			case 'list_users' :
			case 'create_user' :
			case 'delete_user' :
			case 'view_user' :
			case 'edit_user' :
			case 'update_user' :
			case 'export_user' :
				if ($this->CI->session->userdata('is_admin') == true)
					return true;
				else
					return false;
				break;	
			
			//Password management
			case 'change_password' :
				if ($this->CI->session->userdata('is_admin') == true)
					return true;
				else //a user can change its own password
					if ($this->CI->session->userdata('id') == $object_id)
						return true;
					else
						return false;
				break;
			
			//Leaves
			case 'list_leaves' :
			case 'create_leaves' :
			case 'export_leaves' :
				return true;
			//Additionnal access logic: cannot view/edit/update the leave of another user except for admin/manager
				
			//Request
			//Access logic is in the controller : if the connected user manages nobody, the list will be empty
			
			//Calendar
			case 'team_calendar' :
			case 'individual_calendar' :
				return true;
			//Additionnal access logic: filter on the connected user
			//Additionnal access logic: filter on the team of the connected user
			
		}
    }
	
	/**
     * Check if the current user can perform a given action on the system.
	 * @use is_granted
	 * @param int $id  optional object identifier of the operation (e.g. user id)
	 * @return bool true if the user is granted, false otherwise
     */
	public function check_is_granted($operation, $object_id = 0)
	{
		if (!$this->is_granted('list_users')) {
			$this->CI->load->helper('url');
			$this->CI->session->set_flashdata('msg', 'Operation (' . $operation . ') is not granted');
			redirect('forbidden');
		}
	}
}
