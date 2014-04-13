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

If you are using HTTP protocol, don't forget to disable HTTPS in <code>application/config/config.php</code>
<code>$_SERVER['HTTPS'] = 'off';</code>

Other parameters should be considered carefully (in <code>/etc/nginx/nginx.conf</code>), such as:
* events.worker_connections
* events.multi_accept
* http.keepalive_timeout
The values depend on your environment and the load that you are expecting.

IMPORTANT : nginx in conjunction with fpm-php is for advanced users and is more complicated to tune than Apache (see troubleshooting section of this document).
We recommend to validate your production environement with a load test prior going live.

# Troubleshooting

## A load test raises error at a given number of simultaneous user

If you are running php-fpm, examine <code>/var/log/php5-fpm.log</code>, if you see this message :
<code>WARNING: [pool www] server reached pm.max_children setting (5), consider raising it</code>
In <code>/etc/php5/fpm/pool.d/www.conf</code> which is set by default to 5
pm.max_children = xxxx (number of simultaneous processes)
For a load test, allow a margin of 25%

If you are running nginx, tune your configuration (see <code>/etc/nginx/nginx.conf</code>).

## Tested environments

* Raspbian ARM - Apache + PHP
* Raspbian ARM - ngnix + php-fpm
* Windows 7 / 64 - WAMP
* Windows XP / 32 - WAMP
* Centos - Apache + PHP
