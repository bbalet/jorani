LMS is a simple Leave Management System developed in PHP/MySQL under a GPL v3 licence.
LMS is designed to provide a simple leave and overtime request workflow for small organizations.

## Status

This project is under development.

## Features
* User management.
* Notifications by e-mail (request, accept, reject and new user).
* Leave request approval workflow.
* Overtime request approval workflow.
* Export to Excel in a click.
* HR role allows to edit any leave or overtime request.
* Set your own contracts and leave types.
* Fancy calendars (individual, team, collaborators, etc.).
* Describe your organization in a tree structure and attach employees to entities.

## Installation

See <code>/docs/install/README.md</code> for advanced configuration, in a nutshell :
* Download or clone.
* Upload the folder on your server.
* Create a database with <code>/sql/lms.sql</code> script.
* Update <code>/application/config/config.php</code> and <code>/application/config/database.php</code>.

## Credits

### Contributors

* NGO Passerelles numériques, our first user  http://passerellesnumeriques.org/en/

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
