LMS is a Leave Management System developed in PHP/MySQL under a GPL v3 licence.
LMS is designed to provide simple leave and overtime request workflows for small organizations.

## Informations / getting help

* Official website : http://www.leave-management-system.org/
* Google group : https://groups.google.com/forum/?hl=en#!forum/lms-users
* G+ : https://plus.google.com/+LeavemanagementsystemOrganization/posts
* FB : https://www.facebook.com/lmsoss
* Tweeter : https://twitter.com/LeaveMS

## Status

This project is stable and ready for production. An online demo is available here : http://demo.leave-management-system.org/
Use bbalet / bbalet for login / password. Be nice and don't screw up the data of the demo.

## Features

* Comprehensive online documentation.
* User management.
* Notifications by e-mail (requested, accepted, rejected and new user).
* Leave request approval workflow.
* Overtime request approval workflow.
* Leave balance report (filtered by department).
* Export to Excel in a click.
* HR users can edit any leave or overtime request.
* Set your own contracts and leave types.
* Calendars of leaves (individual, team, collaborators, etc.).
* Describe your organization in a tree structure and attach employees to entities.
* Available in English, French and Khmer (localization is in progress).

## Installation

[See the installation instructions](docs/install/README.md) for advanced configuration. In a nutshell :
* Download or clone LMS.
* Upload the content of this folder on your server (in <code>/var/www/...</code>).
* Create a database with <code>/sql/lms.sql</code> script.
* Create a user with SELECT, INSERT, UPDATE, DELETE, EXECUTE permissions on the database.
* Update <code>/application/config/database.php</code> according to your database settings.
* Update the end of <code>/application/config/config.php</code> with your e-mails settings.
* It is recommended to change the private and public RSA keys (in <code>assets/keys</code>).
* Non working days (weekends and day offs) can be defined on a contract so as to automatically calculate the duration of a leave and to display them in the calendar.

## What's next ?

* More HR management features (time, tasks, employee record, etc.).
* History of changes
* Purge data.
* Mass importer of users.
* Setup assistant.

## Contribute

* Suggest ideas, declare bugs with Github's issue tracking system.
* Translate the software in your language.
* Code enhancement requests declared in Github's issue tracking system.

## Credits

### Contributors

* NGO Passerelles numériques, our first user http://passerellesnumeriques.org/en/
* Students of Passerelles numériques for the khmer translation.

### Third party libraries and components

We thank the following open source projects for the components used by LMS:

#### Backend

* CodeIgniter MVC framework http://ellislab.com/codeigniter
* BCRYPT password hasher https://github.com/dwightwatson/codeigniter-bcrypt
* RSA Encryption in pure PHP https://github.com/phpseclib/phpseclib
* Excel import/export https://github.com/PHPOffice/PHPExcel
* TCPDF http://www.tcpdf.org/

#### Frontend

* bootstrap 2.3
* JQuery 1.x and JQuery-UI
* Calendar http://arshaw.com/fullcalendar/
* Datatable https://datatables.net/
* RSA implementation https://github.com/travist/jsencrypt
* Moment (JS dates lib) http://momentjs.com/

Favicon was created by Dakirby309 - http://dakirby309.deviantart.com / License: Creative Commons (Attribution 3.0 Unported)
