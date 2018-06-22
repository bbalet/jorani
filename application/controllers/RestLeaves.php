<?php
/**
 * This controller is the entry point for the REST API used by mobile and HTML5
 * Clients. They use CORS requests. Each call to end points uses BasicAuth 
 * except the preflight exchange. So it should be used with a TLS connection
 * @copyright  Copyright (c) 2014-2018 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.3.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class implements a REST API
 */
class RestLeaves extends MY_RestController {

    /**
     * Get the list of leave requests of the connected employee
     * @param int $leaveId Unique identifier of a leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function leaves($leaveId = 0) {
        log_message('debug', '++leaves id=' . $leaveId);
        $this->load->model('leaves_model');
        
        //Format and translate according to Accept-Language Header
        $langRest = new CI_Lang();
        $langRest->load('global', $this->language);

        if ($leaveId == 0) {
            $leaves = $this->leaves_model->getLeavesOfEmployee($this->user->id);
            log_message('debug', 'We have ' . (is_null($leaves)?0:count($leaves)) . ' leave(s)');
            foreach ($leaves as &$leave) {
                $leave['startdatetype'] = $langRest->line($leave['startdatetype']);
                $leave['enddatetype'] = $langRest->line($leave['enddatetype']);
                $leave['status_name'] = $langRest->line($leave['status_name']);
                $date = new DateTime($leave['startdate']);
                $leave['startdate'] = $date->format($langRest->line('global_date_format'));
                $date = new DateTime($leave['enddate']);
                $leave['enddate'] = $date->format($langRest->line('global_date_format'));
            }
        } else {
            $leaves = $this->leaves_model->getLeaves($leaveId);
            if ($leaves['employee'] != $this->user->id) {
                $this->forbidden();
            }
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($leaves));
        log_message('debug', '--leaves');
    }
    
}
