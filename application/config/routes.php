<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

//Admin : user management
$route['users/myprofile'] = 'users/myprofile';
$route['users/employees'] = 'users/employees';
$route['users/export'] = 'users/export';
$route['users/reset/(:num)'] = 'users/reset/$1';
$route['users/create'] = 'users/create';
$route['users/edit/(:num)'] = 'users/edit/$1';
$route['users/delete/(:num)'] = 'users/delete/$1';
$route['users/check/login'] = 'users/check_login';
$route['users/enable/(:num)'] = 'users/enable/$1';
$route['users/disable/(:num)'] = 'users/disable/$1';
$route['users'] = 'users';

//Human Resources Management
$route['hr/employees'] = 'hr/employees';
$route['hr/employees/entity/(:num)/(:any)'] = 'hr/employees_entity/$1/$2';
$route['hr/employees/export/(:num)/(:any)'] = 'hr/export_employees/$1/$2';
$route['hr/leaves/(:num)'] = 'hr/leaves/$1';
$route['hr/leaves/export/(:num)'] = 'hr/export_leaves/$1';
$route['hr/overtime/(:num)'] = 'hr/overtime/$1';
$route['hr/counters/(:num)'] = 'hr/counters/$1';
$route['hr/counters/(:num)/(:num)'] = 'hr/counters/$1/$2';
$route['hr/overtime/export/(:num)'] = 'hr/export_overtime/$1';
$route['hr/entitleddays/(:num)'] = 'hr/entitleddays/$1';
$route['hr/leaves/create/(:num)'] = 'hr/createleave/$1';
$route['hr/presence/([^/]+)/(:num)'] = 'hr/presence/$1/$2';
$route['hr/presence/([^/]+)/(:num)/(:num)/(:num)'] = 'hr/presence/$1/$2/$3/$4';
$route['hr/presence/export/([^/]+)/(:num)/(:num)/(:num)'] = 'hr/export_presence/$1/$2/$3/$4';
$route['hr'] = 'hr/employees';

//HR edit leave types
$route['leavetypes/delete/(:num)'] = 'leavetypes/delete/$1';
$route['leavetypes/edit/(:num)'] = 'leavetypes/edit/$1';
$route['leavetypes/index'] = 'leavetypes/index';
$route['leavetypes/create'] = 'leavetypes/create';
$route['leavetypes/export'] = 'leavetypes/export';
$route['leavetypes'] = 'leavetypes';

//HR edit positions
$route['positions/delete/(:num)'] = 'positions/delete/$1';
$route['positions/edit/(:num)'] = 'positions/edit/$1';
$route['positions/index'] = 'positions/index';
$route['positions/select'] = 'positions/select';
$route['positions/create'] = 'positions/create';
$route['positions/export'] = 'positions/export';
$route['positions'] = 'positions';

//HR edit contracts
$route['contracts/export'] = 'contracts/export';
$route['contracts/create'] = 'contracts/create';
$route['contracts/edit/(:num)'] = 'contracts/edit/$1';
$route['contracts/update'] = 'contracts/update';
$route['contracts/delete/(:num)'] = 'contracts/delete/$1';
$route['contracts/(:num)/calendar/(:num)'] = 'contracts/calendar/$1/$2';
$route['contracts/(:num)/calendar'] = 'contracts/calendar/$1';
$route['contracts/(:num)/calendar/(:num)/copy/(:num)'] = 'contracts/copydayoff/$1/$3/$2';
$route['contracts/calendar/edit'] = 'contracts/editdayoff';
$route['contracts/calendar/series'] = 'contracts/series';
$route['contracts/calendar/import'] = 'contracts/import';
$route['contracts/calendar/userdayoffs/(:num)'] = 'contracts/userDayoffs/$1';
$route['contracts/calendar/userdayoffs'] = 'contracts/userDayoffs';
$route['contracts/calendar/alldayoffs'] = 'contracts/allDayoffs';
$route['contracts'] = 'contracts';

