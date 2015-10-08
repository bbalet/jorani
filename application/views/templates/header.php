<!DOCTYPE html>
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
    <div class="container-fluid" id="wrap">
