<h2>Edit Leave Request #<?php echo $leave['id']; ?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('leaves/edit') ?>

    <label for="startdate" required>Start Date</label>
    <input type="input" name="startdate" id="startdate" value="<?php echo $leave['startdate']; ?>" />
    <select name="startdatetype">
        <option value="Morning">Morning</option>
        <option value="Afternoon">Afternoon</option>
    </select><br />
    
    <label for="enddate" required>End Date</label>
    <input type="input" name="enddate" id="enddate" value="<?php echo $leave['enddate']; ?>" />
    <select name="enddatetype">
        <option value="Morning">Morning</option>
        <option value="Afternoon">Afternoon</option>
    </select><br />
    
    <label for="duration" required>Duration</label>
    <input type="input" name="duration" id="duration" value="<?php echo $leave['duration']; ?>" />
    
    <label for="type" required>Leave type</label>
    <select name="type">
    <?php foreach ($types as $types_item): ?>
        <option value="<?php echo $types_item['id'] ?>" <?php if ($types_item['id'] == 1) echo "selected" ?>><?php echo $types_item['name'] ?></option>
    <?php endforeach ?>    
    </select><br />
    
    <label for="cause">Cause</label>
    <textarea name="cause"><?php echo set_value('cause'); ?></textarea>
    
    <label for="status" required>Status</label>
    <select name="status">
        <option value="1" selected>Planned</option>
        <option value="2">Requested</option>
    </select><br />

    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Request leave</button>
    &nbsp;
    <a href="<?php echo base_url(); ?>leaves/" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>
</form>

<link href="<?php echo base_url();?>assets/datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('#startdate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
        $('#enddate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    });

</script>