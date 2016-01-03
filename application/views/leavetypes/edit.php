<?php 
/**
 * This view allows an HR admin to modify a leave type.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<?php echo form_open('leavetypes/edit/' . $id); ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <label for="name"><?php echo lang('leavetypes_popup_update_field_name');?></label>
    <input type="text" name="name" value="<?php echo $type_name; ?>" />
    <br />
    <button id="send" class="btn btn-primary"><?php echo lang('leavetypes_popup_update_button_update');?></button>
</form>