//HR Organization
$route['organization/select'] = 'organization/select';
$route['organization/root'] = 'organization/root';
$route['organization/delete'] = 'organization/delete';
$route['organization/create'] = 'organization/create';
$route['organization/rename'] = 'organization/rename';
$route['organization/move'] = 'organization/move';
$route['organization/copy'] = 'organization/copy';
$route['organization/employees'] = 'organization/employees';
$route['organization/addemployee'] = 'organization/addemployee';
$route['organization/delemployee'] = 'organization/delemployee';
$route['organization/getsupervisor'] = 'organization/getsupervisor';
$route['organization/setsupervisor'] = 'organization/setsupervisor';
$route['organization'] = 'organization';

//Various calendars
$route['calendar/individual'] = 'calendar/individual';
$route['calendar/workmates'] = 'calendar/workmates';
$route['calendar/collaborators'] = 'calendar/collaborators';
$route['calendar/organization'] = 'calendar/organization';
$route['calendar/department'] = 'calendar/department';
$route['calendar/tabular'] = 'calendar/tabular';
$route['calendar/tabular/(:num)/(:num)/(:num)/(:any)'] = 'calendar/tabular/$1/$2/$3/$4';
$route['calendar/tabular/export/(:num)/(:num)/(:num)/(:any)'] = 'calendar/tabular_export/$1/$2/$3/$4';
$route['calendar/year/(:num)/(:num)'] = 'calendar/year/$1/$2';
$route['calendar/year/(:num)'] = 'calendar/year/$1';
$route['calendar/year'] = 'calendar/year';
$route['calendar/year/export/(:num)/(:num)'] = 'calendar/year_export/$1/$2';
$route['calendar'] = 'calendar/individual';

//private Fullcalendar feeds
$route['leaves/individual/(:num)'] = 'leaves/individual/$1';
$route['leaves/individual'] = 'leaves/individual';
$route['leaves/workmates'] = 'leaves/workmates';
$route['leaves/department'] = 'leaves/department';
$route['leaves/organization/(:num)'] = 'leaves/organization/$1';
$route['leaves/collaborators'] = 'leaves/collaborators';
$route['leaves/team'] = 'leaves/team';

//public Fullcalendar feeds (available when public calendars are activated)
$route['leaves/public/organization/(:num)'] = 'calendar/public_organization/$1';
$route['contracts/public/calendar/alldayoffs'] = 'calendar/public_dayoffs';

//My leave requests
$route['leaves/counters'] = 'leaves/counters';
$route['leaves/counters/(:num)'] = 'leaves/counters/$1';
$route['leaves/export'] = 'leaves/export';
$route['leaves/create'] = 'leaves/create';
$route['leaves/edit/(:num)'] = 'leaves/edit/$1';
$route['leaves/update'] = 'leaves/update';
$route['leaves/delete/(:num)'] = 'leaves/delete/$1';
$route['leaves/(:num)'] = 'leaves/view/$1';
$route['leaves/validate'] = 'leaves/validate';
$route['leaves'] = 'leaves';

//leave requests (submitted to the line manager)
$route['requests/collaborators'] = 'requests/collaborators';
$route['requests/createleave/(:num)'] = 'requests/createleave/$1';
$route['requests/counters/(:num)'] = 'requests/counters/$1';
$route['requests/counters/(:num)/(:num)'] = 'requests/counters/$1/$2';
$route['requests/export/(:any)'] = 'requests/export/$1';
$route['requests/accept/(:num)'] = 'requests/accept/$1';
$route['requests/reject/(:num)'] = 'requests/reject/$1';
$route['requests/delegations/(:num)'] = 'requests/delegations/$1';
$route['requests/delegations'] = 'requests/delegations';
$route['requests/ajax/delegations/delete'] = 'requests/delegations_delete';
$route['requests/ajax/delegations/add'] = 'requests/delegations_add';
$route['requests/(:any)'] = 'requests/index/$1';
$route['requests'] = 'requests/index/requested';

