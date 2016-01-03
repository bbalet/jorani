<?php 
/**
 * This view allows an HR admin to create a new leave type.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<?php $attributes = array('id' => 'frmCreateLeaveType');
echo form_open('leavetypes/create', $attributes); ?>
    <label for="name"><?php echo lang('leavetypes_popup_create_field_name');?></label>
    <input type="text" name="name" id="name" pattern=".{1,}" required />
    <br />
    <button id="send" class="btn btn-primary"><?php echo lang('leavetypes_popup_create_button_create');?></button>
</form>

