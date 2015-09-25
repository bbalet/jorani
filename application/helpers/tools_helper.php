<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
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

/**
 * Check if user is connected, redirect to login form otherwise
 * Set the user context by retrieving infos from session
 * @param reference to CI Controller object
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function setUserContext(CI_Controller $controller)
{
        if (!$controller->session->userdata('logged_in')) {
            //Test if the expired session was detected while responding to an Ajax request
            if (filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') {
                $controller->output->set_status_header('401');
            } else {
                $controller->session->set_userdata('last_page', current_url());
                redirect('session/login');
            }
        }
        $controller->fullname = $controller->session->userdata('firstname') . ' ' .
                $controller->session->userdata('lastname');
        $controller->is_manager = $controller->session->userdata('is_manager');
        $controller->is_admin = $controller->session->userdata('is_admin');
        $controller->is_hr = $controller->session->userdata('is_hr');
        $controller->user_id = $controller->session->userdata('id');
        $controller->manager = $controller->session->userdata('manager');
        $controller->language = $controller->session->userdata('language');
        $controller->language_code = $controller->session->userdata('language_code');
}

/**
 * Prepare an array containing information about the current user
 * @param reference to CI Controller object
 * @return array data to be passed to the view
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function getUserContext(CI_Controller $controller)
{
    $data['fullname'] = $controller->fullname;
    $data['is_manager'] = $controller->is_manager;
    $data['is_admin'] = $controller->is_admin;
    $data['is_hr'] = $controller->is_hr;
    $data['user_id'] =  $controller->user_id;
    $data['language'] = $controller->session->userdata('language');
    $data['language_code'] =  $controller->session->userdata('language_code');
    return $data;
}

/**
 * Sanitizes an input (GET/POST) coming from outside a form (eg Ajax request)
 * @param string $value value to be cleansed from characters that prevent Jorani to work
 * @return string value where problematic characters have been removed
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function sanitize($value){
    $value = trim($value);
    $value = str_replace('\\','',$value);
    $value = strtr($value,array_flip(get_html_translation_table(HTML_ENTITIES)));
    $value = strip_tags($value);
    $value = htmlspecialchars($value);
    return $value;
}