//overtime requests
$route['extra/export'] = 'extra/export';
$route['extra/create'] = 'extra/create';
$route['extra/edit/(:num)'] = 'extra/edit/$1';
$route['extra/view/(:num)'] = 'extra/view/$1';
$route['extra/delete/(:num)'] = 'extra/delete/$1';
$route['extra/(:num)'] = 'extra/view/$1';
$route['extra'] = 'extra';

//overtime validation
$route['overtime/export/(:any)'] = 'overtime/export/$1';
$route['overtime/accept/(:num)'] = 'overtime/accept/$1';
$route['overtime/reject/(:num)'] = 'overtime/reject/$1';
$route['overtime/(:any)'] = 'overtime/index/$1';
$route['overtime'] = 'overtime/index/requested';

//Entitled days
$route['entitleddays/user/(:num)'] = 'entitleddays/user/$1';
$route['entitleddays/ajax/user'] = 'entitleddays/ajax_user';
$route['entitleddays/userdelete/(:num)'] = 'entitleddays/userdelete/$1';
$route['entitleddays/contract/(:num)'] = 'entitleddays/contract/$1';
$route['entitleddays/ajax/contract'] = 'entitleddays/ajax_contract';
$route['entitleddays/contractdelete/(:num)'] = 'entitleddays/contractdelete/$1';
$route['entitleddays/ajax/update'] = 'entitleddays/ajax_update';

//_______________________________________________
//Reports
$route['reports/balance'] = 'reports/balance';
$route['reports/balance/execute'] = 'reports/balance_execute';
$route['reports/balance/export'] = 'reports/balance_export';
$route['reports'] = 'reports/balance';

//_______________________________________________
//REST API
$route['api/token'] = 'api/token';
$route['api/contracts/(:num)'] = 'api/contracts/$1';
$route['api/contracts'] = 'api/contracts';
$route['api/entitleddayscontract/(:num)'] = 'api/entitleddayscontract/$1';
$route['api/addentitleddayscontract/(:num)'] = 'api/addentitleddayscontract/$1';
$route['api/entitleddaysemployee/(:num)'] = 'api/entitleddaysemployee/$1';
$route['api/addentitleddaysemployee/(:num)'] = 'api/addentitleddaysemployee/$1';
$route['api/leavessummary/(:num)/(:num)'] = 'api/leavessummary/$1/$2';
$route['api/leavessummary/(:num)'] = 'api/leavessummary/$1';
$route['api/leaves/(:num)/(:num)'] = 'api/leaves/$1/$2';
$route['api/leavetypes'] = 'api/leavetypes';
$route['api/positions'] = 'api/positions';
$route['api/userdepartment/(:num)'] = 'api/userdepartment/$1';
$route['api/userextras/(:num)'] = 'api/userextras/$1';
$route['api/userleaves/(:num)'] = 'api/userleaves/$1';
$route['api/users/(:num)'] = 'api/users/$1';
$route['api/users'] = 'api/users';
//v0.4.0
$route['api/monthlypresence/(:num)/(:num)/(:num)'] = 'api/monthlypresence/$1/$2/$3';
$route['api/deleteuser/(:num)'] = 'api/deleteuser/$1';
$route['api/updateuser/(:num)'] = 'api/updateuser/$1';
$route['api/createuser/(:any)'] = 'api/createuser/$1';
$route['api/createuser'] = 'api/createuser';
$route['api/createleave'] = 'api/createleave';

//ICS Feeds
$route['ics/individual/(:num)'] = 'ics/individual/$1';
$route['ics/dayoffs/(:num)/(:num)'] = 'ics/dayoffs/$1/$2';
$route['ics/entity/(:num)/(:num)/(:any)'] = 'ics/entity/$1/$2/$3';
$route['ics/ical/(:num)'] = 'ics/ical/$1';

//Session management
$route['session/login'] = 'session/login';
$route['session/logout'] = 'session/logout';
$route['session/language'] = 'session/language';
$route['session/forgetpassword'] = 'session/forgetpassword';

$route['default_controller'] = 'leaves';
$route['(:any)'] = 'pages/view/$1';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
