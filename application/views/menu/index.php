
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
                    <li><a href="<?php echo base_url();?>users/">List users</a></li>
                    <li><a href="<?php echo base_url();?>users/create">Add a user</a></li>
                    <li class="divider"></li>
                    <li class="nav-header">Settings</li>
                    <li><a href="<?php echo base_url();?>">Settings</a></li>
                  </ul>
                </li>
              </ul>
                
                <ul class="nav navbar-nav pull-right">
                   <li>Welcome</li>
                   <li><a href="<?php echo base_url();?>/session/logout">Logout</a></li>		
                </ul>
                
            </div>		   
        </div>
      </div>
    </div><!-- /.navbar -->

