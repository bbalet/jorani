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
 */

/*
 * History tables are named {table}_history and they are used to store the modifications brought on some tables. They have
 * the same structure than the table they keep history for, but with the following additional columns :
 * modification_id
 * moditifed_type
 * modified_by
 * modified_date
 * 
 * Of course, the PK is no more the identifier of the object but modification_id.
 * Modification types are :
 *     1 - create
 *     2 - update
 *     3 - delete
 */
class History_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {
        
    }
    
    /**
     * Get a list of modification
     * @param int $type Type of modification (create, update, delete) or 0 for any
     * @param string $table Table modified
     * @param int $user_id Identifier of the modifier
     * @param date $name Description
     * @param date $name Description
     * @return result rows as array of arrays
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_history($type, $table, $user_id, $startdate, $enddate) {
        $this->db->select("CONCAT(users.firstname, ' ', users.lastname) as user, " . $table . "_history.*", FALSE);
        $this->db->join('users', 'modified_by = users.id', 'left');
        if ($type != 0) $this->db->where('modification_type', $type);
        if (!is_null($user_id)) $this->db->where('modified_by', $user_id);
        if (!is_null($startdate) && is_null($enddate)) $this->db->where('DATE(modified_date)', $startdate);
        if (is_null($startdate) && !is_null($enddate)) $this->db->where('DATE(modified_date)', $enddate);
        if (!is_null($startdate) && !is_null($enddate)) $this->db->where('DATE(modified_date) >= ', $startdate);
        if (!is_null($startdate) && !is_null($enddate)) $this->db->where('DATE(modified_date) <=', $enddate);
        $this->db->order_by("modified_date", "desc"); 
        $query = $this->db->get($table . '_history');
        $results = $query->result_array();
        var_dump($this->db->last_query());
        var_dump($results);
        return $results;
    }
    
    /**
     * Get the details of a modification
     * @param string $table Table modified
     * @param type $id Unique Identifier of the modification
     * @return result row as an array
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_history_detail($table, $id) {
        $query = $this->db->get_where($table . '_history', array('modification_id' => $id));
        return $query->row_array();
    }
    
    /**
     * Insert a modification into the history table of the modified object (source table)
     * @param int $type Type of modification (create, update, delete)
     * @param string $table Table modified
     * @param int $id Identifier of the object (can be returned by the last inserted id function)
     * @param int $user_id Identifier of the connected user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function set_history($type, $table, $id, $user_id) {
        $sql = 'INSERT INTO ' . $table . '_history';
        $sql .= ' SELECT *, NULL,';
        $sql .= ' ' . $type;
        $sql .= ', ' . $user_id;
        $sql .= ', NOW() FROM ' . $table . ' WHERE id = ' . $id;
        $this->db->query($sql);
    }
    
    /**
     * Purge the table by deleting the records prior $toDate
     * @param string $table Source Table
     * @param date $toDate 
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function purge_history($table, $toDate) {
        $this->db->where('modified_date <= ', $toDate);
        return $this->db->delete($table);
    }

    /**
     * Count the number of rows into the table
     * @param string $table Source Table
     * @return int number of rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function count($table) {
        $this->db->select('count(*) as number',false);
        $this->db->from($table);
        $result = $this->db->get();
        return $result->row()->number;
    }
}
?>
