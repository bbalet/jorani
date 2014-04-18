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
$route['users/export'] = 'users/export';
$route['users/import'] = 'users/import';
$route['users/reset/(:any)'] = 'users/reset/$1';
$route['users/create'] = 'users/create';
$route['users/edit/(:any)'] = 'users/edit/$1';
$route['users/update'] = 'users/update';
$route['users/delete/(:any)'] = 'users/delete/$1';
$route['users/(:any)'] = 'users/view/$1';
$route['users'] = 'users';

//Admin : View and change settings
$route['settings'] = 'settings/set';

//Human Resources Management
$route['hr/index'] = 'hr/index';
$route['hr/employees'] = 'hr/employees';
$route['hr/contract/(:any)'] = 'hr/contract/$1';
$route['hr/manager/(:any)'] = 'hr/manager/$1';
$route['hr/entitleddays/(:any)'] = 'hr/entitleddays/$1';
$route['hr'] = 'hr';

$route['leavetypes/delete/(:any)'] = 'leavetypes/delete/$1';
$route['leavetypes/edit/(:any)'] = 'leavetypes/edit/$1';
$route['leavetypes/index'] = 'leavetypes/index';
$route['leavetypes/create'] = 'leavetypes/create';
$route['leavetypes/export'] = 'leavetypes/export';
$route['leavetypes'] = 'leavetypes';

$route['contracts/export'] = 'contracts/export';
$route['contracts/create'] = 'contracts/create';
$route['contracts/edit/(:any)'] = 'contracts/edit/$1';
$route['contracts/update'] = 'contracts/update';
$route['contracts/delete/(:any)'] = 'contracts/delete/$1';
$route['contracts/(:any)'] = 'contracts/view/$1';
$route['contracts'] = 'contracts';

//Team leave requests (manager->team UNION team->manager)
$route['calendar/individual'] = 'calendar/individual';
$route['calendar/workmates'] = 'calendar/workmates';
$route['calendar/collaborators'] = 'calendar/collaborators';
$route['leaves/individual'] = 'leaves/individual';
$route['leaves/collaborators'] = 'leaves/collaborators';
$route['leaves/team'] = 'leaves/team';
$route['leaves/ical/(:any)'] = 'leaves/ical/$1';

//My leave requests
$route['leaves/counters'] = 'leaves/counters';
$route['leaves/export'] = 'leaves/export';
$route['leaves/create'] = 'leaves/create';
$route['leaves/credit'] = 'leaves/credit';
$route['leaves/edit/(:any)'] = 'leaves/edit/$1';
$route['leaves/update'] = 'leaves/update';
$route['leaves/delete/(:any)'] = 'leaves/delete/$1';
$route['leaves/(:any)'] = 'leaves/view/$1';
$route['leaves'] = 'leaves';

//leave requests
$route['requests/export/(:any)'] = 'requests/export/$1';
$route['requests/accept/(:any)'] = 'requests/accept/$1';
$route['requests/reject/(:any)'] = 'requests/reject/$1';
$route['requests/(:any)'] = 'requests/index/$1';
$route['requests'] = 'requests/index/requested';

$route['entitleddays/user/(:any)'] = 'entitleddays/user/$1';
$route['entitleddays/userdelete/(:any)'] = 'entitleddays/userdelete/$1';
$route['entitleddays/contract/(:any)'] = 'entitleddays/contract/$1';
$route['entitleddays/contractdelete/(:any)'] = 'entitleddays/contractdelete/$1';

//Reports
$route['reports/(:any)/(:any)'] = 'reports/execute/$1/$2';
$route['reports'] = 'reports';

//Session management
$route['session/login'] = 'session/login';
$route['session/logout'] = 'session/logout';

$route['default_controller'] = 'leaves';
$route['(:any)'] = 'pages/view/$1';


/* End of file routes.php */
/* Location: ./application/config/routes.php */