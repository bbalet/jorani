<?php
/*
 * THIS SCRIPT SHOULDN'T BE USED IN PRODUCTION. THIS IS A CODE SAMPLE.
 * 
 * This script is an example that illustrates how to use Jorani's REST API so as to add
 * a seniority entitled day to an employee based on his entry date.
 * 
 * Of course, we don't pretend to cover all use cases as this bonus varies from one 
 * organization to another. In this example, we will add one day of seniority leave to
 * any employee being in the company since more than a year (for the current year).
 * You need to adapt the script to your needs (for example, some companies add
 * 1 extra day for 5 years, etc.).
 * 
 * Then you can launch this script using a cron tab or by any other tool that allows
 *  you to start it on a regular basis.
 * 
 * How to use this test file :
 *  - Insert the test dataset into DB :
 *    INSERT INTO oauth_clients (client_id, client_secret, redirect_uri) VALUES ("testclient", "testpass", "http://fake/");
 * 
 */

require("JoraniAPI.php");

$nb_of_days = 1;    //Number of days to be added to the credit
$leave_type = 1;    //Id of the leave type for seniority leave (see list of leave types into the web ui)
$condition = ' - 1 year';   //Condition for date hired ' - 365 day'
//Current yearly period
$startdate = date('Y') . '-01-01';
$enddate = date('Y') . '-12-31';

//Connect to the REST API
$api = new JoraniAPI('http://localhost/jorani/', 'testclient', 'testpass');

//Get the list of employees
$employees = $api->getEmployeesList();
//echo "Employees  = " . var_dump($employees) . PHP_EOL;

//Iterate on the employees list (datehired > $condition)
$last_year = new DateTime(date('Y-m-d',strtotime(date("Y-m-d", time()) . $condition)));

//TODO : get the label of Seniority Leave ?

foreach ($employees as $employee){
    $datehired = new DateTime($employee->datehired);
    if ($datehired < $last_year) {
        echo "senior employee  = " . $employee->id . PHP_EOL;
        //Check if we already credited seniority entitled days for the current period
        $entitled_days = $api->getEntitledDaysEmployeeList($employee->id);
        echo "entitled days  = " . var_dump($entitled_days) . PHP_EOL;
        $found = FALSE;
        foreach ($entitled_days as $credit){
            echo "Test 1  = " . ($credit->startdate == $startdate) . PHP_EOL;
            echo "Test 2  = " . ($credit->enddate == $enddate) . PHP_EOL;
            echo "Test 3  = " . ($credit->type == $leave_type) . PHP_EOL;
            //TODO :Return of API is a string for leave type and not a number !
            
            if (($credit->startdate == $startdate) &&
                    ($credit->enddate == $enddate) &&
                    ($credit->type == $leave_type)) {
                echo "The employee already has seniority bonus." . PHP_EOL;
                $found = TRUE;
            }
        }
        if (!$found) {
            $description = "Inserted by Seniority Robot";
            $result = $api->addEntitledDaysEmployee($employee->id, $startdate, $enddate, $nb_of_days, $leave_type, $description);
            echo "RESULT / entitled days  = " . $result . PHP_EOL;
        }
    } else {
        echo "recent employee  = " . $employee->id . PHP_EOL;
    }
}
