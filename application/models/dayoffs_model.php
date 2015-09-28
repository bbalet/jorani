<?php
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

class Dayoffs_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {
        
    }
    
    /**
     * Get the list of dayofs for a contract and a civil year (not to be confused with the yearly period)
     * @param int $contract identifier of the contract
     * @param string $year year to be displayed on the calendar
     * @return array record of contracts
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getDaysOffForCivilYear($contract, $year) {
        $this->db->select('DAY(date) as d, MONTH(date) as m, YEAR(date) as y, type, title');
        $this->db->where('contract', $contract);
        $this->db->where('YEAR(date)', $year);
        $query = $this->db->get('dayoffs');
        $dayoffs =array();
        foreach($query->result() as $row)
        {
            //We decompose the date before creating the unix timestamp because there are diffrences of
            //few hours depending the configuration of the system hosting the db (due to time part ?).
            $timestamp = mktime(0, 0, 0, $row->m, $row->d, $row->y);
            $dayoffs[$timestamp][0] = $row->type;
            $dayoffs[$timestamp][1] = $row->title;
        }
        return $dayoffs;
    }
    
    /**
     * Get the list of dayofs for a contract (suitable fo ICS feed)
     * @param int $contract identifier of the contract
     * @return array record of contracts
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getDaysOffForContract($contract) {
        $this->db->where('contract', $contract);
        $query = $this->db->get('dayoffs');
        $this->db->where("date >= DATE_SUB(NOW(),INTERVAL 2 YEAR"); //Security/performance limit
        return $query->result();
    }
    
    
    /**
     * Delete a day off into the day offs table
     * @param int $contract Identifier of the contract
     * @param string $timestamp Date of the day off
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteDayOff($contract, $timestamp) {
        $this->db->where('contract', $contract);
        $this->db->where('date', date('Y/m/d', $timestamp));
        return $this->db->delete('dayoffs');
    } 
    
    /**
     * Delete a day off into the day offs table
     * @param int $contract Identifier of the contract
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteDaysOffCascadeContract($contract) {
        $this->db->where('contract', $contract);
        return $this->db->delete('dayoffs');
    }
    
    /**
     * Delete a list of day offs into the day offs table
     * @param int $contract Identifier of the contract
     * @param string $dateList comma-separated list of dates
     * @return bool outcome of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteListOfDaysOff($contract, $dateList) {
        $dates = explode(",", $dateList);
        $this->db->where('contract', $contract);
        $this->db->where_in('DATE_FORMAT(date, \'%Y-%m-%d\')', $dates);
        return $this->db->delete('dayoffs');
    }

    /**
     * Insert a list of day offs into the day offs table
     * @param int $contract Identifier of the contract
     * @param int $type 1:day, 2:morning, 3:afternoon
     * @param string $title Short description of the day off
     * @param string $dateList comma-separated list of dates
     * @return bool outcome of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function addListOfDaysOff($contract, $type, $title, $dateList) {
        //Prepare a command in order to insert multiple rows with one query MySQL
        $dates = explode(",", $dateList);
        $data = array();
        foreach ($dates as $date) {
            $row = array(
                'contract' => $contract,
                'date' => date('Y-m-d', strtotime($date)),
                'type' => $type,
                'title' => $title
            );
            array_push($data, $row);
        }
        return $this->db->insert_batch('dayoffs', $data); 
    }
    
    /**
     * Copy a list of days off of a source contract to a destination contract (for a given civil year)
     * @param int $source identifier of the source contract
     * @param int $destination identifier of the destination contract
     * @param string $year civil year (and not yearly period)
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function copyListOfDaysOff($source, $destination, $year) {
        //Delete all previous days off defined on the destination contract (avoid duplicated data)
        $this->db->where('contract', $destination);
        $this->db->where('YEAR(date)', $year);
        $this->db->delete('dayoffs');
        
        //Copy source->destination days off
        $sql = 'INSERT dayoffs(contract, date, type, title) ' .
                ' SELECT ' . $this->db->escape($destination) . ', date, type, title ' .
                ' FROM dayoffs ' .
                ' WHERE contract = ' . $this->db->escape($source) .
                ' AND YEAR(date) = ' . $this->db->escape($year);
        $query = $this->db->query($sql);
        return $query;
    }
    
    /**
     * Get the length of days off between two dates for a given contract
     * @param int $contract contract identifier
     * @param date $start start date
     * @param date $end end date
     * @return int number of day offs
     */
    public function lengthDaysOffBetweenDates($contract, $start, $end) {
        $this->db->select('sum(CASE `type` WHEN 1 THEN 1 WHEN 2 THEN 0.5 WHEN 3 THEN 0.5 END) as days');
        $this->db->where('contract', $contract);
        $this->db->where('date >=', $start);
        $this->db->where('date <=', $end);
        $this->db->from('dayoffs');
        $result = $this->db->get()->result_array();
        return $result[0]['days']; 
    }
    
    /**
     * Insert a day off into the day offs table
     * @param int $contract Identifier of the contract
     * @param string $timestamp Date of the day off
     * @param int $type 1:day, 2:morning, 3:afternoon
     * @param string $title Short description of the day off
     * @return bool outcome of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function addDayOff($contract, $timestamp, $type, $title) {
        $this->db->select('id');
        $this->db->where('contract', $contract);
        $this->db->where('date', date('Y/m/d', $timestamp));
        $query = $this->db->get('dayoffs');
        if ($query->num_rows() > 0) {
            $data = array(
                'date' => date('Y/m/d', $timestamp),
                'type' => $type,
                'title' => $title
            );
            $this->db->where('id', $query->row('id'));
            return $this->db->update('dayoffs', $data);
        } else {
            $data = array(
                'contract' => $contract,
                'date' => date('Y/m/d', $timestamp),
                'type' => $type,
                'title' => $title
            );
            return $this->db->insert('dayoffs', $data);
        }
    }
    
    /**
     * Import an ICS feed containing days off (all events are considered as non-working days)
     * This first version is very basic, it supports only full days off
     * @param int $contract Identifier of the contract
     * @param string $url URL of the source ICS feed
     * @return string error message or empty string
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function importDaysOffFromICS($contract, $url) {
        require_once(APPPATH . 'third_party/VObjects/vendor/autoload.php');
        $ical = VObject\Reader::read(fopen($url,'r'), VObject\Reader::OPTION_FORGIVING);
        foreach($ical->VEVENT as $event) {
            $start = new DateTime($event->DTSTART);
            $end = new DateTime($event->DTEND);
            $interval = $start->diff($end);
            //Make a more complicated version that supports half days
            $length = $interval->d;
            $day = $start;
            for ($ii = 0; $ii < $length; $ii++) {
                $tmp = $day->format('U');
                $this->deletedayoff($contract, $tmp);
                $this->adddayoff($contract, $tmp, 1, strval($event->SUMMARY));
                
                $day->add(new DateInterval('P1D'));
            }
        }
    }
    
    /**
     * All day offs of a given user
     * @param int $user_id connected user
     * @param string $start Start date displayed on calendar
     * @param string $end End date displayed on calendar
     * @return string JSON encoded list of full calendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function userDayoffs($user_id, $start = "", $end = "") {
        $this->lang->load('calendar', $this->session->userdata('language'));
        $this->db->select('dayoffs.*');
        $this->db->join('dayoffs', 'users.contract = dayoffs.contract');
        $this->db->where('users.id', $user_id);
        $this->db->where('date >=', $start);
        $this->db->where('date <=', $end);
        $events = $this->db->get('users')->result();
        
        $jsonevents = array();
        foreach ($events as $entry) {
            switch ($entry->type)
            {
                case 1:
                    $title = $entry->title;
                    $startdate = $entry->date . 'T07:00:00';
                    $enddate = $entry->date . 'T18:00:00';
                    $allDay = TRUE;
                    break;
                case 2:
                    $title = lang('Morning') . ': ' . $entry->title;
                    $startdate = $entry->date . 'T07:00:00';
                    $enddate = $entry->date . 'T12:00:00';
                    $allDay = FALSE;
                    break;
                case 3:
                    $title = lang('Afternoon') . ': ' . $entry->title;
                    $startdate = $entry->date . 'T12:00:00';
                    $enddate = $entry->date . 'T18:00:00';
                    $allDay = FALSE;
                    break;
            }
            $jsonevents[] = array(
                'id' => $entry->id,
                'title' => $title,
                'start' => $startdate,
                'color' => '#000000',
                'allDay' => $allDay,
                'end' => $enddate
            );
        }
        return json_encode($jsonevents);
    }
    
    /**
     * All day offs for the organization
     * @param string $start Start date displayed on calendar
     * @param string $end End date displayed on calendar
     * @param integer $entity_id identifier of the entity
     * @param boolean $children include all sub entities or not
     * @return string JSON encoded list of full calendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function allDayoffs($start, $end, $entity_id, $children) {
        $this->lang->load('calendar', $this->session->userdata('language'));
        
        $this->db->select('dayoffs.*, contracts.name');
        $this->db->distinct();
        $this->db->join('contracts', 'dayoffs.contract = contracts.id');
        $this->db->join('users', 'users.contract = contracts.id');
        $this->db->join('organization', 'users.organization = organization.id');
        $this->db->where('date >=', $start);
        $this->db->where('date <=', $end);
        
        if ($children === TRUE) {
            $this->load->model('organization_model');
            $list = $this->organization_model->get_all_children($entity_id);
            $ids = array();
            if (count($list) > 0) {
                $ids = explode(",", $list[0]['id']);
            }
            array_push($ids, $entity_id);
            $this->db->where_in('organization.id', $ids);
        } else {
            $this->db->where('organization.id', $entity_id);
        }        
        
        $events = $this->db->get('dayoffs')->result();
        
        $jsonevents = array();
        foreach ($events as $entry) {
            switch ($entry->type)
            {
                case 1:
                    $title = $entry->title;
                    $startdate = $entry->date . 'T07:00:00';
                    $enddate = $entry->date . 'T18:00:00';
                    $allDay = TRUE;
                    break;
                case 2:
                    $title = lang('Morning') . ': ' . $entry->title;
                    $startdate = $entry->date . 'T07:00:00';
                    $enddate = $entry->date . 'T12:00:00';
                    $allDay = FALSE;
                    break;
                case 3:
                    $title = lang('Afternoon') . ': ' . $entry->title;
                    $startdate = $entry->date . 'T12:00:00';
                    $enddate = $entry->date . 'T18:00:00';
                    $allDay = FALSE;
                    break;
            }
            $jsonevents[] = array(
                'id' => $entry->id,
                'title' => $entry->name . ': ' . $title,
                'start' => $startdate,
                'color' => '#000000',
                'allDay' => $allDay,
                'end' => $enddate
            );
        }
        return json_encode($jsonevents);
    }
    
    /**
     * Purge the table by deleting the records prior $toDate
     * @param date $toDate 
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function purgeDaysoff($toDate) {
        $this->db->where('date <= ', $toDate);
        return $this->db->delete('entitleddays');
    }

    /**
     * Count the number of rows into the table
     * @return int number of rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function count() {
        $this->db->select('count(*) as number', FALSE);
        $this->db->from('dayoffs');
        $result = $this->db->get();
        return $result->row()->number;
    }
    
    /**
     * All day offs of a given employee and between two dates
     * @param int $user_id connected user
     * @param string $start Start date displayed on calendar
     * @param string $end End date displayed on calendar
     * @return array list of day offs
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function lengthDaysOffBetweenDatesForEmployee($id, $start, $end) {
        $this->db->select('dayoffs.*');
        $this->db->join('dayoffs', 'users.contract = dayoffs.contract');
        $this->db->where('users.id', $id);
        $this->db->where('date >= DATE(' . $this->db->escape($start) . ')');
        $this->db->where('date <= DATE(' . $this->db->escape($end) . ')');
        $dayoffs = $this->db->get('users')->result();
        return $dayoffs;
    }
}
