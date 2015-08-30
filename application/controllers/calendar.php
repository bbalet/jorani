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
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */

class Calendar extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        //This controller differs from the others, because some calendars can be public
    }

    /**
     * Display the page of the individual calendar (of the connected user)
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual() {
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->check_is_granted('individual_calendar');
        $data = getUserContext($this);
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
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->check_is_granted('workmates_calendar');
        $data = getUserContext($this);
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
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->check_is_granted('collaborators_calendar');
        $data = getUserContext($this);
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
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->check_is_granted('department_calendar');
        $data = getUserContext($this);
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
        if (($this->config->item('public_calendar') == TRUE) && (!$this->session->userdata('logged_in'))) {
            $this->load->library('polyglot');;
            $data['language'] = $this->config->item('language');
            $data['language_code'] =  $this->polyglot->language2code($data['language']);
            $data['title'] = lang('calendar_organization_title');
            $data['help'] = '';
            $data['logged_in'] = FALSE;
            $this->lang->load('calendar', $data['language']);
            $this->load->view('templates/header', $data);
            $this->load->view('calendar/organization', $data);
            $this->load->view('templates/footer_simple');
        } else {
            setUserContext($this);
            $this->lang->load('calendar', $this->language);
            $this->auth->check_is_granted('organization_calendar');
            $data = getUserContext($this);
            $data['logged_in'] = TRUE;
            $data['title'] = lang('calendar_organization_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_organization');
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('calendar/organization', $data);
            $this->load->view('templates/footer');
        }
    }
    
    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * This code is duplicated from controller/leaves for public access
     * @param int $entity_id Entity identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function public_organization($entity_id) {
        expires_now();
        header("Content-Type: application/json");
        if ($this->config->item('public_calendar') == TRUE) {
            $this->load->model('leaves_model');
            $start = $this->input->get('start', TRUE);
            $end = $this->input->get('end', TRUE);
            $children = filter_var($this->input->get('children', TRUE), FILTER_VALIDATE_BOOLEAN);
            echo $this->leaves_model->department($entity_id, $start, $end, $children);
        } else {
            echo 'Forbidden';
        }
    }
    
    /**
     * Ajax endpoint : Send a list of fullcalendar events: List of all possible day offs
     * This code is duplicated from controller/contract for public access
     * @param int $entity_id Entity identifier
     */
    public function public_dayoffs() {
        expires_now();
        header("Content-Type: application/json");
        if ($this->config->item('public_calendar') == TRUE) {
            $start = $this->input->get('start', TRUE);
            $end = $this->input->get('end', TRUE);
            $entity = $this->input->get('entity', TRUE);
            $children = filter_var($this->input->get('children', TRUE), FILTER_VALIDATE_BOOLEAN);
            $this->load->model('dayoffs_model');
            echo $this->dayoffs_model->allDayoffs($start, $end, $entity, $children);
        } else {
            echo 'Forbidden';
        }
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
        if (($this->config->item('public_calendar') == TRUE) && (!$this->session->userdata('logged_in'))) {
            $this->load->library('polyglot');;
            $data['language'] = $this->config->item('language');
            $data['language_code'] =  $this->polyglot->language2code($data['language']);
            $this->load->model('leaves_model');
            $this->load->model('organization_model');
            $data['tabular'] = $this->leaves_model->tabular($id, $month, $year, $children);
            $data['entity'] = $id;
            $data['month'] = $month;
            $data['year'] = $year;
            $data['children'] = $children;
            $data['department'] = $this->organization_model->get_label($id);
            $data['title'] = lang('calendar_tabular_title');
            $data['help'] = '';
            $this->load->view('templates/header', $data);
            $this->load->view('calendar/tabular', $data);
            $this->load->view('templates/footer_simple');
        } else {
            setUserContext($this);
            $this->lang->load('calendar', $this->language);
            $this->auth->check_is_granted('organization_calendar');
            $data = getUserContext($this);
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
            $this->load->view('menu/index', $data);
            $this->load->view('calendar/tabular', $data);
            $this->load->view('templates/footer');
        }
    }
}
