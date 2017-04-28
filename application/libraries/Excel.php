<?php
/**
 * This CI custom library is just a wrapper around PHPExcel library
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }
require_once FCPATH . "vendor/autoload.php";

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
