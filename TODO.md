If you want to contribute to the development of Jorani, here is a list of things to be implemented.
I tried to sort them out by priority and to explicitly explain what is out of scope.

## v0.6.6 or later

- [ ] Maybe: Cannot force LR status from 2 to 1 for regular user.
- [ ] Forbid negative duration in leave requests (Option).
- [ ] Maybe: remove all references to JQueryUI (datepicker).
- [ ] Maybe: Migrate Fontawesome to material design icons set (in preparation of BS4).
- [ ] Technical update of FullCalendar (beware of LTR bug: https://github.com/fullcalendar/fullcalendar/issues/2516).
- [ ] ICS feeds should use a random hash when publicly exposed.
- [ ] Dynamic form for user properties (Jorani to become a SAML2 IdP). For superadmin only.
- [ ] Dynamic form for user parameters. For superadmin only.
- [ ] Dynamic form for parameters. For superadmin only.
- [ ] Get rid of JQuery UI (mostly used by calendars).
- [ ] Change the system for compensate balance. Give a regular entitlement (start/end dates depend on contract).
- [ ] Remove duplicated string defs (moved into global): users_create_popup_manager_button_ok
- [ ] i18n: Last 3 columns in Reports/Leaves.
- [ ] Maybe: Filter Excel export on visible rows (lot of security backend).
- [ ] Maybe: user/index Ajaxload.
- [ ] Maybe: Compute automatically: length of multiple leave requests.
- [ ] Maybe: series of leave requests (Employees / Multiple edit); take into account days off. and...
- [ ] Add SMTPSecure, SMTPAutoTLS, and SMTPAuth email params. See https://mail.google.com/mail/u/0/#inbox/15ae53ce27624515
- [ ] Fix missing closing DIV (Firefox).
- [ ] Implement OAuth scopes in API.
- [ ] Replace header() by set_header()
- [ ] Replace set_header("HTTP/1.1 422...) by set_status_header()
- [ ] Replace echo() by set_output()
- [ ] Report carried-over leaves wizard : select an entity (change date), opt-in/out employees, check suggested report and go.
- [ ] Mass apply entitled days to a group of employees. (organisation) - (employees in entity) <-> select.
- [ ] Complete PHP triggers (add fruux lib to put CalDAV -> no : incomplete library).
- [ ] Simplify Overtime entitlments.
- [ ] Possible DB optimization on leave table ALTER TABLE `leaves` ADD INDEX(`startdate`); ALTER TABLE `leaves` ADD INDEX(`enddate`);.
- [ ] Possibility to sort the leave types. NEEDS DB PATCH (maybe). Cookie or DB ? Entity scope ?
- [ ] Possibility to optionally exclude leave types to a contract. NEEDS DB PATCH (maybe). Impact leave request, should impact a dynamic build of leave balance report. What about type 0?
- [ ] Possibility to optionally sort the leave types (contract-level, types not excluded).
- [ ] Notification by e-mail : Request deleted / modified (maybe or report v0.5.0). We should maybe have basic objects to pass...
- [ ] Better LDAP integration with an explorer and better/simpler binding method.

## Maybe
- [ ] Add a table to log the execution of services using the API (eg cron tasks) + a sample page in local to display them.
- [ ] Maybe: edit/import/export settings from WebUI.
- [ ] Maybe: user/group permission.
- [ ] Maybe: configuration from DB.

## Ideas

- [ ] Export to Excel: export only the visible rows (POST with the list of Ids). Look into HR/Employees to see how to build the list.
- [ ] Provide examples of REST clients (seniority leaves, carry over, LDAP sync...) in PHP (cron) or go (service).
- [ ] PHP scripts of migration from OrangeHRM, PHP_Conges, LiberTempo, etc.
- [ ] More OAuth2 or SAML identity providers (Investigation for a CAS/SSO integration).
- [ ] Option/Config: kind of multi-tenancy filter on entity and its children the calendars (then calendar/global should be hidden).
- [ ] Color set/stripes for color blind people, eg. :

Code sample:

        /*Status for color-blind people*/
        .rejected-color-blind {
            /*background-color: #ff0000;*/
            background-image: linear-gradient(90deg, transparent 50%, rgba(255,255,255,.5) 50%);
            background-size: 10px 10px;
        }

## Not a priority

- [ ] Supporting docs (upload attachments into database).
- [ ] Implement a kind of heritage in HR/organization for supervisors (child entities). See PR, it looks to be done already
- [ ] HR officers of a part of the organization (defined in HR/organization), for future functions when they'll be CCed. and/or ...
- [ ] ... Multitenancy (add global filter / Additionnal field on all tables). Allow access to the same instance for multiple tenants.
- [ ] Add a field explaining why a request is rejected. Validation E-mail will redirect to a form is this option is activated ?
- [ ] Anticipated leaves (entitled days of next yearly period). Today we can deal with it manually because LMS doesn't forbid to
request a leave even if your credit is negative and because leave balance reports can be executed with a past/future date.
- [ ] Setup assistant (with links to requirements, testldap and testmail scripts).
- [ ] Mass importer of users (from CSV, LDAP ?). From GUI or a side tool using the REST API ?
- [ ] Alternative overtime module. It will be separated from the LMS, accepted extra time will not be added as a compensate entitled days but compensation will have its own wkf.

## Post v1.0

- [ ] Switch to package.json for JS dependencies
- [ ] Migrate to latest google analytics javascript (in templates/header.php)
- [ ] Upgrade JS libs (bootboxjs)
- [ ] Migrate to latest fullcalendar

## Might not be implemented

- [ ] Requests are validated by two users (multi level validation).
- [ ] Different colors for each leave type. Requesters often don't know that you can get the list of leaves elsewhere.
- [ ] PDF Export (no serious OSS PDF lib dealing correctly with Unicode / Modern browsers can print as PDF).

# Impacts

## Fullcalendar

Migration of fullcalendar requires the application of this patch https://github.com/fullcalendar/fullcalendar/issues/2516 while the issue is not solved.

## Jquery

We noticed an incompatibility between Jquery 2 and datatable that must be investigated prior any migration.
