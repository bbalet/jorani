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

class Organization extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        //This controller differs from the others, because some endpoints can be public
        //when they are used by public calendars
    }

    /**
     * Main view that allows to describe the entities of the organization
     * And to attach employees to entities (lot of Ajax callbacks)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        setUserContext($this);
        $this->auth->checkIfOperationIsAllowed('organization_index');
        $data = getUserContext($this);
        $this->lang->load('organization', $this->language);
        $this->lang->load('datatable', $this->language);
        $this->lang->load('treeview', $this->language);
        $data['title'] = lang('organization_index_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_hr_organization');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('organization/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Pop-up showing the tree of the organization and allowing a
     * user to choose an entity (filter of a report or a calendar)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function select() {
        if (($this->config->item('public_calendar') == TRUE) && (!$this->session->userdata('logged_in'))) {
            $this->load->library('polyglot');
            $data['language'] = $this->config->item('language');
            $data['language_code'] = $this->polyglot->language2code($data['language']);
            $this->lang->load('organization', $data['language']);
            $this->lang->load('treeview', $data['language']);
            $data['help'] = '';
            $data['logged_in'] = FALSE;
            $this->load->view('organization/select', $data);
        } else {
            setUserContext($this);
            $this->auth->checkIfOperationIsAllowed('organization_select');
            $data = getUserContext($this);
            $this->lang->load('organization', $this->language);
            $this->lang->load('treeview', $this->language);
            $this->load->view('organization/select', $data);
        }
    }

    /**
     * Ajax endpoint: Rename an entity of the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function rename() {
        header("Content-Type: application/json");
        setUserContext($this);
        if ($this->auth->isAllowed('edit_organization') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $id = $this->input->get('id', TRUE);
            $text = sanitize($this->input->get('text', TRUE));
            $this->load->model('organization_model');
            $this->organization_model->rename($id, $text);
        }
    }
    
    /**
     * Ajax endpoint: Create an entity in the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        header("Content-Type: application/json");
        setUserContext($this);
        if ($this->auth->isAllowed('edit_organization') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $id = $this->input->get('id', TRUE);
            $text = sanitize($this->input->get('text', TRUE));
            $this->load->model('organization_model');
            $this->organization_model->create($id, $text);
        }
    }
    
    /**
     * Ajax endpoint: Move an entity into the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function move() {
        header("Content-Type: application/json");
        setUserContext($this);
        if ($this->auth->isAllowed('edit_organization') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $id = $this->input->get('id', TRUE);
            $parent = $this->input->get('parent', TRUE);
            $this->load->model('organization_model');
            $this->organization_model->move($id, $parent);
        }
    }
    
    /**
     * Ajax endpoint: Copy an entity into the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function copy() {
        header("Content-Type: application/json");
        setUserContext($this);
        if ($this->auth->isAllowed('edit_organization') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $id = $this->input->get('id', TRUE);
            $parent = $this->input->get('parent', TRUE);
            $this->load->model('organization_model');
            $this->organization_model->copy($id, $parent);
        }
    }

    /**
     * Ajax endpoint: Returns the list of the employees attached to an entity
     * Prints the table content in a JSON format expected by jQuery Datatable
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function employees() {
        header("Content-Type: application/json");
        setUserContext($this);
        $id = $this->input->get('id', TRUE);
        $this->load->model('organization_model');
        $employees = $this->organization_model->employees($id)->result();
        $msg = '{"iTotalRecords":' . count($employees);
        $msg .= ',"iTotalDisplayRecords":' . count($employees);
        $msg .= ',"aaData":[';
        foreach ($employees as $employee) {
            $msg .= '["' . $employee->id . '",';
            $msg .= '"' . $employee->firstname . '",';
            $msg .= '"' . $employee->lastname . '",';
            $msg .= '"' . $employee->email . '"';
            $msg .= '],';
        }
        $msg = rtrim($msg, ",");
        $msg .= ']}';
        echo $msg;
    }
    
    /**
     * Ajax endpoint: Add an employee to an entity of the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function addemployee() {
        header("Content-Type: application/json");
        setUserContext($this);
        if ($this->auth->isAllowed('edit_organization') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $id = $this->input->get('user', TRUE);
            $entity = $this->input->get('entity', TRUE);
            $this->load->model('organization_model');
            echo json_encode($this->organization_model->add_employee($id, $entity));
        }
    }   
    
    /**
     * Ajax endpoint: Add an employee to an entity of the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delemployee() {
        header("Content-Type: application/json");
        setUserContext($this);
        if ($this->auth->isAllowed('edit_organization') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $id = $this->input->get('user', TRUE);
            $this->load->model('organization_model');
            echo json_encode($this->organization_model->delete_employee($id));
        }
    } 
    
    /**
     * Ajax endpoint: Cascade delete children and set employees' org to NULL
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete() {
        header("Content-Type: application/json");
        setUserContext($this);
        if ($this->auth->isAllowed('edit_organization') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $entity = $this->input->get('entity', TRUE);
            $this->load->model('organization_model');
            echo json_encode($this->organization_model->delete($entity));
        }
    }
    
    /**
     * Ajax endpoint: Returns a JSON string describing the organization structure.
     * In a format expected by jsTree component.
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function root() {
        header("Content-Type: application/json");
        if (($this->config->item('public_calendar') == TRUE) && (!$this->session->userdata('logged_in'))) {
            //nop
        } else {
            setUserContext($this);
            $this->auth->checkIfOperationIsAllowed('organization_select');
        }
        
        $id = $this->input->get('id', TRUE);
        if ($id == "#") {
            unset($id);
        }
        $this->load->model('organization_model');
        $entities = $this->organization_model->get_all_entities();
        $msg = '[';
        foreach ($entities->result() as $entity) {
            $msg .= '{"id":"' . $entity->id . '",';
            if ($entity->parent_id == -1) {
                $msg .= '"parent":"#",';
            } else {
                $msg .= '"parent":"' . $entity->parent_id . '",';
            }
            $msg .= '"text":"' . $entity->name . '"';
            $msg .= '},';
        }
        $msg = rtrim($msg, ",");
        $msg .= ']';
        echo $msg;
    }
    
    /**
     * Ajax endpoint:Returns the supervisor of an entity of the organization 
     * (string containing an id)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getsupervisor() {
        header("Content-Type: application/json");
        setUserContext($this);
        $entity = $this->input->get('entity', TRUE);
        if (isset($entity)) {
            $this->load->model('organization_model');
            echo json_encode($this->organization_model->get_supervisor($entity));
        } else {
            $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
        }
    }

    /**
     * Ajax endpoint: Select the supervisor of an entity of the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function setsupervisor() {
        header("Content-Type: application/json");
        setUserContext($this);
        if ($this->auth->isAllowed('edit_organization') == FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            if ($this->input->get('user', TRUE) == "") {
                $id = NULL;
            } else {
                $id = $this->input->get('user', TRUE);
            }
            $entity = $this->input->get('entity', TRUE);
            $this->load->model('organization_model');
            echo json_encode($this->organization_model->set_supervisor($id, $entity));
        }
    }
}
