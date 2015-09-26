If you want to contribute to the development of Jorani, here is a list of things to be implemented.
I tried to sort them out by priority and to explicitly explain what is out of scope.

## v0.4.3

- [X] Fix regression issue in accessing the detail of a leave/overtime request when connected as manager.
- [X] Get rid of status_model->get_label and similar calls ($this->types_model->get_types()).
- [X] New diagnostic page for OpenSSL. Allow to easily create a pair of public/private RSA keys.
- [X] Possibility to overwrite any page. Custom reports called from local, etc.
- [X] PHP and JS triggers for leave/extra creation and modification. Called from local.
- [ ] Clean the code we've created to experiment custom reports or list all custom pages for current lang ? INI file was a good idea.
- [ ] Get rid of $_GET['source'] for redirection. Not a risk for XSS attack but not clean (see 'hr/presence/([^/]+)/(:num)')....
- [ ] Optimize overtime/leave request edit view the same as we did for view (for self view, no need to display name).
- [ ] Merge HR/Counters with Validation/counters, this will reduce the basecode.
- [ ] Improve code quality, adopt PSR1 for function names.
- [ ] Duplicate code for tabular/hr presence and Excel for linear calendar. Can we do something?
- [ ] Report carried-over leaves wizard : select an entity (change date), opt-in/out employees, check suggested report and go.

## v0.5.0 or later

Following naming convention, this version will need a DB patch.

- [ ] Update load test and its dataset.
- [ ] Add a table to log the execution of services using the API (eg cron tasks) + a sample page in local to display them.
- [ ] Possible DB optimization on leave table ALTER TABLE `leaves` ADD INDEX(`startdate`); ALTER TABLE `leaves` ADD INDEX(`enddate`);.
- [ ] Possibility to sort the leave types. NEEDS DB PATCH (maybe). Cookie or DB ? Entity scope ?
- [ ] Possibility to optionally exclude leave types to a contract. NEEDS DB PATCH (maybe). Impact leave request, should impact a dynamic build of leave balance report. 
- [ ] Notification by e-mail : Request deleted / modified (maybe or report v0.5.0). We should maybe have basic objects to pass...

## Ideas

- [ ] Provide examples of REST clients (seniority leaves, carry over, LDAP sync...) in PHP (cron) or go (service).
- [ ] PHP scripts of migration from OrangeHRM, PHP_Conges, LiberTempo, etc.

## Not a priority

- [ ] Supporting docs (upload attachments into local folder).
- [ ] Better LDAP integration.
- [ ] Investigation for a CAS/SSO integration.
- [ ] Mobile-optimized views (limited to few pages : simple user and validation).
- [ ] Implement a kind of heritage in HR/organization for supervisors (child entities).
- [ ] HR officers of a part of the organization (defined in HR/organization), for future functions when they'll be CCed. and/or ...
- [ ] ... Multitenancy (add global filter / Additionnal field on all tables). Allow access to the same instance for multiple tenants.
- [ ] Add a field explaining why a request is rejected. Validation E-mail will redirect to a form is this option is activated ?
- [ ] Anticipated leaves (entitled days of next yearly period). Today we can deal with it manually because LMS doesn't forbid to 
request a leave even if your credit is negative and because leave balance reports can be executed with a past/future date.
- [ ] Setup assistant (with links to requirements, testldap and testmail scripts).
- [ ] Mass importer of users (from CSV, LDAP ?). From GUI or a side tool using the REST API ?
- [ ] Alternative overtime module. It will be separated from the LMS, accepted extra time will not be added as a compensate entitled days but compensation will have its own wkf.

## Post v1.0

- [ ] Simplified time tracking. Not sure because Jorani is specialized in LMS. Maybe a side project ?

## Might not be implemented

- [ ] Requests are validated by two users (too complicated for targeted users).
- [ ] Different colors for each leave type. Requesters often don't know that you can get the list of leaves elsewhere.
- [ ] PDF Export (no serious OSS PDF lib dealing correctly with Unicode / Modern browsers can print as PDF).
- [ ] Migration to CI 3.0.0 (we don't rely too much on the library, so it has no interrest and it is slower than v2).
