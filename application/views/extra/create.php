<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('extra', $language);
$this->lang->load('status', $language);?>

<h2><?php echo lang('extra_create_title');?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('extra/create') ?>

    <label for="date" required><?php echo lang('extra_create_field_date');?></label>
    <input type="input" name="date" id="date" value="<?php echo set_value('date'); ?>" />
    
    <label for="duration" required><?php echo lang('extra_create_field_duration');?></label>
    <input type="input" name="duration" id="duration" value="<?php echo set_value('duration'); ?>" />
    
    <label for="cause"><?php echo lang('extra_create_field_cause');?></label>
    <textarea name="cause"><?php echo set_value('cause'); ?></textarea>
    
    <label for="status" required><?php echo lang('extra_create_field_status');?></label>
    <select name="status">
        <option value="1" selected><?php echo lang('Planned');?></option>
        <option value="2"><?php echo lang('Requested');?></option>
    </select><br />

    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp; <?php echo lang('extra_create_button_create');?></button>
    &nbsp;
    <a href="<?php echo base_url(); ?>leaves" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp; <?php echo lang('extra_create_button_cancel');?></a>
</form>

<link href="<?php echo base_url();?>assets/datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('#date').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    });
</script>