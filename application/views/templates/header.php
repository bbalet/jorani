<html>
<head>
	<title><?php echo $title ?> - LMS</title>
	
	<link href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo base_url();?>assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<!--[if lt IE 9]>
	<script src="<?php echo base_url();?>assets/js/html5shiv.min.js"></script>
	<![endif]-->
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
	
</head>
<body>

<div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <div class="container">
              <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </a>
            <div class="nav-collapse">
              <ul class="nav">			  
			    <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url();?>index.php/users/">List users</a></li>
                    <li><a href="<?php echo base_url();?>index.php/users/create">Add a user</a></li>
                    <li class="divider"></li>
                    <li class="nav-header">Settings</li>
                    <li><a href="<?php echo base_url();?>">Settings</a></li>
                  </ul>
                </li>
            </div>		   
        </div>
      </div>
    </div><!-- /.navbar -->

	<div class="container-fluid">
	