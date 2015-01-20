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
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('leaves', $language);
$this->lang->load('status', $language);
$this->lang->load('calendar', $language);
$this->lang->load('global', $language);?>

<h2><?php echo lang('leaves_view_title');?><?php echo $leave['id']; ?>&nbsp;<span class="muted">(<?php echo $name ?>)</span></h2>

    <label for="startdate"><?php echo lang('leaves_view_field_start');?></label>
    <input type="text" name="startdate" value="<?php $date = new DateTime($leave['startdate']); echo $date->format(lang('global_date_format'));?>" readonly />
    <select name="startdatetype" readonly>
        <option selected><?php echo lang($leave['startdatetype']); ?></option>
    </select><br />
    
    <label for="enddate"><?php echo lang('leaves_view_field_end');?></label>
    <input type="text" name="enddate"  value="<?php $date = new DateTime($leave['enddate']); echo $date->format(lang('global_date_format'));?>" readonly />
    <select name="enddatetype" readonly>
        <option selected><?php echo lang($leave['enddatetype']); ?></option>
    </select><br />
    
    <label for="duration"><?php echo lang('leaves_view_field_duration');?></label>
    <input type="text" name="duration"  value="<?php echo $leave['duration']; ?>" readonly />
    
    <label for="type"><?php echo lang('leaves_view_field_type');?></label>
    <select name="type" readonly>
        <option selected><?php echo $leave['type_label']; ?></option>
    </select><br />
    
    <label for="cause"><?php echo lang('leaves_view_field_cause');?></label>
    <textarea name="cause" readonly><?php echo $leave['cause']; ?></textarea>
    
    <label for="status"><?php echo lang('leaves_view_field_status');?></label>
    <select name="status" readonly>
        <option selected><?php echo lang($leave['status_label']); ?></option>
    </select><br />

    <br /><br />
    <?php if (($leave['status'] == 1) || ($is_hr)) { ?>
    <a href="<?php echo base_url();?>leaves/edit/<?php echo $leave['id'] ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i>&nbsp;<?php echo lang('leaves_view_button_edit');?></a>
    &nbsp;
    <?php } ?>
    
    <?php if (isset($_GET['source'])) {?>
        <a href="<?php echo base_url() . $_GET['source']; ?>" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('leaves_view_button_back_list');?></a>
    <?php } else {?>
        <a href="<?php echo base_url(); ?>leaves" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('leaves_view_button_back_list');?></a>
    <?php } ?>
    