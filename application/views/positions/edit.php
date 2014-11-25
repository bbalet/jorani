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
$this->lang->load('positions', $language);?>

<h2><?php echo lang('positions_edit_title');?><?php echo $position['id']; ?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('positions/edit/' . $position['id']) ?>

    <label for="name"><?php echo lang('positions_edit_field_name');?></label>
    <input type="input" name="name" id="name" value="<?php echo $position['name']; ?>" autofocus required /><br />

    <label for="description"><?php echo lang('positions_edit_field_description');?></label>
    <textarea type="input" name="description" id="description" /><?php echo $position['description']; ?></textarea>

    <br /><br />
    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('positions_edit_button_update');?></button>
    &nbsp;
    <a href="<?php echo base_url();?>positions" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('positions_edit_button_cancel');?></a>
</form>