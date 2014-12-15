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
$this->lang->load('extra', $language);
$this->lang->load('status', $language);
$this->lang->load('global', $language);?>

<h2><?php echo lang('extra_view_title');?><?php echo $extra['id']; ?></h2>

    <label for="date" required><?php echo lang('extra_view_field_date');?></label>
    <input type="text" name="date"  value="<?php $date = new DateTime($extra['date']); echo $date->format(lang('global_date_format'));?>" readonly />
    
    <label for="duration" required><?php echo lang('extra_view_field_duration');?></label>
    <input type="text" name="duration"  value="<?php echo $extra['duration']; ?>" readonly />
    
    <label for="cause"><?php echo lang('extra_view_field_cause');?></label>
    <textarea name="cause" readonly><?php echo $extra['cause']; ?></textarea>
    
    <label for="status"><?php echo lang('extra_view_field_status');?></label>
    <select name="status" readonly>
        <option selected><?php echo lang($extra['status_label']); ?></option>
    </select><br />
    <br /><br />
    <?php if ($extra['status'] == 1) { ?>
    <a href="<?php echo base_url();?>extra/edit/<?php echo $extra['id'] ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i>&nbsp;<?php echo lang('extra_view_button_edit');?></a>
    &nbsp;
    <?php } ?>
    <a href="<?php echo base_url();?>extra" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('extra_view_button_back_list');?></a>
