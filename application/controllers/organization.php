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
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

class Organization extends CI_Controller {
    
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
        $this->load->model('organization_model');
        $this->fullname = $this->session->userdata('firstname') . ' ' .
                $this->session->userdata('lastname');
        $this->is_admin = $this->session->userdata('is_admin');
        $this->is_hr = $this->session->userdata('is_hr');
        $this->user_id = $this->session->userdata('id');
        $this->language = $this->session->userdata('language');
        $this->language_code = $this->session->userdata('language_code');
        $this->load->helper('language');
        $this->lang->load('organization', $this->language);
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
     * Main view that allows to describe the entities of the organization
     * And to attach employees to entities (lot of Ajax callbacks)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->check_is_granted('organization_index');
        $data = $this->getUserContext();
        
        $data['title'] = lang('organization_index_title');
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
        $this->auth->check_is_granted('organization_select');
        $data = $this->getUserContext();
        $this->load->view('organization/select', $data);
    }
    
    /**
     * Rename an entity of the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function rename() {
        header("Content-Type: application/json");
        $id = $this->input->get('id', TRUE);
        $text = $this->input->get('text', TRUE);
        $this->organization_model->rename($id, $text);
    }
    
    /**
     * Create an entity in the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        header("Content-Type: application/json");
        $id = $this->input->get('id', TRUE);
        $text = $this->input->get('text', TRUE);
        $this->organization_model->create($id, $text);
    }
    
    /**
     * Move an entity into the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function move() {
        header("Content-Type: application/json");
        $id = $this->input->get('id', TRUE);
        $parent = $this->input->get('parent', TRUE);
        $this->organization_model->move($id, $parent);
    }
    
    /**
     * Copy an entity into the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function copy() {
        header("Content-Type: application/json");
        $id = $this->input->get('id', TRUE);
        $parent = $this->input->get('parent', TRUE);
        $this->organization_model->copy($id, $parent);
    }

    /**
     * Returns the list of the employees attached to an entity
     * Prints the table content in a JSON format expected by jQuery Datatable
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function employees() {
        header("Content-Type: application/json");
        $id = $this->input->get('id', TRUE);
        $employees = $this->organization_model->employees($id);
        $msg = '{"iTotalRecords":' . count($employees);
        $msg .= ',"iTotalDisplayRecords":' . count($employees);
        $msg .= ',"aaData":[';
        foreach ($employees->result() as $employee) {
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
     * Add an employee to an entity of the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function addemployee() {
        header("Content-Type: application/json");
        $id = $this->input->get('user', TRUE);
        $entity = $this->input->get('entity', TRUE);
        echo json_encode($this->organization_model->add_employee($id, $entity));
    }   
    
    /**
     * Add an employee to an entity of the organization
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delemployee() {
        header("Content-Type: application/json");
        $id = $this->input->get('user', TRUE);
        echo json_encode($this->organization_model->delete_employee($id));
    } 
    
    /**
     * Cascade delete children and set employees' org to NULL
     * takes parameters by GET
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete() {
        header("Content-Type: application/json");
        $entity = $this->input->get('entity', TRUE);
        echo json_encode($this->organization_model->delete($entity));
    }
    
    /**
     * Returns a JSON string describing the organization structure.
     * In a format expected by jsTree component.
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function root() {
        header("Content-Type: application/json");
        $id = $this->input->get('id', TRUE);
        if ($id == "#") {
            unset($id);
        }
        $this->auth->check_is_granted('organization_select');
        $data = $this->getUserContext();
        header("Content-Type: application/json");
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
}
