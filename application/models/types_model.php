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

class Types_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {
        
    }

    /**
     * Get the label of a given type id
     * @param type $id
     * @return string label
     */
    public function get_label($id) {
        switch ($id) {
            case 1 : return 'paid leave';
            case 2 : return 'maternity leave';
            case 3 : return 'paternity leave';
            case 4 : return 'special leave';
            case 4 : return 'sick leave';
            default : return 'Unknown';
        }
    }
}
	