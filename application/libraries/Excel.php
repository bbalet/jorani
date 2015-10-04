<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }
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

/**
 * This CI custom library is just a wrapper around PHPExcel library
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 * @license      http://opensource.org/licenses/GPL-3.0 GPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

require_once APPPATH . "third_party/PHPExcel.php";

/**
 * This class is a wrapper around PHPExcel library.
 * We added a utility function that return a column name (string) from an index.
 */
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
     * This code example:
     * <code>
     * echo $excel->column_name(6);
     * </code>
     * would return F
     * @param int $number Column index
     * @return string Excel representation of the column index
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function column_name($number) {
        if ($number < 27) {
            return substr("ABCDEFGHIJKLMNOPQRSTUVWXYZ", $number - 1, 1);
        } else {
            return substr("AAABACADAEAFAGAHAIAJAKALAMANAOAPAQARASATAUAVAWAXAYAZ", (($number -27) * 2), 2);
        }
    }

}
