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

<h2><?php echo lang('positions_create_title');?></h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'target');
echo form_open('positions/create', $attributes); ?>

    <label for="name"><?php echo lang('positions_create_field_name');?></label>
    <input type="text" name="name" id="name" autofocus required /><br />

    <label for="description"><?php echo lang('positions_create_field_description');?></label>
    <textarea type="input" name="description" id="description" /></textarea>
    
    <br /><br />
    <button id="send" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('positions_create_button_create');?></button>
    &nbsp;
    <a href="<?php echo base_url(); ?>positions" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('positions_create_button_cancel');?></a>
</form>
