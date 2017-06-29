<?php
/**
 * This helper contains a list of functions used throughout the application
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * Check if user is connected, redirect to login form otherwise
 * Set the user context by retrieving infos from session
 * @param CI_Controller $controller reference to CI Controller object
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function setUserContext(CI_Controller $controller) {
    //Memorize the last displayed page except for Ajax queries
    if (filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') !== 'XMLHttpRequest') {
        $controller->session->set_userdata('last_page', current_url());
        $controller->session->set_userdata('last_page_params', $_SERVER['QUERY_STRING']);
    }
    if (!$controller->session->userdata('logged_in')) {
        //Test if the expired session was detected while responding to an Ajax request
        if (filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') {
            $controller->output->set_status_header('401');
        } else {
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
 * @param CI_Controller $controller reference to CI Controller object
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
    if ($controller->is_manager === TRUE) {
        $controller->load->model('leaves_model');
        $data['requested_leaves_count'] = $controller->leaves_model->countLeavesRequestedToManager($controller->user_id);
        if ($controller->config->item('disable_overtime') == FALSE) {
            $controller->load->model('overtime_model');
            $data['requested_extra_count'] = $controller->overtime_model->countExtraRequestedToManager($controller->user_id);
        } else {
            $data['requested_extra_count'] = 0;
        }
        $data['requests_count'] = $data['requested_leaves_count'] + $data['requested_extra_count'];
    }
    return $data;
}

/**
 * Check if user is connected, redirect to login form otherwise
 * Set the user context by retrieving infos from session
 * and load these data into the array passed to the view
 * @see setUserContext and getUserContext
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function getCIUserContext() {
    $controller = & get_instance();
    setUserContext($controller);
    return getUserContext($controller);
}

/**
 * Sanitizes an input (GET/POST) coming from outside a form (eg Ajax request)
 * @param string $value value to be cleansed from characters that prevent Jorani to work
 * @return string value where problematic characters have been removed
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function sanitize($value)
{
    $value = trim($value);
    $value = str_replace('\\','',$value);
    $value = strtr($value,array_flip(get_html_translation_table(HTML_ENTITIES)));
    $value = strip_tags($value);
    $value = htmlspecialchars($value);
    return $value;
}

/**
 * Wrapper between the controller and the e-mail library
 * @param CI_Controller $controller reference to CI Controller object
 * @param string $subject Subject of the e-mail
 * @param string $message Message of the e-mail
 * @param string $to Recipient of the e-mail
 * @param string $cc (optional) Copied to recipients
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function sendMailByWrapper(CI_Controller $controller, $subject, $message, $to, $cc = NULL)
{
    $controller->load->library('email');
    if ($controller->config->item('subject_prefix') !== NULL) {
        $controller->email->subject($controller->config->item('subject_prefix') . ' ' . $subject);
    } else {
       $controller->email->subject('[Jorani] ' . $subject);
    }
    $controller->email->set_encoding('quoted-printable');
    if (($controller->config->item('from_mail') !== NULL) && ($controller->config->item('from_name') !== NULL)) {
        $controller->email->from($controller->config->item('from_mail'), $controller->config->item('from_name'));
    } else {
       $controller->email->from('do.not@reply.me', 'LMS');
    }
    $controller->email->to($to);
    if (!is_null($cc)) {
        $controller->email->cc($cc);
    }
    $controller->email->message($message);
    $controller->email->send();
}

/**
 * Finalize the export to a spreadsheet. Called from an export view.
 * @param $context reference to CI Controller/View object
 * @param string $filename filename of the spreadsheet (xlsx, ods)
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function exportSpreadsheet($context, $filename)
{
    $format = 'xlsx';
    $objWriter = NULL;
    if (in_array($context->config->item('spreadsheet_format'), array('ods', 'xlsx'))) {
        $format = $context->config->item('spreadsheet_format');
    }
    $filename .= '.' . $format;
    $context->output->set_header('Cache-Control: max-age=0');
    $context->output->set_header('Content-Disposition: attachment;filename="' . $filename . '"');
    switch ($format) {
        case 'ods':
            $context->output->set_header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
            $objWriter = PHPExcel_IOFactory::createWriter($context->excel, 'OpenDocument');
            break;
        case 'xlsx':
            $context->output->set_header('Content-Type: application/vnd.ms-excel');
            $objWriter = PHPExcel_IOFactory::createWriter($context->excel, 'Excel2007');
            break;
    }
    $objWriter->save('php://output');
}

//Function cal_days_in_month might not exist with HHVM and FreeBSD without proper config
if (!function_exists('cal_days_in_month'))
{
    /**
     * Alternative implementation of cal_days_in_month function
     * @param int $calendar calendar number
     * @param int $month month number
     * @param int $year year number
     * @return int number of days in the month or 0 if error
     */
    function cal_days_in_month($calendar, $month, $year) {
        if (checkdate($month, 31, $year))
            return 31;
        if (checkdate($month, 30, $year))
            return 30;
        if (checkdate($month, 29, $year))
            return 29;
        if (checkdate($month, 28, $year))
            return 28;
        return 0; // error 
    }
}

if (!defined('CAL_GREGORIAN')) 
    define('CAL_GREGORIAN', 1); 
