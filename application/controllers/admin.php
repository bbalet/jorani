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

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->lang->load('global', $this->language);
    }
    
    /**
     * Display the settings of the system (extract of config.php)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function settings() {
        $this->auth->check_is_granted('list_settings');
        $data = getUserContext($this);
        $data['title'] = 'application/config/config.php';
        $data['help'] = ''; //create_help_link
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('admin/settings', $data);
        $this->load->view('templates/footer');
    }
}
