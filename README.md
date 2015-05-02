Jorani is a Leave Management System developed in PHP/MySQL under a GPL v3 licence.
Jorani is designed to provide simple leave and overtime request workflows for small organizations.

![Jorani Logo](https://raw.githubusercontent.com/bbalet/jorani/master/assets/images/Jorani_Logo_horizontal-700.png)

## Informations / getting help

* Official website : http://jorani.org/
* Google group : https://groups.google.com/forum/?hl=en#!forum/jorani
* G+ : https://plus.google.com/+LeavemanagementsystemOrganization/posts
* FB : https://www.facebook.com/lmsoss
* Tweeter : https://twitter.com/LeaveMS

## Status

This project is stable and ready for production. An online demo is available here : http://demo.jorani.org/
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
* Non working days (weekends and day offs) can be defined on a contract so as to automatically calculate the duration of a leave and to display them in the calendar.
* LDAP Authentication.
* Available in English, French, Spanish and Dutch.

## Installation

[See the installation instructions](docs/install/README.md) for advanced configuration. In a nutshell :
* If you use Apache, **mod_rewrite must be activated and the config must allow overwriting settings with .htaccess file**.
* Download or clone Jorani.
* Upload the content of this folder on your server (in <code>/var/www/...</code>).
* Create a database with <code>/sql/lms.sql</code> script.
* Create a user with SELECT, INSERT, UPDATE, DELETE, EXECUTE permissions on the database.
* Update <code>/application/config/database.php</code> according to your database settings.
* Update the end of <code>/application/config/email.php</code> with your e-mails settings.
* Update the end of <code>/application/config/config.php</code> if you want to change the default behaviour.
* It is recommended to change the private and public RSA keys (in <code>assets/keys</code>).
* Check your installation with the <code>requirements.php</code> page at the root of your installation (e.g. http://localhost/lms/requirements.php).
* The default user is *bbalet* and password is *bbalet*.

## What's next ?

* More HR management features (time, tasks, employee record, etc.).
* History of changes
* Purge data.
* Mass importer of users.
* Setup assistant.

## Contribute

* Suggest ideas, declare bugs with Github's issue tracking system or Google group.
* Help us to translate the software in your language https://www.transifex.com/projects/p/jorani

## Credits

### Contributors

* NGO Passerelles numériques, our first user http://passerellesnumeriques.org/en/
* Github and Google groupe users for their ideas and tests.
* Hector NESTAR : Spanish Translation
* Roger WOLFS : Dutch Translation
* Christian SONNENBERG : German Translation

### Third party libraries and components

We thank the following open source projects for the components used by Jorani:

#### Backend

* CodeIgniter MVC framework http://www.codeigniter.com/
* BCRYPT password hasher https://github.com/dwightwatson/codeigniter-bcrypt
* RSA Encryption in pure PHP https://github.com/phpseclib/phpseclib
* Excel import/export https://github.com/PHPOffice/PHPExcel
* Oauth2 Server https://github.com/bshaffer/oauth2-server-php

#### Frontend

* bootstrap 2.3
* JQuery 1.x and JQuery-UI
* FullCalendar http://arshaw.com/fullcalendar/
* Datatable https://datatables.net/
* RSA implementation https://github.com/travist/jsencrypt
* Moment (JS dates lib) http://momentjs.com/
* Selectize https://github.com/brianreavis/selectize.js
* Zero Clipboard https://github.com/zeroclipboard/zeroclipboard
