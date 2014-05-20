<h2>Edit Overtime Request #<?php echo $leave['id']; ?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('extra/edit/' . $id) ?>

    <label for="date" required>Date</label>
    <input type="input" name="date" id="date" value="<?php echo $leave['date']; ?>" />
    
    <label for="duration" required>Duration</label>
    <input type="input" name="duration" id="duration" value="<?php echo $leave['duration']; ?>" />
    
    <label for="cause">Cause</label>
    <textarea name="cause"><?php echo $leave['cause']; ?></textarea>
    
    <label for="status" required>Status</label>
    <select name="status">
        <option value="1" selected>Planned</option>
        <option value="2">Requested</option>
    </select><br />

    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Update overtime</button>
    &nbsp;
    <a href="<?php echo base_url(); ?>extra" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>
</form>

<link href="<?php echo base_url();?>assets/datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('#date').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    });
</script>