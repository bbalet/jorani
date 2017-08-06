<?php
/**
 * This controller serves all the ICS (webcal, ical) feeds exposed by Jorani.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

//VObject is used to build an ICS feed (webcal, ical feed)
require_once FCPATH . "vendor/autoload.php";
use Sabre\VObject;

/**
 * This class builds all the ICS (webcal, ical) feeds exposed by Jorani.
 */
class Ics extends CI_Controller {
    
    /**
     * Default constructor
     * Initializing of Sabre VObjets library
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('polyglot');
    }
    
    /**
     * Get the list of dayoffs for a given contract identifier
     * @param int $user identifier of the user wanting to view the list (mind timezone)
     * @param int $contract identifier of a contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function dayoffs($user, $contract) {
        if ($this->config->item('ics_enabled') == FALSE) {
            $this->output->set_header("HTTP/1.0 403 Forbidden");
        } else {
            //Get timezone and language of the user
            $this->load->model('users_model');
            $employee = $this->users_model->getUsers($user);
            if (!is_null($employee['timezone'])) {
                $tzdef = $employee['timezone'];
            } else {
                $tzdef = $this->config->item('default_timezone');
                if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
            }
            $this->lang->load('global', $this->polyglot->code2language($employee['language']));
            //Load the list of day off associated to the contract
            $this->load->model('dayoffs_model');
            $result = $this->dayoffs_model->getDaysOffForContract($contract);
            if (empty($result)) {
                echo "";
            } else {
                $vcalendar = new VObject\Component\VCalendar();
                foreach ($result as $event) {
                    $startdate = new \DateTime($event->date, new \DateTimeZone($tzdef));
                    $enddate = new \DateTime($event->date, new \DateTimeZone($tzdef));
                    switch ($event->type) {
                        case 1: 
                            $startdate->setTime(0, 0);
                            $enddate->setTime(0, 0);
                            $enddate->modify('+1 day');
                            break;
                        case 2:
                            $startdate->setTime(0, 0);
                            $enddate->setTime(12, 0);
                            break;
                        case 3:
                            $startdate->setTime(12, 0);
                            $enddate->setTime(0, 0);
                            $enddate->modify('+1 day');
                            break;
                    }
                    //In order to support Outlook, we convert start and end dates to UTC
                    $startdate->setTimezone(new DateTimeZone("UTC"));
                    $enddate->setTimezone(new DateTimeZone("UTC"));
                    $vcalendar->add('VEVENT', Array(
                        'SUMMARY' => $event->title,
                        'CATEGORIES' => lang('day off'),
                        'DTSTART' => $startdate,
                        'DTEND' => $enddate
                    ));    
                }
                echo $vcalendar->serialize();
            }
        }
    }
    
    /**
     * Get the list of leaves for a given employee identifier
     * @param int $id identifier of an employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual($id) {
        if ($this->config->item('ics_enabled') == FALSE) {
            $this->output->set_header("HTTP/1.0 403 Forbidden");
        } else {
            $this->load->model('leaves_model');
            $result = $this->leaves_model->getLeavesOfEmployee($id);
            if (empty($result)) {
                echo "";
            } else {
                //Get timezone and language of the user
                $this->load->model('users_model');
                $employee = $this->users_model->getUsers($id);
                if (!is_null($employee['timezone'])) {
                    $tzdef = $employee['timezone'];
                } else {
                    $tzdef = $this->config->item('default_timezone');
                    if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
                }
                $this->lang->load('global', $this->polyglot->code2language($employee['language']));
                
                $vcalendar = new VObject\Component\VCalendar();
                foreach ($result as $event) {
                    if (($event['status'] != LMS_CANCELED) && ($event['status'] != LMS_REJECTED)) {
                        $startdate = new \DateTime($event['startdate'], new \DateTimeZone($tzdef));
                        $enddate = new \DateTime($event['enddate'], new \DateTimeZone($tzdef));
                        if ($event['startdatetype'] == 'Morning') $startdate->setTime(0, 0);
                        if ($event['startdatetype'] == 'Afternoon') $startdate->setTime(12, 0);
                        if ($event['enddatetype'] == 'Morning') $enddate->setTime(12, 0);
                        if ($event['enddatetype'] == 'Afternoon'){
                            $enddate->setTime(0, 0);
                            $enddate->modify('+1 day');
                        } 

                        //In order to support Outlook, we convert start and end dates to UTC
                        $startdate->setTimezone(new DateTimeZone("UTC"));
                        $enddate->setTimezone(new DateTimeZone("UTC"));
                        $vcalendar->add('VEVENT', Array(
                                'SUMMARY' => lang('leave'),
                                'CATEGORIES' => lang('leave'),
                                'DTSTART' => $startdate,
                                'DTEND' => $enddate,
                                'DESCRIPTION' => $event['cause'],
                                'URL' => base_url() . "leaves/" . $event['id'],
                        ));
                    }
                }
                echo $vcalendar->serialize();
            }
        }
    }

    /**
     * Get the list of leaves for a group of employees attached to an entity
     * @param int $user identifier of the user wanting to view the list (mind timezone)
     * @param int $entity identifier of an entity
     * @param bool $children TRUE include sub-entity, FALSE otherwise (default)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function entity($user, $entity, $children) {
        if ($this->config->item('ics_enabled') == FALSE) {
            $this->output->set_header("HTTP/1.0 403 Forbidden");
        } else {
            $this->load->model('leaves_model');
            $children = filter_var($children, FILTER_VALIDATE_BOOLEAN);
            $result = $this->leaves_model->entity($entity, $children);
            if (empty($result)) {
                echo "";
            } else {
                //Get timezone and language of the user
                $this->load->model('users_model');
                $employee = $this->users_model->getUsers($user);
                if (!is_null($employee['timezone'])) {
                    $tzdef = $employee['timezone'];
                } else {
                    $tzdef = $this->config->item('default_timezone');
                    if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
                }
                $this->lang->load('global', $this->polyglot->code2language($employee['language']));
                
                $vcalendar = new VObject\Component\VCalendar();
                foreach ($result as $event) {
                    if (($event['status'] != LMS_CANCELED) && ($event['status'] != LMS_REJECTED)) {
                        $startdate = new \DateTime($event['startdate'], new \DateTimeZone($tzdef));
                        $enddate = new \DateTime($event['enddate'], new \DateTimeZone($tzdef));
                        if ($event['startdatetype'] == 'Morning') $startdate->setTime(0, 1);
                        if ($event['startdatetype'] == 'Afternoon') $startdate->setTime(12, 0);
                        if ($event['enddatetype'] == 'Morning') $enddate->setTime(12, 0);
                        if ($event['enddatetype'] == 'Afternoon') $enddate->setTime(23, 59);

                        //In order to support Outlook, we convert start and end dates to UTC
                        $startdate->setTimezone(new DateTimeZone("UTC"));
                        $enddate->setTimezone(new DateTimeZone("UTC"));
                        $vcalendar->add('VEVENT', Array(
                            'SUMMARY' => $event['firstname'] . ' ' . $event['lastname'],
                            'CATEGORIES' => lang('leave'),
                            'DTSTART' => $startdate,
                            'DTEND' => $enddate,
                            'DESCRIPTION' => $event['type'] . ($event['cause']!=''?(' / ' . $event['cause']):''),
                            'URL' => base_url() . "leaves/" . $event['id'],
                        )); 
                    }
                }
                echo $vcalendar->serialize();
            }
        }
    }
    
    /**
     * Get the list of leaves of the collaborators of the connected user (manager)
     * @param int $user identifier of the user wanting to view the list (mind timezone)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators($user) {
        if ($this->config->item('ics_enabled') == FALSE) {
            $this->output->set_header("HTTP/1.0 403 Forbidden");
        } else {
            $this->load->model('leaves_model');
            $result = $this->leaves_model->getLeavesRequestedToManager($user, TRUE);
            if (empty($result)) {
                echo "";
            } else {
                //Get timezone and language of the user
                $this->load->model('users_model');
                $employee = $this->users_model->getUsers($user);
                if (!is_null($employee['timezone'])) {
                    $tzdef = $employee['timezone'];
                } else {
                    $tzdef = $this->config->item('default_timezone');
                    if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
                }
                $this->lang->load('global', $this->polyglot->code2language($employee['language']));
                
                $vcalendar = new VObject\Component\VCalendar();
                foreach ($result as $event) {
                    if (($event['status'] != LMS_CANCELED) && ($event['status'] != LMS_REJECTED)) {
                        $startdate = new \DateTime($event['startdate'], new \DateTimeZone($tzdef));
                        $enddate = new \DateTime($event['enddate'], new \DateTimeZone($tzdef));
                        if ($event['startdatetype'] == 'Morning') $startdate->setTime(0, 1);
                        if ($event['startdatetype'] == 'Afternoon') $startdate->setTime(12, 0);
                        if ($event['enddatetype'] == 'Morning') $enddate->setTime(12, 0);
                        if ($event['enddatetype'] == 'Afternoon') $enddate->setTime(23, 59);

                        //In order to support Outlook, we convert start and end dates to UTC
                        $startdate->setTimezone(new DateTimeZone("UTC"));
                        $enddate->setTimezone(new DateTimeZone("UTC"));
                        $vcalendar->add('VEVENT', Array(
                            'SUMMARY' => $event['firstname'] . ' ' . $event['lastname'],
                            'CATEGORIES' => lang('leave'),
                            'DTSTART' => $startdate,
                            'DTEND' => $enddate,
                            'DESCRIPTION' => $event['type_label'] . ($event['cause']!=''?(' / ' . $event['cause']):''),
                            'URL' => base_url() . "leaves/" . $event['id'],
                        ));
                    }
                }
                echo $vcalendar->serialize();
            }
        }
    }
    
    /**
     * Action : download an iCal event corresponding to a leave request
     * @param int leave request id
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function ical($id) {
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename=leave.ics');
        $this->load->model('leaves_model');
        $leave = $this->leaves_model->getLeaves($id);
        //Get timezone and language of the user
        $this->load->model('users_model');
        $employee = $this->users_model->getUsers($leave['employee']);
        if (!is_null($employee['timezone'])) {
            $tzdef = $employee['timezone'];
        } else {
            $tzdef = $this->config->item('default_timezone');
            if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
        }
        $this->lang->load('global', $this->polyglot->code2language($employee['language']));
        
        $startdate = new \DateTime($leave['startdate'], new \DateTimeZone($tzdef));
        $enddate = new \DateTime($leave['enddate'], new \DateTimeZone($tzdef));
        //In order to support Outlook, we convert start and end dates to UTC
        $startdate->setTimezone(new DateTimeZone("UTC"));
        $enddate->setTimezone(new DateTimeZone("UTC"));
        
        $vcalendar = new VObject\Component\VCalendar();
        $vcalendar->add('VEVENT', Array(
            'SUMMARY' => lang('leave'),
            'CATEGORIES' => lang('leave'),
            'DESCRIPTION' => $leave['cause'],
            'DTSTART' => $startdate,
            'DTEND' => $enddate,
            'URL' => base_url() . "leaves/" . $id,
        ));
        echo $vcalendar->serialize();
    }
}
