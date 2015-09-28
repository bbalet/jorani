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

class Positions extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->load->model('positions_model');
        $this->lang->load('positions', $this->language);
    }

    /**
     * Display list of positions
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->checkIfOperationIsAllowed('list_positions');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['positions'] = $this->positions_model->getPositions();
        $data['title'] = lang('positions_index_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_positions_list');
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, TRUE);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('positions/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display a popup showing the list of positions
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function select() {
        $this->auth->checkIfOperationIsAllowed('list_positions');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['positions'] = $this->positions_model->getPositions();
        $this->load->view('positions/select', $data);
    }
    
    /**
     * Display a form that allows adding a position
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        $this->auth->checkIfOperationIsAllowed('create_positions');
        $data = getUserContext($this);
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('positions_create_title');
        
        $this->form_validation->set_rules('name', lang('positions_create_field_name'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('description', lang('positions_create_field_description'), 'xss_clean|strip_tags');
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('positions/create', $data);
            $this->load->view('templates/footer');
        } else {
            $this->positions_model->setPositions($this->input->post('name'), $this->input->post('description'));
            $this->session->set_flashdata('msg', lang('positions_create_flash_msg'));
            redirect('positions');
        }
    }

    /**
     * Display a form that allows to edit a position
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($id) {
        $this->auth->checkIfOperationIsAllowed('edit_positions');
        $data = getUserContext($this);
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('positions_edit_title');
        $data['position'] = $this->positions_model->getPositions($id);
        
        $this->form_validation->set_rules('name', lang('positions_edit_field_name'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('description', lang('positions_edit_field_description'), 'xss_clean|strip_tags');
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('positions/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $this->positions_model->updatePositions($id, $this->input->post('name'), $this->input->post('description'));
            $this->session->set_flashdata('msg', lang('positions_edit_flash_msg'));
            redirect('positions');
        }
    }
    
    /**
     * Delete a position
     * @param int $id position identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) {
        $this->auth->checkIfOperationIsAllowed('delete_positions');
        $this->positions_model->deletePosition($id);
        $this->session->set_flashdata('msg', lang('positions_delete_flash_msg'));
        redirect('positions');
    }

    /**
     * Export the list of all positions into an Excel file
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export() {
        $this->auth->checkIfOperationIsAllowed('export_positions');
        $this->load->library('excel');
        $this->load->view('positions/export');
    }
}
