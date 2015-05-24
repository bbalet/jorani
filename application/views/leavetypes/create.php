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
<?php $attributes = array('id' => 'frmCreateLeaveType');
echo form_open('leavetypes/create', $attributes); ?>
    <label for="name"><?php echo lang('leavetypes_popup_create_field_name');?></label>
    <input type="text" name="name" id="name" pattern=".{1,}" required />
    <label for"color"><?php echo lang('leavetypes_type_thead_color');?></label>
    <input name="color" type="color" id="html5colorpicker" class="form-control" onchange="clickColor(0, -1, -1, 5)" value="#ff0000">
    <br />
    <button id="send" class="btn btn-primary"><?php echo lang('leavetypes_popup_create_button_create');?></button>
</form>

