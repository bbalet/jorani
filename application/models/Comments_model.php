<?php
/**
 * This Model contains all the business logic and the persistence layer for comments of a leave.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.7.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class contains the business logic and manages the persistence of leave requests.
 */
class Comments_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {

    }

    /**
     * Get the JSON of all comments of a leave
     * @param int $id Id of the leave request
     * @return array list of records
     * @author Emilien NICOLAS <milihhard1996@gmail.com>
     */
    public function getCommentsLeaveJson($id){
      return "{
        \"comments\" : [
          {
            \"type\" : \"comment\",
            \"author\" : 2,
            \"value\" : \"Je prend un congé parce que c'est comme ça.\",
            \"date\" : \"2017-07-04\"
          },
          {
            \"type\" : \"change\",
            \"status_number\" : 4,
            \"date\" : \"2017-07-05\"
          },
          {
            \"type\" : \"comment\",
            \"author\" : 4,
            \"value\" : \"C'est mort.\",
            \"date\" : \"2017-07-05\"
          },
          {
            \"type\" : \"comment\",
            \"author\" : 1,
            \"value\" : \"Non ca ne peut pas se faire comme ca!\",
            \"date\" : \"2017-07-05\"
          },
          {
            \"type\" : \"change\",
            \"status_number\" : 2,
            \"date\" : \"2017-07-05\"
          }
        ]
      }";
    }

    /**
     * Get the list of all comments of a leave
     * @param int $id Id of the leave request
     * @return array list of records
     * @author Emilien NICOLAS <milihhard1996@gmail.com>
     */
    public function getCommentsLeave($id){
      return json_decode($this->getCommentsLeaveJson($id));
    }

}
