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
$this->lang->load('leavetypes', $language);?>

<?php echo form_open('leavetypes/edit/' . $id); ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <label for="name"><?php echo lang('leavetypes_popup_update_field_name');?></label>
    <input type="text" name="name" value="<?php echo $type_name; ?>" />
    <label for"color"><?php echo lang('leavetypes_type_thead_color');?></label>
    <input name="color" type="color" id="html5colorpicker" class="form-control" onchange="clickColor(0, -1, -1, 5)" value="<?php echo $type['color']; ?>">
    <br />
    <button id="send" class="btn btn-primary"><?php echo lang('leavetypes_popup_update_button_update');?></button>
</form>
