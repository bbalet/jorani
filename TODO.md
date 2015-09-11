If you want to contribute to the development of Jorani, here is a list of things to be implemented.
I tried to sort them out by priority and to explicitly explain what is out of scope.

## v0.4.2

- [ ] Fix issue in tabular view.
- [ ] Update load test and its dataset.
- [ ] Update install doc (feedback with e-mail problems, Opcache, TLS, etc.).
- [ ] Prevent double-click in validation/leaves.
- [ ] Better entitled days editor (contract / employee).
- [ ] Experiment a simple way to display abbreviation of leave types in tabular calendar (without a new DB field).
- [ ] Provide an example of a REST client (seniority leaves, carry over, LDAP sync...) in PHP (cron) or go (service).

## v0.5.0 or later

Following naming convention, this version will need a DB patch.

- [ ] DB optimization on leave table.
- [ ] Possibility to sort the leave types (optionally attached to contract ?). NEEDS DB PATCH (maybe).
- [ ] Report carried-over leaves (Semi-auto / wizard), employee by employee (or mass ?).
- [ ] Notification by e-mail : Request deleted / modified.

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
