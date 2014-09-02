<!DOCTYPE html>
<html lang="<?php echo $language_code;?>">
<head>
    <title><?php echo $title ?> - LMS</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <meta charset="UTF-8">
    <link href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="<?php echo base_url();?>assets/js/html5shiv.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo base_url();?>apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo base_url();?>apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo base_url();?>apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo base_url();?>apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo base_url();?>apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo base_url();?>apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo base_url();?>apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo base_url();?>apple-touch-icon-152x152.png">
    <link rel="icon" type="image/png" href="<?php echo base_url();?>favicon-196x196.png" sizes="196x196">
    <link rel="icon" type="image/png" href="<?php echo base_url();?>favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="<?php echo base_url();?>favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="<?php echo base_url();?>favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="<?php echo base_url();?>favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php echo base_url();?>favicon.ico" sizes="32x32">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-TileImage" content="<?php echo base_url();?>mstile-144x144.png">
    

<?php if ($this->config->item('ga_code') != "") { ?>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', '<?php echo $this->config->item('ga_code');?>', 'auto');
        ga('send', 'pageview');
    </script>
<?php } ?>
</head>
<body>
    <div class="container-fluid">
	