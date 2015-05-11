If you want to contribute to the development of Jorani, here is a list of things to be implemented.
I tried to sort them out by priority and to explicitly explain what is out of scope.

## v0.5.0 or later

- [ ] Copy contract/calendar (definition of non-working days for a yearly period).
- [ ] Better entitled days editor (contract / employee).
- [ ] Report carried-over leaves (Semi-auto / wizard), employee by employee (or mass ?).
- [ ] Notification by e-mail : Request deleted / modified.
- [ ] Delete a past leave request (Request + Wkf ?), or...
- [ ] ... send an e-mail (send to line manager, option to CC the HR officers). Contextual to a leave/overtime request.
- [ ] Provide an example of a REST client (seniority leaves, carry over...) in PHP (cron) or go (service).

## Not a priority

- [ ] Better LDAP integration.
- [ ] Implement a kind of heritage in HR/organization for supervisors (child entities).
- [ ] Inactive employees (today we can put them into an archive entity in HR/organization so as to exclude them from reports).
- [ ] HR officers of a part of the organization (defined in HR/organization), for future functions when they'll be CCed.
- [ ] Add a field explaining why a request is rejected. Validation E-mail will redirect to a form is this option is activated ?
- [ ] Anticipated leaves (entitled days of next yearly period). Today we can deal with it manually because LMS doesn't forbid to 
request a leave even if your credit is negative and because leave balance reports can be executed with a past/future date.
- [ ] Setup assistant.
- [ ] Mass importer of users (from CSV, LDAP ?).

## Post v1.0

- [ ] Simplified time tracking.

## Will not be implemented

- [ ] Requests are validated by two users (opens the door to a wkf engine, too complicated for targeted users).
- [ ] PDF Export (no serious OSS PDF lib dealing correctly with Unicode / Modern browsers can print as PDF).
- [ ] Migration to CI 3.0.0 (we don't rely too much on the library, so it has no interrest and it is slower than v2).
