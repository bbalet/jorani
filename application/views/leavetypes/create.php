<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('leavetypes', $language);?>

<?php echo form_open('leavetypes/create'); ?>
    <label for="name"><?php echo lang('hr_leaves_popup_create_field_name');?></label>
    <input type="text" name="name" />
    <br />
    <button id="send" class="btn btn-primary"><?php echo lang('hr_leaves_popup_create_button_create');?></button>
</form>
