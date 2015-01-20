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
 */

//This CI custom library is just a wrapper around PHPExcel library
require_once APPPATH . "/third_party/PHPExcel.php"; 
 
class Excel extends PHPExcel {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct(); 
    }
    
    /**
     * Return the excel column name for a given column index
     * @param type $number
     * @return string
     */
    public function column_name($number) {
        if ($number < 27) {
            return substr("ABCDEFGHIJKLMNOPQRSTUVWXYZ", $number - 1, 1);
        } else {
            return substr("AAABACADAEAFAGAHAIAJAKALAMANAOAPAQARASATAUAVAWAXAYAZ", (($number -27) * 2), 2);
        }
    }

}