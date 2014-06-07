<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('extra', $language);
$this->lang->load('status', $language);?>

<h2><?php echo lang('extra_edit_title');?><?php echo $leave['id']; ?></h2>

<?php echo validation_errors(); ?>

<?php if (isset($_GET['source'])) {
    echo form_open('extra/edit/' . $id . '?source=' . $_GET['source']);
} else {
    echo form_open('extra/edit/' . $id);
} ?>

    <label for="date"><?php echo lang('extra_edit_field_date');?></label>
    <input type="input" name="date" id="date" value="<?php echo $leave['date']; ?>" required />
    
    <label for="duration"><?php echo lang('extra_edit_field_duration');?></label>
    <input type="input" name="duration" id="duration" value="<?php echo $leave['duration']; ?>" required />
    
    <label for="cause"><?php echo lang('extra_edit_field_cause');?></label>
    <textarea name="cause" required><?php echo $leave['cause']; ?></textarea>
    
    <label for="status"><?php echo lang('extra_edit_field_status');?></label>
    <select name="status" required>
        <option value="1" <?php if ($leave['status'] == 1) echo 'selected'; ?>><?php echo lang('Planned');?></option>
        <option value="2" <?php if ($leave['status'] == 2) echo 'selected'; ?>><?php echo lang('Requested');?></option>
        <?php if ($is_hr) {?>
        <option value="3" <?php if ($leave['status'] == 3) echo 'selected'; ?>><?php echo lang('Accepted');?></option>
        <option value="4" <?php if ($leave['status'] == 4) echo 'selected'; ?>><?php echo lang('Rejected');?></option>        
        <?php } ?>
    </select><br />

    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('extra_edit_button_update');?></button>
    &nbsp;
    <?php if (isset($_GET['source'])) {?>
        <a href="<?php echo base_url() . $_GET['source']; ?>" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('extra_edit_button_cancel');?></a>
    <?php } else {?>
        <a href="<?php echo base_url(); ?>extra" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('extra_edit_button_cancel');?></a>
    <?php } ?>
</form>

<link href="<?php echo base_url();?>assets/datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('#date').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    });
</script>