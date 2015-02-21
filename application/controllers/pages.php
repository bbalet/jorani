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

class Pages extends CI_Controller {
   
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
    }

    /**
     * Display a static web page. We try to find if a filename matches with the
     * views available in views/pages/ folder
     * @param type $page
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function view($page = 'home') {
        $data = getUserContext($this);
        $path = 'pages/' . $this->session->userdata('language_code') . '/' . $page . '.php';
        if (!file_exists('application/views/' . $path)) {
            $path = 'pages/en/' . $page . '.php';
            if (!file_exists('application/views/' . $path)) { //fallback on default language
                show_404();
            }
        }
        $data['title'] = ucfirst($page); // Capitalize the first letter
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view($path, $data);
        $this->load->view('templates/footer', $data);
    }

}
