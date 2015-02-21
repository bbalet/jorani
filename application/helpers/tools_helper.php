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
 */

/**
 * Check if user is connected, redirect to login form otherwise
 * Set the user context by retrieving infos from session
 * @param reference to CI Controller object
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function setUserContext($controller)
{
        if (!$controller->session->userdata('logged_in')) {
            $controller->session->set_userdata('last_page', current_url());
            redirect('session/login');
        }
        $controller->fullname = $controller->session->userdata('firstname') . ' ' .
                $controller->session->userdata('lastname');
        $controller->is_manager = $controller->session->userdata('is_manager');
        $controller->is_admin = $controller->session->userdata('is_admin');
        $controller->is_hr = $controller->session->userdata('is_hr');
        $controller->user_id = $controller->session->userdata('id');
        $controller->language = $controller->session->userdata('language');
        $controller->language_code = $controller->session->userdata('language_code');
}

/**
 * Prepare an array containing information about the current user
 * @param reference to CI Controller object
 * @return array data to be passed to the view
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function getUserContext($controller)
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
 * Internal utility function
 * make sure a resource is reloaded every time
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function expires_now() {
    // Date in the past
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    // always modified
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    // HTTP/1.1
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    // HTTP/1.0
    header("Pragma: no-cache");
}
