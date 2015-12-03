<?php
/**
 * This CodeIginiter hook allows you to analyze the code coverage during a test.
 * In order to activate it, open application/config/config.php and switch the hook variable to TRUE :
 * $config['enable_hooks'] = TRUE;
 * From that moment, all HTTP sessions will be analyzed.If you want to stop the current analysis and 
 * to build a coverage report, add STOP_COVERAGE_ANALISYS to the URL :
 * http://localhost/jorani/home?STOP_COVERAGE_ANALISYS
 * The Code coverage report will be produced into local/temp/coverage/code-coverage-report folder.
 * Beware that this hook seriously degrades the performance of the application, so once finnished, turn it off :
 * $config['enable_hooks'] = FALSE;
 * @copyright  Copyright (c) 2014-2015 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.3
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Curent instance of CodeCoverage object used during all the HTTP request.
 * Note that we explicitly exclude CI libraries from the code coverage analisys.
 * @global PHP_CodeCoverage $coverage
 * @name $coverage
 */
global $coverage;

require_once APPPATH . "third_party/phpcov/vendor/autoload.php";
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
 * @global $coverage Curent instance of CodeCoverage object
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
function start() {
    if (!isset($_GET["STOP_COVERAGE_ANALISYS"])) {
        global $coverage;
        $coverage->start('manual test');
    }
}

/**
 * Stop to analyze the code coverage of the current HTTP request and serialize the result into a temp file.
 * If STOP_COVERAGE_ANALISYS is set as a GET parameter :
 *  - build the code coverage report
 *  - delete the temporary files used for the code coverage session
 * @global $coverage Curent instance of CodeCoverage object
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
