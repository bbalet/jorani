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
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('users', $language);?>

<h1><?php echo lang('users_myprofile_title');?></h1>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_firstname');?></strong></div>
    <div class="span3"><?php echo $user['firstname'];?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_lastname');?></strong></div>
    <div class="span3"><?php echo $user['lastname'];?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_manager');?></strong></div>
    <div class="span3"><?php echo $manager_label;?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_contract');?></strong></div>
    <div class="span3"><?php echo $contract_label;?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_position');?></strong></div>
    <div class="span3"><?php echo $position_label;?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_entity');?></strong></div>
    <div class="span3"><?php echo $organization_label;?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_hired');?></strong></div>
    <div class="span3"><?php echo $user['datehired'];?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_identifier');?></strong></div>
    <div class="span3"><?php echo $user['identifier'];?></div>
    <div class="span6">&nbsp;</div>
</div>
