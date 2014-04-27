<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('menu', $language);?>

<div class="row-fluid">
    <div class="span6">
        <h3><a href="<?php echo base_url();?>" style="text-decoration:none; color:black;"><img src="<?php echo base_url();?>assets/images/logo.png">&nbsp;<?php echo lang('menu_banner_slogan');?></a>
    </div>
    <div class="span6 pull-right">
        <a href="<?php echo base_url();?>users/reset/<?php echo $user_id; ?>" title="reset password" data-target="#frmChangeMyPwd" data-toggle="modal"><i class="icon-lock"></i></a>
        &nbsp;
        <?php echo lang('menu_banner_welcome');?> <?php echo $fullname;?>, <a href="<?php echo base_url();?>session/logout"><?php echo lang('menu_banner_logout');?></a>
        <!--
        &nbsp;
        <form id="languageFrom" action="<?php echo base_url();?>session/language">
            <input type="hidden" name="last_page" value="<?php echo current_url();?>" />
            <select name="language" id="language">
                <option value="en" <?php if ($language_code == 'en') echo 'selected'; ?>>English</option>
                <option value="fr" <?php if ($language_code == 'fr') echo 'selected'; ?>>Français</option>
            </select>
        </form>
        //-->
        <script type="text/javascript">
            $(function () {
               //Change the current language
                /*$('#language').change(function(){
                    $('#languageFrom').submit();
                });*/
                $('#frmChangeMyPwd').alert();
            });
        </script>
        
    </div>
</div>

<div id="frmChangeMyPwd" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmChangeMyPwd').modal('hide')" class="close">&times;</a>
         <h3><?php echo lang('menu_password_popup_title');?></h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="javascript:$('#frmChangeMyPwd').modal('hide')" class="btn secondary"><?php echo lang('menu_password_popup_button_cancel');?></a>
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
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('menu_admin_title');?> <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url();?>users"><?php echo lang('menu_admin_list_users');?></a></li>
                    <li><a href="<?php echo base_url();?>users/create"><?php echo lang('menu_admin_add_user');?></a></li>
                    <li class="divider"></li>
                    <li class="nav-header"><?php echo lang('menu_admin_settings_divider');?></li>
                    <li><a href="<?php echo base_url();?>settings"><?php echo lang('menu_admin_settings');?></a></li>
                  </ul>
                </li>
              </ul>
              <?php } ?>

              <?php if ($is_hr == TRUE) { ?>
              <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('menu_hr_title');?> <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li class="nav-header"><?php echo lang('menu_hr_employees_divider');?></li>
                    <li><a href="<?php echo base_url();?>hr/employees"><?php echo lang('menu_hr_list_employees');?></a></li>
                    <li class="divider"></li>
                    <li class="nav-header"><?php echo lang('menu_hr_contracts_divider');?></li>
                    <li><a href="<?php echo base_url();?>contracts"><?php echo lang('menu_hr_list_contracts');?></a></li>
                    <li class="divider"></li>
                    <li class="nav-header"><?php echo lang('menu_hr_leaves_type_divider');?></li>
                    <li><a href="<?php echo base_url();?>leavetypes"><?php echo lang('menu_hr_list_leaves_type');?></a></li>
                  </ul>
                </li>
              </ul>
              <?php } ?>

             <ul class="nav">			  
                <li class="dropdown">
                  <a href="<?php echo base_url();?>requests"><?php echo lang('menu_requests_title');?></a>
                </li>
              </ul>
              
              <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('menu_leaves_title');?> <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url();?>leaves/counters"><?php echo lang('menu_leaves_counters');?></a></li>
                    <li><a href="<?php echo base_url();?>leaves"><?php echo lang('menu_leaves_list_requests');?></a></li>
                    <li><a href="<?php echo base_url();?>leaves/create"><?php echo lang('menu_leaves_create_request');?></a></li>
                  </ul>
                </li>
              </ul>
         
              <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('menu_calendar_title');?> <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                      <li><a href="<?php echo base_url();?>calendar/individual"><?php echo lang('menu_calendar_individual');?></a></li>
                      <li><a href="<?php echo base_url();?>calendar/workmates"><?php echo lang('menu_calendar_workmates');?></a></li>
                      <li><a href="<?php echo base_url();?>calendar/collaborators"><?php echo lang('menu_calendar_collaborators');?></a></li>
                  </ul>
                </li>
              </ul>
                
            </div>		   
        </div>
      </div>
    </div><!-- /.navbar -->

