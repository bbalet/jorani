Leave Management System developed in PHP/MySQL under an AGPL v3 licence.
Designed to provide simple leave and overtime request workflows for small organizations.

## Features

* Notifications by e-mail (requested, accepted, rejected and new user).
* Leave request approval workflow (1 validator).
* Overtime request approval workflow (1 validator).
* Leave balance report (filtered by department).
* Monthly presence report.
* Export to XLSX (Excel, LibreOffice) in a click (almost all pages of the application).
* HR users can edit any leave or overtime request.
* Set your own contracts and leave types.
* Calendars of leaves (individual, team, collaborators, etc.).
* Describe your organization in a tree structure and attach employees to entities, define a supervisor per entity.
* Non working days (weekends and day offs) can be defined on a contract so as to automatically calculate the duration of a leave and to display them in the calendar.
* REST API (OAuth2) fully documented and examples with PHP clients.
* LDAP Authentication (OpenLDAP, AD, etc.).
* OAuth2 Authentication (only Google+ at the moment).
* Multilingual capable

## __Installation__

[See the installation instructions](docs/install/README.md) for advanced configuration. 

## In a nutshell:
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

## Contribute

* [Read the TODO list](TODO.md) if you want to know what are the priorities.

## Credits

### Third party libraries and components

Thank to the following open source projects for the components.

#### Backend

* CodeIgniter MVC framework http://www.codeigniter.com/
* RSA Encryption in pure PHP https://github.com/phpseclib/phpseclib
* Excel import/export https://github.com/PHPOffice/PHPExcel
* OAuth2 Server https://github.com/bshaffer/oauth2-server-php
* OAuth2 Client https://github.com/thephpleague/oauth2-client
* OAuth2 Google Provider https://github.com/thephpleague/oauth2-google
* Sabre/VObject https://github.com/fruux/sabre-vobject
* PHPMailer https://github.com/PHPMailer/PHPMailer
* PHPMailer CI wrapper https://github.com/ivantcholakov/codeigniter-phpmailer

#### Frontend

* bootstrap 2.3, bootbox and Font Awesome
* JQuery 1.x and JQuery-UI
* FullCalendar http://arshaw.com/fullcalendar/
* Datatable https://datatables.net/
* RSA implementation https://github.com/travist/jsencrypt
* Moment (JS dates library) http://momentjs.com/
* Selectize https://github.com/brianreavis/selectize.js
* Zero Clipboard https://github.com/zeroclipboard/zeroclipboard
* Google noto fonts https://www.google.com/get/noto/
