<?php defined('BASEPATH') OR exit('No direct script access allowed.');

$config['protocol'] = 'smtp';
$config['useragent'] = 'phpmailer';
//$config['mailpath'] = '/usr/sbin/sendmail';
$config['smtp_host'] = 'localhost';
$config['smtp_user'] = '';
$config['smtp_pass'] = '';
$config['_smtp_auth'] = TRUE;
$config['smtp_port'] = '25';
$config['smtp_timeout'] = '20';
$config['smtp_crypto'] = '';                       // '' or 'tls' or 'ssl'
$config['charset'] = 'UTF-8';
$config['validate'] = true;
$config['mailtype'] = 'html';
$config['wordwrap'] = FALSE;
$config['wrapchars'] = 70;
$config['validate'] = FALSE;
$config['priority'] = 3;                                // 1, 2, 3, 4, 5
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";
$config['bcc_batch_mode'] = false;
$config['bcc_batch_size'] = 200;
