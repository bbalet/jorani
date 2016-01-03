<?php
/**
 * This controller contains the actions allowing to manage and display the organization tree
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class allows to manage the organization of of users. Users can be attached to a node of a tree.
 * These nodes are called 'entities' and can be 'departments' or 'sub-departments', 'groups', etc.
 * It allows to use filters on a part of your structure, whatever your organization is.
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
     * Ajax endpoint: Returns the list of the employees attached to an entity
     * The difference with employees endpoint is that the information is condensed and that we display the entry date
     * Prints the table content in a JSON format expected by jQuery Datatable
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function employeesDateHired() {
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
            $msg .= '"' . $employee->firstname . " " . $employee->lastname . '",';
            $msg .= '"' . $employee->datehired . '"';
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
            echo json_encode($this->organization_model->attachEmployee($id, $entity));
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
            echo json_encode($this->organization_model->detachEmployee($id));
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
        $entities = $this->organization_model->getAllEntities();
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
            echo json_encode($this->organization_model->getSupervisor($entity));
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
            echo json_encode($this->organization_model->setSupervisor($id, $entity));
        }
    }
}
