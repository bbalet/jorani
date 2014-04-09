
<div class="row-fluid">
    <div class="span8">
        <h3><a href="<?php echo base_url();?>" style="text-decoration:none; color:black;"><img src="<?php echo base_url();?>assets/images/favicon.png">&nbsp;Passerelles numériques</a>
    </div>
    <div class="span4 pull-right">
        <a href="<?php echo base_url();?>users/reset/<?php echo $user_id; ?>" title="reset password" data-target="#frmChangeMyPwd" data-toggle="modal"><i class="icon-lock"></i></a>
        &nbsp;
        Welcome <?php echo $fullname;?>, <a href="<?php echo base_url();?>session/logout">Logout</a>
    </div>
</div>

<div id="frmChangeMyPwd" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmChangeMyPwd').modal('hide')" class="close">&times;</a>
         <h3>Change password</h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="javascript:$('#frmChangeMyPwd').modal('hide')" class="btn secondary">Cancel</a>
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
                    <li><a href="<?php echo base_url();?>users/">List of users</a></li>
                    <li><a href="<?php echo base_url();?>users/create">Add a user</a></li>
                    <li class="divider"></li>
                    <li class="nav-header">Settings</li>
                    <li><a href="<?php echo base_url();?>settings">Settings</a></li>
                  </ul>
                </li>
              </ul>
              <?php } ?>
                
              <?php if ($is_hr == TRUE) { ?>
              <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">HR <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li class="nav-header">Employees</li>
                    <li><a href="<?php echo base_url();?>hr/employees">List of employees</a></li>
                    <li class="divider"></li>
                    <li class="nav-header">Contracts</li>
                    <li><a href="<?php echo base_url();?>contracts/">List of contracts</a></li>
                    <li class="divider"></li>
                    <li class="nav-header">Leaves</li>
                    <li><a href="<?php echo base_url();?>leavetypes/">List of types</a></li>
                  </ul>
                </li>
              </ul>
              <?php } ?>

             <ul class="nav">			  
                <li class="dropdown">
                  <a href="<?php echo base_url();?>requests/">Requests</a>
                </li>
              </ul>
                
              <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">My leaves <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url();?>leaves/counters">Counters</a></li>
                    <li><a href="<?php echo base_url();?>leaves/">List of leave requests</a></li>
                    <li><a href="<?php echo base_url();?>leaves/create">Request a leave</a></li>
                  </ul>
                </li>
              </ul>
                
              <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Calendars <b class="caret"></b></a>
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

