<?php 
/*
 * This file is part of lms.
 *
 * lms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with lms.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('menu', $language);?>

<div class="row-fluid">
    <div class="span6">
        <h3><a href="<?php echo base_url();?>" style="text-decoration:none; color:black;"><img src="<?php echo base_url();?>assets/images/logo.png">&nbsp;<?php echo lang('menu_banner_slogan');?></a>
    </div>
    <div class="span6 pull-right">
        <a href="<?php echo base_url();?>users/myprofile" title="<?php echo lang('menu_banner_tip_myprofile');?>"><i class="icon-user"></i></a>
        &nbsp;
        <a href="<?php echo base_url();?>users/reset/<?php echo $user_id; ?>" title="<?php echo lang('menu_banner_tip_reset');?>" data-target="#frmChangeMyPwd" data-toggle="modal"><i class="icon-lock"></i></a>
        &nbsp;
        <?php echo lang('menu_banner_welcome');?> <?php echo $fullname;?>, <a href="<?php echo base_url();?>session/logout"><?php echo lang('menu_banner_logout');?></a>     
    </div>
</div>

<div id="frmChangeMyPwd" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h3><?php echo lang('menu_password_popup_title');?></h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal"><?php echo lang('menu_password_popup_button_cancel');?></button>
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
                
              <?php if ($is_hr == TRUE) { ?>
              <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('menu_admin_title');?> <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url();?>users"><?php echo lang('menu_admin_list_users');?></a></li>
                    <li><a href="<?php echo base_url();?>users/create"><?php echo lang('menu_admin_add_user');?></a></li>
                    <!--<li class="divider"></li>
                    <li class="nav-header"><?php echo lang('menu_admin_settings_divider');?></li>
                    <li><a href="<?php echo base_url();?>settings"><?php echo lang('menu_admin_settings');?></a></li>//-->
                    <li class="divider"></li>
                    <li class="nav-header"><?php echo lang('menu_hr_leaves_type_divider');?></li>
                    <li><a href="<?php echo base_url();?>leavetypes"><?php echo lang('menu_hr_list_leaves_type');?></a></li>
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
                    <li><a href="<?php echo base_url();?>organization"><?php echo lang('menu_hr_list_organization');?></a></li>
                    <li class="divider"></li>
                    <li class="nav-header"><?php echo lang('menu_hr_contracts_divider');?></li>
                    <li><a href="<?php echo base_url();?>contracts"><?php echo lang('menu_hr_list_contracts');?></a></li>
                    <li><a href="<?php echo base_url();?>positions"><?php echo lang('menu_hr_list_positions');?></a></li>
                    <li class="divider"></li>
                    <li class="nav-header"><?php echo lang('menu_hr_reports_divider');?></li>
                    <li><a href="<?php echo base_url();?>reports/balance"><?php echo lang('menu_hr_report_leave_balance');?></a></li>
                  </ul>
                </li>
              </ul>
              <?php } ?>

             <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('menu_validation_title');?> <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo base_url();?>requests/collaborators"><?php echo lang('menu_validation_collaborators');?></a></li>
                    <li><a href="<?php echo base_url();?>requests"><?php echo lang('menu_validation_leaves');?></a></li>
                    <li><a href="<?php echo base_url();?>overtime"><?php echo lang('menu_validation_overtime');?></a></li>
                  </ul>
                </li>
              </ul>
              
              <ul class="nav">			  
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo lang('menu_requests_title');?> <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li class="nav-header"><?php echo lang('menu_requests_leaves');?></li>
                    <li><a href="<?php echo base_url();?>leaves/counters"><?php echo lang('menu_leaves_counters');?></a></li>
                    <li><a href="<?php echo base_url();?>leaves"><?php echo lang('menu_leaves_list_requests');?></a></li>
                    <li><a href="<?php echo base_url();?>leaves/create"><?php echo lang('menu_leaves_create_request');?></a></li>
                    <li class="divider"></li>
                    <li class="nav-header"><?php echo lang('menu_requests_overtime');?></li>
                    <li><a href="<?php echo base_url();?>extra"><?php echo lang('menu_requests_list_extras');?></a></li>
                    <li><a href="<?php echo base_url();?>extra/create"><?php echo lang('menu_requests_request_extra');?></a></li>
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
                      <li><a href="<?php echo base_url();?>calendar/department"><?php echo lang('menu_calendar_department');?></a></li>
                      <li><a href="<?php echo base_url();?>calendar/organization"><?php echo lang('menu_calendar_organization');?></a></li>
                  </ul>
                </li>
              </ul>
                
            </div>		   
        </div>
      </div>
    </div><!-- /.navbar -->

    <script type="text/javascript">
        $(function () {
            $('#frmChangeMyPwd').alert();
        });
    </script>
