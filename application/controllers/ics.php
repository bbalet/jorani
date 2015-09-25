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

use Sabre\VObject;

class Ics extends CI_Controller {
    
    /**
     * Default constructor
     * Initializing of Sabre VObjets library
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('polyglot');
        require_once(APPPATH . 'third_party/VObjects/vendor/autoload.php');
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
            $employee = $this->users_model->get_users($user);
            if (!is_null($employee['timezone'])) {
                $tzdef = $employee['timezone'];
            } else {
                $tzdef = $this->config->item('default_timezone');
                if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
            }
            $this->lang->load('global', $this->polyglot->code2language($employee['language']));
            //Load the list of day off associated to the contract
            $this->load->model('dayoffs_model');
            $result = $this->dayoffs_model->get_all_dayoffs($contract);
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
                            $enddate->setTime(23, 59);
                            break;
                        case 2:
                            $startdate->setTime(0, 0);
                            $enddate->setTime(12, 0);
                            break;
                        case 3:
                            $startdate->setTime(12, 0);
                            $enddate->setTime(23, 59);
                            break;
                    }                    
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
                $employee = $this->users_model->get_users($id);
                if (!is_null($employee['timezone'])) {
                    $tzdef = $employee['timezone'];
                } else {
                    $tzdef = $this->config->item('default_timezone');
                    if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
                }
                $this->lang->load('global', $this->polyglot->code2language($employee['language']));
                
                $vcalendar = new VObject\Component\VCalendar();
                foreach ($result as $event) {
                    $startdate = new \DateTime($event['startdate'], new \DateTimeZone($tzdef));
                    $enddate = new \DateTime($event['enddate'], new \DateTimeZone($tzdef));
                    if ($event['startdatetype'] == 'Morning') $startdate->setTime(0, 0);
                    if ($event['startdatetype'] == 'Afternoon') $startdate->setTime(12, 0);
                    if ($event['enddatetype'] == 'Morning') $enddate->setTime(12, 0);
                    if ($event['enddatetype'] == 'Afternoon') $enddate->setTime(23, 59);
                    
                    $vcalendar->add('VEVENT', Array(
                        'SUMMARY' => lang('leave'),
                        'CATEGORIES' => lang('leave'),
                        'DTSTART' => $startdate,
                        'DTEND' => $enddate,
                        'DESCRIPTION' => $event['cause'],
                        'URL' => base_url() . "leaves/" . $event['id'],
                    ));    
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
            $result = $this->leaves_model->entity($entity, $children);
            if (empty($result)) {
                echo "";
            } else {
                //Get timezone and language of the user
                $this->load->model('users_model');
                $employee = $this->users_model->get_users($user);
                if (!is_null($employee['timezone'])) {
                    $tzdef = $employee['timezone'];
                } else {
                    $tzdef = $this->config->item('default_timezone');
                    if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
                }
                $this->lang->load('global', $this->polyglot->code2language($employee['language']));
                
                $vcalendar = new VObject\Component\VCalendar();
                foreach ($result as $event) {
                    $startdate = new \DateTime($event['startdate'], new \DateTimeZone($tzdef));
                    $enddate = new \DateTime($event['enddate'], new \DateTimeZone($tzdef));
                    if ($event['startdatetype'] == 'Morning') $startdate->setTime(0, 1);
                    if ($event['startdatetype'] == 'Afternoon') $startdate->setTime(12, 0);
                    if ($event['enddatetype'] == 'Morning') $enddate->setTime(12, 0);
                    if ($event['enddatetype'] == 'Afternoon') $enddate->setTime(23, 59);
                    
                    $vcalendar->add('VEVENT', Array(
                        'SUMMARY' => $event['firstname'] . ' ' . $event['lastname'],
                        'CATEGORIES' => lang('leave'),
                        'DTSTART' => $startdate,
                        'DTEND' => $enddate,
                        'DESCRIPTION' => $event['type'] . ($event['cause']!=''?(' / ' . $event['cause']):''),
                        'URL' => base_url() . "leaves/" . $event['id'],
                    ));    
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
        $employee = $this->users_model->get_users($leave['employee']);
        if (!is_null($employee['timezone'])) {
            $tzdef = $employee['timezone'];
        } else {
            $tzdef = $this->config->item('default_timezone');
            if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
        }
        $this->lang->load('global', $this->polyglot->code2language($employee['language']));
        
        $vcalendar = new VObject\Component\VCalendar();
        $vcalendar->add('VEVENT', Array(
            'SUMMARY' => lang('leave'),
            'CATEGORIES' => lang('leave'),
            'DESCRIPTION' => $leave['cause'],
            'DTSTART' => new \DateTime($leave['startdate'], new \DateTimeZone($tzdef)),
            'DTEND' => new \DateTime($leave['enddate'], new \DateTimeZone($tzdef)),
            'URL' => base_url() . "leaves/" . $id,
        ));
        echo $vcalendar->serialize();
    }
}
