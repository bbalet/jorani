<h2>Edit Overtime Request #<?php echo $leave['id']; ?></h2>

<?php echo validation_errors(); ?>

<?php if (isset($_GET['source'])) {
    echo form_open('extra/edit/' . $id . '?source=' . $_GET['source']);
} else {
    echo form_open('extra/edit/' . $id);
} ?>

    <label for="date" required>Date</label>
    <input type="input" name="date" id="date" value="<?php echo $leave['date']; ?>" />
    
    <label for="duration" required>Duration</label>
    <input type="input" name="duration" id="duration" value="<?php echo $leave['duration']; ?>" />
    
    <label for="cause">Cause</label>
    <textarea name="cause"><?php echo $leave['cause']; ?></textarea>
    
    <label for="status" required>Status</label>
    <select name="status">
        <option value="1" <?php if ($leave['status'] == 1) echo 'selected'; ?>>Planned</option>
        <option value="2" <?php if ($leave['status'] == 2) echo 'selected'; ?>>Requested</option>
        <?php if ($is_hr) {?>
        <option value="3" <?php if ($leave['status'] == 3) echo 'selected'; ?>>Accepted</option>
        <option value="4" <?php if ($leave['status'] == 4) echo 'selected'; ?>>Rejected</option>        
        <?php } ?>
    </select><br />

    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Update overtime</button>
    &nbsp;
    <?php if (isset($_GET['source'])) {?>
        <a href="<?php echo base_url() . $_GET['source']; ?>" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>
    <?php } else {?>
        <a href="<?php echo base_url(); ?>extra" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>
    <?php } ?>
</form>

<link href="<?php echo base_url();?>assets/datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('#date').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    });
</script>