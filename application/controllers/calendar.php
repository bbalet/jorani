<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jorani is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 */

class Calendar extends CI_Controller {
    
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
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
        $this->language = $this->session->userdata('language');
        $this->language_code = $this->session->userdata('language_code');
        $this->load->helper('language');
        $this->lang->load('calendar', $this->language);
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
        $data['language'] = $this->language;
        $data['language_code'] =  $this->language_code;
        return $data;
    }

    /**
     * Display the page of the individual calendar (of the connected user)
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual() {
        $this->auth->check_is_granted('individual_calendar');
        $data = $this->getUserContext();
        $data['title'] = lang('calendar_individual_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_individual');
        $data['googleApi'] = false;
        $data['clientId'] = 'key';
        $data['apiKey'] = 'key';
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/individual', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the page of the team calendar (users having the same manager
     * than the connected user)
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function workmates() {
        $this->auth->check_is_granted('workmates_calendar');
        $data = $this->getUserContext();
        $data['title'] = lang('calendar_workmates_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_workmates');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/workmates', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display the calendar of the employees managed by the connected user
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators() {
        $this->auth->check_is_granted('collaborators_calendar');
        $data = $this->getUserContext();
        $data['title'] = lang('calendar_collaborators_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_collaborators');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/collaborators', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the calendar of the employees working in the same department
     * than the connected user.
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function department() {
        $this->auth->check_is_granted('department_calendar');
        $data = $this->getUserContext();
        $data['title'] = lang('calendar_department_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_department');
        $this->load->model('organization_model');
        $department = $this->organization_model->get_department($this->user_id);
        if (empty($department)) {
            $this->session->set_flashdata('msg', lang('contract_department_msg_error'));
            redirect('leaves');
        } else {
            $data['department'] = $department[0]['name'];
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('calendar/department', $data);
            $this->load->view('templates/footer');
        }
    }
    
    /**
     * Display a global calendar filtered by organization/entity
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function organization() {
        $this->auth->check_is_granted('organization_calendar');
        $data = $this->getUserContext();
        $data['title'] = lang('calendar_organization_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_organization');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/organization', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display a global tabular calendar
     * @param int $id identifier of the entity
     * @param int $month Month number
     * @param int $year Year number
     * @param bool $children If TRUE, includes children entity, FALSE otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function tabular($id=-1, $month=0, $year=0, $children=TRUE) {
        $this->auth->check_is_granted('organization_calendar');
        $data = $this->getUserContext();
        $this->load->model('leaves_model');
        $this->load->model('organization_model');
        $data['tabular'] = $this->leaves_model->tabular($id, $month, $year, $children);
        $data['entity'] = $id;
        $data['month'] = $month;
        $data['year'] = $year;
        $data['children'] = $children;
        $data['department'] = $this->organization_model->get_label($id);
        $data['title'] = lang('calendar_tabular_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_tabular');
        $this->load->view('templates/header', $data);
        $this->load->view('templates/tabular_style', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/tabular', $data);
        $this->load->view('templates/footer');
    }
}
