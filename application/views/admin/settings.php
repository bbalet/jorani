<?php
/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jorani is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */
?>

<div class="row-fluid">
    <div class="span12">

        <h2><?php echo $title;?><?php echo $help;?></h2>

        <table class="table table-bordered table-hover table-condensed">
          <tbody>
            <tr><td>from_mail</td><td><?php echo $this->config->item('from_mail'); ?></td></tr>
            <tr><td>from_name</td><td><?php echo $this->config->item('from_name'); ?></td></tr>
            <tr><td>subject_prefix</td><td><?php echo $this->config->item('subject_prefix'); ?></td></tr>
            <tr><td>leave_status_requested</td><td><?php echo ($this->config->item('leave_status_requested') ? lang('global_true') : lang('global_false')); ?></td></tr>
            <tr><td>default_leave_type</td><td><?php echo $this->config->item('default_leave_type'); ?></td></tr>
            <tr><td>disable_edit_leave_duration</td><td><?php echo ($this->config->item('disable_edit_leave_duration') ? lang('global_true') : lang('global_false')); ?></td></tr>
            <tr><td>delete_rejected_requests</td><td><?php echo ($this->config->item('delete_rejected_requests') ? lang('global_true') : lang('global_false')); ?></td></tr>
            <tr><td>edit_rejected_requests</td><td><?php echo ($this->config->item('edit_rejected_requests') ? lang('global_true') : lang('global_false')); ?></td></tr>
            <tr><td>requests_by_manager</td><td><?php echo ($this->config->item('requests_by_manager') ? lang('global_true') : lang('global_false')); ?></td></tr>
            <tr><td>languages</td><td><?php echo $this->config->item('languages'); ?></td></tr>
            <tr><td>ics_enabled</td><td><?php echo ($this->config->item('ics_enabled') ? lang('global_true') : lang('global_false')); ?></td></tr>
            <tr><td>default_timezone</td><td><?php echo $this->config->item('default_timezone'); ?></td></tr>
            <tr><td>public_calendar</td><td><?php echo ($this->config->item('public_calendar') ? lang('global_true') : lang('global_false')); ?></td></tr>
          </tbody>
        </table>

    </div>
</div>
