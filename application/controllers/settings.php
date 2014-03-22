<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
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

class Settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        //Check if user is connected
        if (!$this->session->userdata('logged_in')) {
            redirect('session/login');
        }
        //$this->load->model('leaves_model');
    }

    public function set() {
        /*$data['leaves'] = $this->leaves_model->get_leaves();*/
        $data['title'] = 'Settings';
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('settings/set', $data);
        $this->load->view('templates/footer');
    }

}
