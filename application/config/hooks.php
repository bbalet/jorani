<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_controller'] = array(
    'class' => '',
    'function' => 'start',
    'filename' => 'CodeCoverage.php',
    'filepath' => 'hooks'
);

$hook['post_system'] = array(
    'class' => '',
    'function' => 'stop',
    'filename' => 'CodeCoverage.php',
    'filepath' => 'hooks'
);
