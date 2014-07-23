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
$this->lang->load('leaves', $language);
$this->lang->load('status', $language);
?>

<h2><?php echo lang('leaves_view_title');?><?php echo $leave['id']; ?></h2>

    <label for="startdate"><?php echo lang('leaves_view_field_start');?></label>
    <input type="input" name="startdate" value="<?php echo $leave['startdate']; ?>" readonly />
    <select name="startdatetype" readonly>
        <option selected><?php echo $leave['startdatetype']; ?></option>
    </select><br />
    
    <label for="enddate"><?php echo lang('leaves_view_field_end');?></label>
    <input type="input" name="enddate"  value="<?php echo $leave['enddate']; ?>" readonly />
    <select name="enddatetype" readonly>
        <option selected><?php echo $leave['enddatetype']; ?></option>
    </select><br />
    
    <label for="duration"><?php echo lang('leaves_view_field_duration');?></label>
    <input type="input" name="duration"  value="<?php echo $leave['duration']; ?>" readonly />
    
    <label for="type"><?php echo lang('leaves_view_field_type');?></label>
    <select name="type" readonly>
        <option selected><?php echo $leave['type_label']; ?></option>
    </select><br />
    
    <label for="cause"><?php echo lang('leaves_view_field_cause');?></label>
    <textarea name="cause" readonly><?php echo $leave['cause']; ?></textarea>
    
    <label for="status"><?php echo lang('leaves_view_field_status');?></label>
    <select name="status" readonly>
        <option selected><?php echo $leave['status_label']; ?></option>
    </select><br />

    <br /><br />
    <?php if ($leave['status'] == 1) { ?>
    <a href="<?php echo base_url();?>leaves/edit/<?php echo $leave['id'] ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i>&nbsp;<?php echo lang('leaves_view_button_edit');?></a>
    &nbsp;
    <?php } ?>
    <a href="<?php echo base_url();?>leaves" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('leaves_view_button_back_list');?></a>

