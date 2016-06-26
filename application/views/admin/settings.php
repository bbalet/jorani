<?php
/**
 * This view displays a portion of the configuration file (the part containing the application parameters).
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.3
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
            <tr><td>leave_status_requested</td><td><?php echo ($this->config->item('leave_status_requested') ? 'TRUE':'FALSE'); ?></td></tr>
            <tr><td>default_leave_type</td><td><?php echo $this->config->item('default_leave_type'); ?></td></tr>
            <tr><td>disable_edit_leave_duration</td><td><?php echo ($this->config->item('disable_edit_leave_duration') ? 'TRUE':'FALSE'); ?></td></tr>
            <tr><td>delete_rejected_requests</td><td><?php echo ($this->config->item('delete_rejected_requests') ? 'TRUE':'FALSE'); ?></td></tr>
            <tr><td>edit_rejected_requests</td><td><?php echo ($this->config->item('edit_rejected_requests') ? 'TRUE':'FALSE'); ?></td></tr>
            <tr><td>requests_by_manager</td><td><?php echo ($this->config->item('requests_by_manager') ? 'TRUE':'FALSE'); ?></td></tr>
            <tr><td>languages</td><td><?php echo $this->config->item('languages'); ?></td></tr>
            <tr><td>ics_enabled</td><td><?php echo ($this->config->item('ics_enabled') ? 'TRUE':'FALSE'); ?></td></tr>
            <tr><td>default_timezone</td><td><?php echo $this->config->item('default_timezone'); ?></td></tr>
            <tr><td>public_calendar</td><td><?php echo ($this->config->item('public_calendar') ? 'TRUE':'FALSE'); ?></td></tr>
          </tbody>
        </table>

    </div>
</div>
