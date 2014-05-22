LMS is a simple Leave Management System developed in PHP/MySQL under a GPL v3 licence.
LMS is designed to provide a simple leave and overtime request workflow for small organizations.

## Status

This project is under development. An online demo is available here : http://demo.leave-management-system.org/
Use bbalet / bbalet for login / password. Be nice and don't screw up the data of the demo.

## Features

* User management.
* Notifications by e-mail (requested, accepted, rejected and new user).
* Leave request approval workflow.
* Overtime request approval workflow.
* Export to Excel in a click.
* HR users can edit any leave or overtime request.
* Set your own contracts and leave types.
* Calendars of leaves (individual, team, collaborators, etc.).
* Describe your organization in a tree structure and attach employees to entities.

## Installation

See <code>/docs/install/README.md</code> for advanced configuration. In a nutshell :
* Download or clone LMS.
* Upload the content of this folder on your server (in <code>/var/www/...</code>).
* Create a database with <code>/sql/lms.sql</code> script.
* Update <code>/application/config/database.php</code> according to your database settings.
* Update the end of <code>/application/config/config.php</code> with your e-mails settings.

## Credits

### Contributors

* NGO Passerelles numériques, our first user http://passerellesnumeriques.org/en/

### Third party libraries and components

We thank the following open source projects for the components used by LMS:

#### Backend

* CodeIgniter MVC framework http://ellislab.com/codeigniter
* BCRYPT password hasher https://github.com/dwightwatson/codeigniter-bcrypt
* RSA Encryption in pure PHP https://github.com/phpseclib/phpseclib
* Excel import/export https://github.com/PHPOffice/PHPExcel

#### Frontend

* bootstrap 2.3
* JQuery 1.x
* Datepicker https://github.com/eternicode/bootstrap-datepicker
* Calendar http://arshaw.com/fullcalendar/
* Datatable https://datatables.net/
* RSA implementation https://github.com/travist/jsencrypt
* Form validation http://reactiveraven.github.io/jqBootstrapValidation/

Favicon was created by Dakirby309 - http://dakirby309.deviantart.com / License: Creative Commons (Attribution 3.0 Unported)
