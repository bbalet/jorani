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
 * along with lms.  If not, see <http://www.gnu.org/licenses/>.
 */

class Auth {

    /**
     * Access to CI framework so as to use other libraries
     * @var type Code Igniter framework
     */
    private $CI;

    /**
     * Default constructor
     */
    public function __construct() {
        $this->CI = & get_instance();
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
     * @param string $operation Operation attempted by the user
     * @param int $id  optional object identifier of the operation (e.g. user id)
     * @return bool true if the user is granted, false otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function is_granted($operation, $object_id = 0) {
        //log_message('debug', '{librairies/auth/is_granted} Entering method with Operation=' . $operation . ' / object_id=' . $object_id);
        switch ($operation) {
            
            //Admin functions
            case 'purge_database' :
                if ($this->CI->session->userdata('is_hr') == true)
                    if ($this->CI->config->item('enable_purge') == true)
                        return true;
                    else
                        return false;
                else
                    return false;
                break;
            
            //User management
            case 'list_users' :
            case 'create_user' :
            case 'delete_user' :
            case 'view_user' :
            case 'edit_user' :
            case 'update_user' :
            case 'import_user' :
            case 'export_user' :
                if ($this->CI->session->userdata('is_hr') == true)
                    return true;
                else
                    return false;
                break;

            //Password management
            case 'change_password' :
                if ($this->CI->session->userdata('is_hr') == true)
                    return true;
                else {//a user can change its own password
                    if ($this->CI->session->userdata('id') == $object_id)
                        return true;
                    else
                        return false;
                }
                break;
                
            //Configuration
            case 'edit_settings' :
                if ($this->CI->session->userdata('is_hr') == true)
                    return true;
                else
                    return false;
                break;

            //Configuration of HR objects
            case 'list_employees' :
            case 'employee_contract' :
            case 'employee_manager' :
            case 'list_contracts' :
            case 'export_contracts' :
            case 'view_contract' :
            case 'create_contract' :
            case 'delete_contract' :
            case 'edit_contract' :
            case 'delete_positions' :
            case 'edit_positions' :
            case 'create_positions' :
            case 'export_positions' :
            case 'list_positions' :
                
            case 'calendar_contract' :
            case 'adddayoff_contract' :
            case 'deletedayoff_contract' :
                if ($this->CI->session->userdata('is_hr') == true)
                    return true;
                else
                    return false;
                break;
            
            case 'native_report_history':
            case 'native_report_balance':
            case 'report_list' :
            case 'report_execute' :
                if ($this->CI->session->userdata('is_hr') == true)
                    return true;
                else
                    return false;
                break;
            
            //HR
            case 'leavetypes_delete' :
            case 'leavetypes_list' :
            case 'leavetypes_export' :
            case 'leavetypes_create' :
            case 'leavetypes_edit' :
            case 'entitleddays_user' :
            case 'entitleddays_user_delete' :
            case 'entitleddays_contract' :
            case 'entitleddays_contract_delete' :
                if ($this->CI->session->userdata('is_hr') == true)
                    return true;
                else
                    return false;
                break;

            case 'organization_index' :
                if ($this->CI->session->userdata('is_hr') == true)
                    return true;
                else
                    return false;
                break;
            
            //General
            case 'view_myprofile' :
            case 'employees_list' :
            case 'organization_select' :
                return true;
                break;
                
            //Leaves
            case 'list_leaves' :
            case 'create_leaves' :
            case 'export_leaves' :
            case 'view_leaves' :
            case 'edit_leaves' :
            case 'counters_leaves' :
                return true;
                break;
            
            //Extra
            case 'list_extra' :
            case 'create_extra' :
            case 'export_extra' :
            case 'view_extra' :
            case 'edit_extra' :
                return true;
                break;
            //Additionnal access logic: cannot view/edit/update the leave of another user except for admin/manager
            //Extra Request
            case 'list_overtime' :
            case 'accept_overtime' :
            case 'reject_overtime' :
                return true;
                break;
            
            //Additionnal access logic: cannot view/edit/update the leave of another user except for admin/manager
            //Request
            case 'list_collaborators' :
            case 'list_requests' :
            case 'accept_requests' :
            case 'reject_requests' :
                return true;
                break;
            //Access logic is in the controller : if the connected user manages nobody, the list will be empty
            //Calendar
            case 'individual_calendar' :
            case 'workmates_calendar' :
            case 'collaborators_calendar' :
            case 'department_calendar' :
            case 'organization_calendar' :
            case 'download_calendar' :
                return true;
                break;
            //Additionnal access logic: filter on the connected user
            //Additionnal access logic: filter on the team of the connected user
            default:
                return false;
                break;
        }
    }

    /**
     * Check if the current user can perform a given action on the system.
     * @use is_granted
     * @param string $operation Operation attempted by the user
     * @param int $id  optional object identifier of the operation (e.g. user id)
     * @return bool true if the user is granted, false otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function check_is_granted($operation, $object_id = 0) {
        if (!$this->is_granted($operation, $object_id)) {
            $this->CI->load->helper('url');
            $this->CI->load->helper('language');
            $this->CI->lang->load('global', $this->CI->session->userdata('language'));
            log_message('error', 'User #' . $this->CI->session->userdata('id') . ' illegally tried to access to ' . $operation);
            $this->CI->session->set_flashdata('msg', sprintf(lang('global_msg_error_forbidden'), $operation));
            redirect('forbidden');
        }
        else {
            //log_message('debug', '{libraries/auth/check_is_granted} User #' . $this->CI->session->userdata('id') . ' granted access to ' . $operation);
        }
    }

}
