If you want to contribute to the development of Jorani, here is a list of things to be implemented.
I tried to sort them out by priority and to explicitly explain what is out of scope.

## v0.4.4

- [X] Support half-days in Fullcalendar widget.
- [X] USABILITY: Don't display 404 for broken links but a full page instead.
- [X] OAuth2 with Google+ as 3rd party authentifier.
- [X] Add Ukrainian language.
- [X] Prevent the creation of a leave request with a duration of 0.
Bugs:
- [X] Broken link in Leave request e-mail.
- [X] ICS Feed is not working in global calendar (partly due to missing timezone var in PHP.ini).
Performance:
- [X] Optimize autoloading (see composer for VObjects).

## v0.5.0 or later

Following naming convention, this version will need a DB patch.

- [ ] Fix wrong charset problem on table dayoffs.
- [ ] More flexibility with date format (see forum).
- [ ] Report carried-over leaves wizard : select an entity (change date), opt-in/out employees, check suggested report and go.
- [ ] Mass apply entitled days to a group of employees. (organisation) - (employees in entity) <-> select.
- [ ] Complete PHP triggers (add fruux lib to put CalDAV -> no : incomplete library).
- [ ] Simplify Overtime entitlments.
Maybe:
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
