If you want to contribute to the development of Jorani, here is a list of things to be implemented.
I tried to sort them out by priority and to explicitly explain what is out of scope.

## v0.4.1

- [X] Copy contract/calendar (definition of non-working days for a civil year).
- [X] A manager can create a leave request in behalf of a collaborator.
- [X] Check Ajax return (e.g. calendar views) to redirect to login in case of session expiration.
- [X] Inactive employees (put them into an archive entity in HR/organization so as to exclude them from reports).
- [X] Experimental : import day offs from an ICS/CalDAV feed.
- [X] Public Global and Tabular calendar in an embeddable view.
- [X] Add an option to switch the duration field in readonly.
- [X] Allow user to delete/edit rejected leave requests (take into account leave_status_requested).
- [X] Bug when editing a LR from HR/Employee => e-mail is messy (reload the LR from DB in leaves:sendMail)
- [X] Report duration and balance into request e-mail.
- [X] Entitled days editor : possibility to copy a line.
- [X] Entitled days editor : a field allows to set the step for inc/dec (saved in a cookie).
- [X] Sum of leave grouped by type in presence report.
- [X] Yearly individual calendar.
- [ ] Display entity name in list of HR/Employees (maybe or report to 0.5.0).
- [ ] Update jorani.pot in transifex and warn translators/update strings.
- [ ] Check HR/Monthly report for overlapping. Report this overlapping feature to calendar/tabular.
- [X] Fix Deprecation warning: moment.lang is deprecated. Use moment.locale instead (technical migration of FullCalendar).

## v0.5.0 or later

Following naming convention, this version will need a DB patch.

- [ ] DB optimization on leave table.
- [ ] Better entitled days editor (contract / employee).
- [ ] Possibility to sort the leave types (optionally attached to contract ?). NEEDS DB PATCH (maybe).
- [ ] Report carried-over leaves (Semi-auto / wizard), employee by employee (or mass ?).
- [ ] Notification by e-mail : Request deleted / modified.
- [ ] Provide an example of a REST client (seniority leaves, carry over, LDAP sync...) in PHP (cron) or go (service).

## Not a priority

- [ ] Better LDAP integration.
- [ ] Investigation for a CAS/SSO integration.
- [ ] Mobile-optimized views (limited to few pages : simple user and validation).
- [ ] Implement a kind of heritage in HR/organization for supervisors (child entities).
- [ ] HR officers of a part of the organization (defined in HR/organization), for future functions when they'll be CCed.
- [ ] Add a field explaining why a request is rejected. Validation E-mail will redirect to a form is this option is activated ?
- [ ] Anticipated leaves (entitled days of next yearly period). Today we can deal with it manually because LMS doesn't forbid to 
request a leave even if your credit is negative and because leave balance reports can be executed with a past/future date.
- [ ] Setup assistant (with links to requirements, testldap and testmail scripts).
- [ ] Mass importer of users (from CSV, LDAP ?). From GUI or a side tool using the REST API ?
- [ ] Multitenancy (add global filter / Additionnal field on all tables). Allow access to the same instance for multiple tenants.
- [ ] Alternative overtime module. It will be separated from the LMS, accepted extra time will not be added as a compensate entitled days but compensation will have its own wkf.

## Post v1.0

- [ ] Simplified time tracking. Not sure because Jorani is specialized in LMS. Maybe a side project ?

## Might not be implemented

- [ ] Requests are validated by two users (opens the door to a wkf engine, too complicated for targeted users).
- [ ] PDF Export (no serious OSS PDF lib dealing correctly with Unicode / Modern browsers can print as PDF).
- [ ] Migration to CI 3.0.0 (we don't rely too much on the library, so it has no interrest and it is slower than v2).
