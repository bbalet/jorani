<?php 
/**
 * This view is included into all desktop full views. It contains HTML and CSS definitions.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?><!DOCTYPE html>
<html lang="<?php echo $language_code;?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta charset="utf-8">
    <title><?php echo $title ?> - Jorani</title>
    <meta description="Jorani a free and open source leave management system. Workflow of approval; e-mail notifications; calendars; reports; export to Excel and more.">
    <link href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/font-awesome/css/font-awesome.min.css">
<?php CI_Controller::get_instance()->load->helper('language');
$this->lang->load('global', $language);?>
    <!--[if lte IE 8]>
    <script type="text/javascript">
    alert("<?php echo lang('global_msg_old_browser'); ?>");
    </script>
    <![endif]-->
    <!--[if lt IE 9]>
    <script src="<?php echo base_url();?>assets/js/html5shiv.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
    <link rel="icon" type="image/x-icon" href="<?php echo base_url();?>favicon.ico" sizes="32x32">
    <style>
        
<?php
$fonts = $this->config->item('fonts');
if (!is_null($fonts)) {
    if (array_key_exists($language_code, $fonts)) { ?>
    @font-face {
      font-family: 'Noto Sans Khmer';
      src: url('<?php echo base_url(), 'assets/fonts/', $fonts[$language_code]['asset'];?>') format('truetype');
    }
    body, button, input, select, .ui-datepicker, .selectize-input {
        font-family: 'Noto Sans Khmer' !important;
    }
<?php 
        }
    } ?>
    .anchor {
        color: #3097d1;
    }

    /*Sticky footer*/
    html, body {
      height: 100%;
    } 
    #push,
    #footer {
      height: 40px;
    }
    #footer {
        border-top: 1px #e4e4e4 solid;
        border-top-radius: 4px;
        box-shadow: 0 0 6px #ccc;
        padding: 10px;
        background-color: #fff;
    }
    #wrap {
      min-height: 100%;
      height: auto !important;
      height: 100%;
      margin: 0 auto -40px;
    }
    
    /*Background color of the navbar*/
    .navbar-inner {
      background-color: #3097d1;
      background-image: none;
      color: white;
    }

    .navbar-inverse .navbar-inner {
      background-color: #3097d1;
      background-image: none;
      color: white;
      border-color: #3097d1;
    }

    .navbar .nav > li > a {
      background-color: #3097d1;
      color: white;
    }

    .navbar .nav > li > a:focus,
    .navbar .nav > li > a:hover {
      background-color: #3097d1;
      color: white;
    }

    .navbar .nav > .active > a,
    .navbar .nav > .active > a:hover,
    .navbar .nav > .active > a:focus {
      background-color: white;
      color: #dddada;
    }

    .navbar-inverse .nav li.dropdown.open>.dropdown-toggle,
    .navbar-inverse .nav li.dropdown.active>.dropdown-toggle,
    .navbar-inverse .nav li.dropdown.open.active>.dropdown-toggle {
      background-color: #3097d1;
      color: #dddada;
    }

    .navbar .brand {
      background-color: #3097d1;
      color: white;
    }
    
    /*Button*/
    .btn {
      border-radius: 1;
      border-color: black;
    }
    
    .btn:focus, .btn:active:focus, .btn.active:focus {
        outline: 0 none;
    }
    
    .btn-primary {
        background: #3097d1;
        border-color: #3097d1;
        color: white;
    }

    .btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .open > .dropdown-toggle.btn-primary {
        background: #3097d1;
    }

    .btn-primary:active, .btn-primary.active {
        background: #3097d1;
        box-shadow: none;
    }
    
    .btn-danger {
        background: #bd362f;
        border-color: #bd362f;
        color: white;
    }

    .btn-danger:hover, .btn-danger:focus, .btn-danger:active, .btn-danger.active, .open > .dropdown-toggle.btn-danger {
        background: #bd362f;
    }

    .btn-danger:active, .btn-danger.active {
        background: #bd362f;
        box-shadow: none;
    }
    
    .btn-info {
        background: #49afcd;
        border-color: #49afcd;
        color: white;
    }

    .btn-info:hover, .btn-info:focus, .btn-info:active, .btn-info.active, .open > .dropdown-toggle.btn-info {
        background: #49afcd;
    }

    .btn-info:active, .btn-info.active {
        background: #49afcd;
        box-shadow: none;
    }
    
    /*Always put Jquery datepicker on the top layer */
    .ui-datepicker{
        z-index: 9999 !important;
    }
</style>
<?php if ($this->config->item('ga_code') != "") { ?>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        <?php if (($this->session->userdata('id') != FALSE) && ($this->config->item('ga_code') != FALSE)){
          $gacode = "ga('create', '%s', { 'userId': '%s' });";
          echo sprintf($gacode, $this->config->item('ga_code'), $this->session->userdata('id'));
        }?>
        ga('create', '<?php echo $this->config->item('ga_code');?>', 'auto');
        ga('send', 'pageview');
    </script>
<?php } ?>
</head>
<body>
