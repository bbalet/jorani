<?php
/**
 * This controller serves all the ICS (webcal, ical) feeds exposed by Jorani.
 * @copyright  Copyright (c) 2014-2019 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

//VObject is used to build an ICS feed (webcal, ical feed)
use Sabre\VObject;

/**
 * This class builds all the ICS (webcal, ical) feeds exposed by Jorani.
 */
class Ics extends CI_Controller {

    /**
     * String representing the timezone of an employee or
     * a default timezone if it is not set for the user.
     * @var string
     */
    private $timezone;

    /**
     * Default constructor
     * Initializing of Sabre VObjets library
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();

        $this->load->model('users_model');
        $methodName = $this->router->fetch_method();
        if (!in_array($methodName, array("ical"))) {
            if (!$this->checkIfAccessIsGranted()) {
                $this->output->set_header("HTTP/1.0 403 Forbidden")->_display();
                die();
            }
        }
        $this->load->library('polyglot');
    }

    /**
     * If legacy feeds are disabled, we must check if the feed is queried
     * for/by an existing user in the database. It is a pseudo authentication
     * That prevents illegal access from outside
     *
     * @return bool TRUE if the user is authenticated, FALSE otherwise
     */
    private function checkIfAccessIsGranted() {
        if ($this->config->item('ics_enabled') === FALSE) {
            return FALSE;
        }
        $this->load->model('users_model');
        if ($this->config->item('legacy_feeds') === FALSE) {
            if ($this->input->get('token')) {
                if (!$this->users_model->checkUserByHash($this->input->get('token', TRUE))) {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }
        return TRUE;
    }


    /**
     * Returns a VTIMEZONE component
     * with daylight transitions covering the given date range.
     *
     * Copied from: https://gist.github.com/thomascube/47ff7d530244c669825736b10877a200
     *
     * @param string Timezone ID as used in PHP's Date functions
     * @param integer Unix timestamp with first date/time in this timezone
     * @param integer Unix timestap with last date/time in this timezone
     *
     * @return mixed A Sabre\VObject\Component object representing a VTIMEZONE definition
     *               or false if no timezone information is available
     */
    function add_vtimezone($vcalendar, $tzid, $from = 0, $to = 0)
    {
        if (!$from) $from = time();
        if (!$to)   $to = $from;

        try {
            $tz = new \DateTimeZone($tzid);
        }
        catch (\Exception $e) {
            return false;
        }

        // get all transitions for one year back/ahead
        $year = 86400 * 360;
        $transitions = $tz->getTransitions($from - $year, $to + $year);

        $vt = $vcalendar->createComponent('VTIMEZONE');
        $vt->TZID = $tz->getName();

        $std = null; $dst = null;
        foreach ($transitions as $i => $trans) {
            $cmp = null;

            // skip the first entry...
            if ($i == 0) {
                // ... but remember the offset for the next TZOFFSETFROM value
                $tzfrom = $trans['offset'] / 3600;
                continue;
            }

            // daylight saving time definition
            if ($trans['isdst']) {
                $t_dst = $trans['ts'];
                $dst = $vcalendar->createComponent('DAYLIGHT');
                $cmp = $dst;
            }
            // standard time definition
            else {
                $t_std = $trans['ts'];
                $std = $vcalendar->createComponent('STANDARD');
                $cmp = $std;
            }

            if ($cmp) {
                $dt = new DateTime($trans['time']);
                $offset = $trans['offset'] / 3600;

                $cmp->DTSTART = $dt->format('Ymd\THis');
                $cmp->TZOFFSETFROM = sprintf('%s%02d%02d', $tzfrom >= 0 ? '+' : '-', abs(floor($tzfrom)), ($tzfrom - floor($tzfrom)) * 60);
                $cmp->TZOFFSETTO   = sprintf('%s%02d%02d', $offset >= 0 ? '+' : '-', abs(floor($offset)), ($offset - floor($offset)) * 60);

                // add abbreviated timezone name if available
                if (!empty($trans['abbr'])) {
                    $cmp->TZNAME = $trans['abbr'];
                }

                $tzfrom = $offset;
                $vt->add($cmp);
            }

            // we covered the entire date range
            if ($std && $dst && min($t_std, $t_dst) < $from && max($t_std, $t_dst) > $to) {
                break;
            }
        }

        // add X-MICROSOFT-CDO-TZID if available
        $microsoftExchangeMap = array_flip(VObject\TimeZoneUtil::$microsoftExchangeMap);
        if (array_key_exists($tz->getName(), $microsoftExchangeMap)) {
            $vt->add('X-MICROSOFT-CDO-TZID', $microsoftExchangeMap[$tz->getName()]);
        }

        return $vcalendar->add($vt);
    }

    private function isFullDayEntry($leave) {
        if ($leave['startdatetype'] != 'Morning')
            return FALSE;
        if ($leave['enddatetype'] != 'Afternoon')
            return FALSE;

        return TRUE;
    }

    private function createVEvent($leave) {
        $vcalendar = new VObject\Component\VCalendar();
        $vevent = $vcalendar->createComponent('VEVENT');

        $vevent->CATEGORIES = lang('leave');
        $vevent->URL = base_url() . "leaves/" . $leave['id'];

        $startdate = new \DateTime($leave['startdate'], new \DateTimeZone($this->timezone));
        $enddate = new \DateTime($leave['enddate'], new \DateTimeZone($this->timezone));

        if ($leave['startdatetype'] == 'Morning') $startdate->setTime(0, 0);
        if ($leave['startdatetype'] == 'Afternoon') $startdate->setTime(12, 0);
        if ($leave['enddatetype'] == 'Morning') $enddate->setTime(12, 0);
        if ($leave['enddatetype'] == 'Afternoon'){
            $enddate->setTime(0, 0);
            $enddate->modify('+1 day');
        }

        $vevent->DTSTART = $startdate;
        $vevent->DTEND = $enddate;

        if ($this->isFullDayEntry($leave)) {
            $vevent->DTSTART['VALUE'] = 'DATE';
            $vevent->DTEND['VALUE'] = 'DATE';
        }

        return $vevent;
    }

    /**
     * Get timezone and language of the user
     *
     * @param int $userId Identifier of an employee
     * @return void
     */
    private function getTimezoneAndLanguageOfUser($userId) {
        $employee = $this->users_model->getUsers($userId);
        if (!is_null($employee['timezone'])) {
            $this->timezone = $employee['timezone'];
        } else {
            $this->timezone = $this->config->item('default_timezone');
            if ($this->timezone === FALSE) $this->timezone = 'Europe/Paris';
        }
        $this->lang->load('global', $this->polyglot->code2language($employee['language']));
    }

    /**
     * Get the list of dayoffs for a given contract identifier
     * @param int $userId identifier of the user wanting to view the list (mind timezone)
     * @param int $contract identifier of a contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function dayoffs($userId, $contract) {
        //Get timezone and language of the user
        $this->getTimezoneAndLanguageOfUser($userId);
        //Load the list of day off associated to the contract
        $this->load->model('dayoffs_model');
        $result = $this->dayoffs_model->getDaysOffForContract($contract);
        if (empty($result)) {
            echo "";
        } else {
            $vcalendar = new VObject\Component\VCalendar();
            foreach ($result as $leave) {
                $startdate = new \DateTime($leave->date, new \DateTimeZone($this->timezone));
                $enddate = new \DateTime($leave->date, new \DateTimeZone($this->timezone));
                switch ($leave->type) {
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
                    'SUMMARY' => $leave->title,
                    'CATEGORIES' => lang('day off'),
                    'DTSTART' => $startdate,
                    'DTEND' => $enddate
                ));
            }
            echo $vcalendar->serialize();
        }
    }

    /**
     * Get the list of leaves for a given employee identifier
     * @param int $userId identifier of an employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual($userId) {
        $this->load->model('leaves_model');
        $result = $this->leaves_model->getLeavesOfEmployee($userId);
        if (empty($result)) {
            echo "";
        } else {
            //Get timezone and language of the user
            $this->getTimezoneAndLanguageOfUser($userId);

            $vcalendar = new VObject\Component\VCalendar();
            foreach ($result as $leave) {
                if (($leave['status'] != LMS_CANCELED) && ($leave['status'] != LMS_REJECTED)) {
                    $vevent = $this->createVEvent($leave);
                    $vevent->SUMMARY = lang('leave');
                    $vevent->DESCRIPTION = $leave['cause'];

                    $vcalendar->add($vevent);
                }
            }

            $this->add_vtimezone($vcalendar, $this->timezone);

            echo $vcalendar->serialize();
        }
    }

    /**
     * Get the list of leaves for a group of employees attached to an entity
     * @param int $userId identifier of the user wanting to view the list (mind timezone)
     * @param int $entity identifier of an entity
     * @param bool $children TRUE include sub-entity, FALSE otherwise (default)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function entity($userId, $entity, $children) {
        $this->load->model('leaves_model');
        $children = filter_var($children, FILTER_VALIDATE_BOOLEAN);
        $result = $this->leaves_model->entity($entity, $children);
        if (empty($result)) {
            echo "";
        } else {
            //Get timezone and language of the user
            $this->getTimezoneAndLanguageOfUser($userId);

            $vcalendar = new VObject\Component\VCalendar();
            foreach ($result as $leave) {
                if (($leave['status'] != LMS_CANCELED) && ($leave['status'] != LMS_REJECTED)) {
                    $vevent = $this->createVEvent($leave);
                    $vevent->SUMMARY = $leave['firstname'] . ' ' . $leave['lastname'];
                    $vevent->DESCRIPTION = $leave['type'] . ($leave['cause']!=''?(' / ' . $leave['cause']):'');

                    $vcalendar->add($vevent);
                }
            }

            $this->add_vtimezone($vcalendar, $this->timezone);

            echo $vcalendar->serialize();
        }
    }

    /**
     * Get the list of leaves of the collaborators of the connected user (manager)
     * @param int $userId identifier of the user wanting to view the list (mind timezone)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators($userId) {
        $this->load->model('leaves_model');
        $result = $this->leaves_model->getLeavesRequestedToManager($userId, TRUE);
        if (empty($result)) {
            echo "";
        } else {
            //Get timezone and language of the user
            $this->getTimezoneAndLanguageOfUser($userId);

            $vcalendar = new VObject\Component\VCalendar();
            foreach ($result as $leave) {
                if (($leave['status'] != LMS_CANCELED) && ($leave['status'] != LMS_REJECTED)) {
                    $vevent = $this->createVEvent($leave);
                    $vevent->SUMMARY = $leave['firstname'] . ' ' . $leave['lastname'];
                    $vevent->DESCRIPTION = $leave['type_label'] . ($leave['cause']!=''?(' / ' . $leave['cause']):'');

                    $vcalendar = new VObject\Component\VCalendar();
                    $vcalendar->add($vevent);
                }
            }

            $this->add_vtimezone($vcalendar, $this->timezone);

            echo $vcalendar->serialize();
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
        $this->getTimezoneAndLanguageOfUser($leave['employee']);

        $vevent = $this->createVEvent($leave);

        $vcalendar = new VObject\Component\VCalendar();
        $vcalendar->add($vevent);

        $this->add_vtimezone($vcalendar, $this->timezone);

        echo $vcalendar->serialize();
    }
}
