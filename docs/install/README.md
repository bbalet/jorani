# Installation

LMS has been tested with Apache and ngnix.
LMS is compatible with HHVM as Codeigniter is 100% compatible with the VM. 
However, please note that HHVM is still under development.

## General considerations

The LMS application must have write privileges on <code>temp</code> and <code>application/logs</code> folders.

## Database setup

LMS has been tested with MySQL and MariaDB (please note that functions are created in the schema). Please follow these steps :
* Create a database.
* Import <code>/sql/lms.sql</code>.
* Change <code>application/config/database.php</code> according to your environment.
* Change <code>application/config/config.php</code> according to your environment.

Please note that the schema contains procedures, so the user created must have EXECUTE permission.
You might need to change the code <code>CREATE DEFINER=`root`@`localhost`</code> of the <code>/sql/lms.sql</code> file if the db user you are using to create the schema is not the root user (e.g. on shared hosting).

## E-mail setup

LMS uses e-mail to notify users and managers. In order to setup e-mail modify the end of 
<code>application/config/config.php</code> file according to your environment.

## Apache

LMS is a PHP/CI application using rewrite rules and .htaccess files. 
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

## Error upstream sent too big header

If you get this error : <code>upstream sent too big header while reading response header from upstream</code>, you need to enlarge the buffers used by nginx.

Add this to your http {} of the nginx.conf file normally located at /etc/nginx/nginx.conf:

proxy_buffer_size   128k;
proxy_buffers   4 256k;
proxy_busy_buffers_size   256k;

Then add this to your php location block, this will be located in your vhost file look for the block that begins with location ~ .php$ {

fastcgi_buffer_size 128k;
fastcgi_buffers 4 256k;
fastcgi_busy_buffers_size 256k;

If you are running nginx, tune your configuration (see <code>/etc/nginx/nginx.conf</code>).

## Tested environments

* Raspbian ARM - Apache + PHP
* Raspbian ARM - ngnix + php-fpm
* Windows 7 / 64 - WAMP
* Windows XP / 32 - WAMP
* Centos - Apache + PHP
* Ubuntu 13.10 x86_64 - ngnix + php-fpm or HHVM
