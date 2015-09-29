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
     * @param string $page Name of the view (and of the corresponding PHP file)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function view($page = 'home') {
        $data = getUserContext($this);
        $trans = array("-" => " ", "_" => " ", "." => " ");
        $data['title'] = ucfirst(strtr($page, $trans)); // Capitalize the first letter
        //The page containing export in their name are returning another MIMETYPE
        if (strpos($page,'export') === FALSE) {//Don't include header and menu
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
        }
        $view = 'pages/' . $this->language_code .'/' . $page . '.php';
        $pathCI = APPPATH . 'views/';
        $pathLocal = FCPATH .'local/';
        //Check if we have a user-defined view
        if (file_exists($pathLocal . $view)) {
            $this->load->ext_view($pathLocal, $view, $data);
        } else {//Load the page from the default location (CI views folder)
            if (!file_exists($pathCI . $view)) {
                    show_404();
            }
            $this->load->view($view, $data);
        }
        if (strpos($page,'export') === FALSE) {
            $this->load->view('templates/footer', $data);
        }
    }

}
