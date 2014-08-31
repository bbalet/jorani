<?php
/* 
 * This file is part of lms.
 *
 * lms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

class Contracts_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {
        
    }

    /**
     * Get the list of contracts or one contract
     * @param int $id optional id of a contract
     * @return array record of contracts
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_contracts($id = 0) {
        if ($id === 0) {
            $query = $this->db->get('contracts');
            return $query->result_array();
        }
        $query = $this->db->get_where('contracts', array('id' => $id));
        return $query->row_array();
    }
    
    /**
     * Get the label for a given contract id
     * @param type $id
     * @return string label
     */
    public function get_label($id) {
        $record = $this->get_contracts($id);
        if (count($record) > 0) {
            return $record['name'];
        } else {
            return '';
        }
    }
    
    /**
     * Insert a new contract into the database. Inserted data are coming from an
     * HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_contracts() {
        $startentdate = str_pad($this->input->post('startentdatemonth'), 2, "0", STR_PAD_LEFT) .
                "/" . str_pad($this->input->post('startentdateday'), 2, "0", STR_PAD_LEFT);
        $endentdate = str_pad($this->input->post('endentdatemonth'), 2, "0", STR_PAD_LEFT) .
                "/" . str_pad($this->input->post('endentdateday'), 2, "0", STR_PAD_LEFT);
        $data = array(
            'name' => $this->input->post('name'),
            'startentdate' => $startentdate,
            'endentdate' => $endentdate
        );
        return $this->db->insert('contracts', $data);
    }
    
    /**
     * Delete a contract from the database
     * @param int $id identifier of the contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete_contract($id) {
        $query = $this->db->delete('contracts', array('id' => $id));
        $this->load->model('entitleddays_model');
        $this->entitleddays_model->delete_entitleddays_cascade_contract($id);
    }
    
    /**
     * Update a given contract in the database. Update data are coming from an
     * HTML form
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function update_contract() {
        
        $startentdate = str_pad($this->input->post('startentdatemonth'), 2, "0", STR_PAD_LEFT) .
                "/" . str_pad($this->input->post('startentdateday'), 2, "0", STR_PAD_LEFT);
        $endentdate = str_pad($this->input->post('endentdatemonth'), 2, "0", STR_PAD_LEFT) .
                "/" . str_pad($this->input->post('endentdateday'), 2, "0", STR_PAD_LEFT);
        $data = array(
            'name' => $this->input->post('name'),
            'startentdate' => $startentdate,
            'endentdate' => $endentdate
        );

        $this->db->where('id', $this->input->post('id'));
        return $this->db->update('contracts', $data);
    }
    
    /**
     * Get the list of contracts or one contract
     * @param int $contract identifier of the contract
     * @param string $year year to be displayed on the calendar
     * @return array record of contracts
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_dayoffs($contract, $year) {
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
     * Delete a day off into the day offs table
     * @param int $contract Identifier of the contract
     * @param string $timestamp Date of the day off
     * @return bool outcome of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deletedayoff($contract, $timestamp) {
        $this->db->where('contract', $contract);
        $this->db->where('date', date('Y/m/d', $timestamp));
        return $this->db->delete('dayoffs');
    }
    
    /**
     * Delete a list of day offs into the day offs table
     * @param int $contract Identifier of the contract
     * @param string $dateList comma-separated list of dates
     * @return bool outcome of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deletedayoffs($contract, $dateList) {
        $dates = explode(",", $dateList);
        $this->db->where('contract', $contract);
        $this->db->where_in('date', $dates);
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
    public function adddayoffs($contract, $type, $title, $dateList) {
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
     * Get the sum of day offs between two dates for a given contract
     * @param int $contract contract identifier
     * @param date $start start date
     * @param date $end end date
     * @return int number of day offs
     */
    public function sumdayoffs($contract, $start, $end) {
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
    public function adddayoff($contract, $timestamp, $type, $title) {

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
     * All day offs of a given user
     * @param int $user_id connected user
     * @param string $start Start date displayed on calendar
     * @param string $end End date displayed on calendar
     * @return string JSON encoded list of full calendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function userDayoffs($user_id, $start = "", $end = "") {
        $this->load->helper('language');
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
                    $allDay = true;
                    break;
                case 2:
                    $title = lang('Morning') . ': ' . $entry->title;
                    $startdate = $entry->date . 'T07:00:00';
                    $enddate = $entry->date . 'T12:00:00';
                    $allDay = false;
                    break;
                case 3:
                    $title = lang('Afternoon') . ': ' . $entry->title;
                    $startdate = $entry->date . 'T12:00:00';
                    $enddate = $entry->date . 'T18:00:00';
                    $allDay = false;
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
        $this->load->helper('language');
        $this->lang->load('calendar', $this->session->userdata('language'));
        
        $this->db->select('dayoffs.*, contracts.name');
        $this->db->distinct();
        $this->db->join('contracts', 'dayoffs.contract = contracts.id');
        $this->db->join('users', 'users.contract = contracts.id');
        $this->db->join('organization', 'users.organization = organization.id');
        $this->db->where('date >=', $start);
        $this->db->where('date <=', $end);
        
        if ($children == true) {
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
                    $allDay = true;
                    break;
                case 2:
                    $title = lang('Morning') . ': ' . $entry->title;
                    $startdate = $entry->date . 'T07:00:00';
                    $enddate = $entry->date . 'T12:00:00';
                    $allDay = false;
                    break;
                case 3:
                    $title = lang('Afternoon') . ': ' . $entry->title;
                    $startdate = $entry->date . 'T12:00:00';
                    $enddate = $entry->date . 'T18:00:00';
                    $allDay = false;
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
}
