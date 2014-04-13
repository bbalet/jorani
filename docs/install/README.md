# Installation

LMS has been tested with Apache and ngnix.
LMS should be compatible with HHVM as Codeigniter is 100% compatible with the VM. 
However, HHVM is still under development and LMS has not been fully tested with this VM.

## General considerations

The LMS application must have write privileges on <code>temp</code> and <code>application/logs</code> folders.

## Database setup

LMS has been tested with MySQL. Please follow these steps :
* Create a database.
* Import <code>/sql/lms.sql</code>.
* Change <code>application/config/database.php</code> according to your environment.

# E-mail setup

LMS uses e-mail to notify users and managers. TEMPORARY (under development), modify :
<code>application/models/settings_model.php</code>

## Apache

LMS is a classic PHP/CI application using rewrite rules and .htaccess files. 
So your Apache configuration must allow overwriting configuration by .htaccess files and mod_rewrite must be enabled.

## nginx

For your convinience, a sample ngnix configuration file is provided in this folder
<code>/docs/install/ngnix/default</code>

If you are using HTTP protocol, don't forget to disable HTTPS in application/config/config.php
<code>$_SERVER['HTTPS'] = 'off';</code>

## Tested environments

* Raspbian ARM - Apache + PHP
* Raspbian ARM - ngnix + php-fpm
* Windows 7 / 64 - WAMP
* Windows XP / 32 - WAMP
* Centos - Apache + PHP
