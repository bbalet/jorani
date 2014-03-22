<h2>Create a new user</h2>

<?php echo validation_errors(); ?>

<?php echo form_open('leaves/create') ?>

    <label for="startdate">Start Date</label>
    <input type="input" name="startdate" id="startdate" />
    <select name="startdatetype">
        <option value="Morning">Morning</option>
        <option value="Afternoon">Afternoon</option>
    </select><br />
    
    <label for="enddate">End Date</label>
    <input type="input" name="enddate" id="enddate" />
    <select name="enddatetype">
        <option value="Morning">Morning</option>
        <option value="Afternoon">Afternoon</option>
    </select><br />
    
    <label for="duration">Duration</label>
    <input type="input" name="duration" id="duration" />
    
    <label for="cause">Cause</label>
    <textarea name="cause"></textarea>
    <label for="status">Status</label>
    <select name="status">
        <option value="1">Planned</option>
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
        $('#startdate').datepicker({format: 'yyyy-mm-dd'});
        $('#enddate').datepicker({format: 'yyyy-mm-dd'});
    });

</script>