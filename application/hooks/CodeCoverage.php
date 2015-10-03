<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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

require_once APPPATH . "third_party/phpcov/vendor/autoload.php";
global $coverage;
$filter = new PHP_CodeCoverage_Filter();
$filter->addDirectoryToBlacklist(SYSDIR);
$filter->addDirectoryToBlacklist(APPPATH . 'config');
$filter->addDirectoryToBlacklist(APPPATH . 'core');
$filter->addDirectoryToBlacklist(APPPATH . 'hooks');
$filter->addDirectoryToBlacklist(APPPATH . 'language');
$filter->addDirectoryToBlacklist(APPPATH . 'third_party');
$filter->addDirectoryToBlacklist(FCPATH . 'local' );
$coverage = new PHP_CodeCoverage(null, $filter);

/**
 * Start to analysis the code coverage of the current request
 * @global PHP_CodeCoverage $coverage
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function start() {
    if (!isset($_GET["STOP_COVERAGE_ANALISYS"])) {
        global $coverage;
        $coverage->start('manual test');
    }
}

/**
 * Stop to analysis the code coverage of the current request
 * If STOP_COVERAGE_ANALISYS is set in GET parameters, build the code coverage report
 * @global PHP_CodeCoverage $coverage
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function stop() {
    global $coverage;
    if (!isset($_GET["STOP_COVERAGE_ANALISYS"])) {
        $coverage->stop();
        $s = serialize($coverage);
        $token = uniqid();
        file_put_contents(FCPATH . 'local/temp/coverage/requests/' . $token, $s);
    } else {
        $files = glob(FCPATH . 'local/temp/coverage/requests/*');
        foreach($files as $file) {
                $s = file_get_contents($file);
                $data = unserialize($s);
                $coverage->merge($data);
        }
        $writer = new PHP_CodeCoverage_Report_HTML;
        $writer->process($coverage, FCPATH . 'local/temp/coverage/code-coverage-report');
        
        //Delete all files that were used to trace the requests
        $files = glob(FCPATH . 'local/temp/coverage/requests/*');
        foreach($files as $file){
          if(is_file($file))
            unlink($file);
        }
    }
}
