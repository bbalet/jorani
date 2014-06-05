<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('positions', $language);?>

<h2><?php echo lang('positions_create_title');?></h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'target');
echo form_open('positions/create', $attributes); ?>

    <label for="name"><?php echo lang('positions_create_field_name');?></label>
    <input type="input" name="name" id="name" required /><br />

    <label for="description"><?php echo lang('positions_create_field_description');?></label>
    <textarea type="input" name="description" id="description" /></textarea>
    
    <br /><br />
    <button id="send" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('positions_create_button_create');?></button>
    &nbsp;
    <a href="<?php echo base_url(); ?>positions" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('positions_create_button_cancel');?></a>
</form>
