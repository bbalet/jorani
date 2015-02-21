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

class Settings extends CI_Controller {
   
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
    }

    /**
     * Display the settings form
     * TODO : to be implemented
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set() {
        $this->auth->check_is_granted('edit_settings');
        $data = getUserContext($this);
        $data['title'] = 'Settings';
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('settings/set', $data);
        $this->load->view('templates/footer');
    }

}
