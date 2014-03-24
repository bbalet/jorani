
<div class="row-fluid">
    <div class="span8">
        <h3><a href="<?php echo base_url();?>" style="text-decoration:none; color:black;"><img src="<?php echo base_url();?>assets/images/favicon.png">&nbsp;Passerelles numériques</a>
    </div>
    <div class="span4 pull-right">
        Welcome <?php echo $fullname;?>, <a href="<?php echo base_url();?>session/logout">Logout</a>
    </div>
</div>

<div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <div class="container">
              <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </a>
            <div class="nav-collapse">
                
              <?php if ($is_admin == TRUE) { ?>
              <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url();?>users/">List users</a></li>
                    <li><a href="<?php echo base_url();?>users/create">Add a user</a></li>
                    <li class="divider"></li>
                    <li class="nav-header">Settings</li>
                    <li><a href="<?php echo base_url();?>settings">Settings</a></li>
                  </ul>
                </li>
              </ul>
              <?php } ?>

              <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">My leaves <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url();?>leaves/">List</a></li>
                    <li><a href="<?php echo base_url();?>leaves/create">Request a leave</a></li>
                  </ul>
                </li>
              </ul>
                
              <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Calendar <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url();?>calendar/team/">Team calendar</a></li>
                    <li><a href="<?php echo base_url();?>calendar/individual/">My calendar</a></li>
                  </ul>
                </li>
              </ul>
                
            </div>		   
        </div>
      </div>
    </div><!-- /.navbar -->

