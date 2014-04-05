<?php echo validation_errors(); ?>

<?php echo form_open('entitleddays/create') ?>

    <input type="hidden" name="contract" value="<?php echo $users_item['id']; ?>" required /><br />

    <label for="startdate" required>Start Date</label>
    <input type="input" name="startdate" id="startdate" value="<?php echo set_value('startdate'); ?>" />
    <select name="startdatetype">
        <option value="Morning">Morning</option>
        <option value="Afternoon">Afternoon</option>
    </select><br />
    
    <label for="enddate" required>End Date</label>
    <input type="input" name="enddate" id="enddate" value="<?php echo set_value('enddate'); ?>" />
    <select name="enddatetype">
        <option value="Morning">Morning</option>
        <option value="Afternoon">Afternoon</option>
    </select><br />
    
    <label for="days" required>Entitled days</label>
    <input type="input" name="days" id="duration" value="<?php echo set_value('days'); ?>" />
    
    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Add</button>
    &nbsp;
    <a href="<?php echo base_url(); ?>contracts/" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>
</form>

<link href="<?php echo base_url();?>assets/datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('#startdate').datepicker({format: 'yyyy-mm-dd'});
        $('#enddate').datepicker({format: 'yyyy-mm-dd'});
    });

</script>