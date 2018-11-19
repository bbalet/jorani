<?php
/**
 * This view is included into all desktop full views. It contains HTML and CSS definitions.
 * @copyright  Copyright (c) 2014-2018 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.1.0
 */
?><!DOCTYPE html>
<html lang="<?php echo $language_code;?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta charset="utf-8">
    <title><?php echo $title ?> - Jorani</title>
    <meta description="Jorani a free and open source leave management system. Workflow of approval; e-mail notifications; calendars; reports; export to Excel and more.">
    <meta name="version" content="0.6.0">
    <link href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/css/jorani-0.6.6.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/MDI-2.1.19/css/materialdesignicons.min.css">
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
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
    <link rel="icon" type="image/x-icon" href="<?php echo base_url();?>favicon.ico" sizes="32x32">
    <style>
<?php //Font mapping with languages needing a better font than the default font
$fonts = $this->config->item('fonts');
if (!is_null($fonts)) {
    if (array_key_exists($language_code, $fonts)) { ?>
    @font-face {
      font-family: '<?php echo $fonts[$language_code]['name'];?>';
      src: url('<?php echo base_url(), 'assets/fonts/', $fonts[$language_code]['asset'];?>') format('truetype');
    }
    body, button, input, select, .ui-datepicker, .selectize-input {
        font-family: '<?php echo $fonts[$language_code]['name'];?>' !important;
    }
<?php
        }
    } ?>
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
