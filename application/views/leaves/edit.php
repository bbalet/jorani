<h2>Edit Leave Request #<?php echo $leave['id']; ?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('leaves/edit/' . $id) ?>

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
    
    <label for="type" required>Leave type</label>
    <select name="type" id="type">
    <?php foreach ($types as $types_item): ?>
        <option value="<?php echo $types_item['id'] ?>" <?php if ($types_item['id'] == 1) echo "selected" ?>><?php echo $types_item['name'] ?></option>
    <?php endforeach ?>    
    </select><br />
    
    <label for="duration" required>Duration</label>
    <input type="input" name="duration" id="duration" value="<?php echo $leave['duration']; ?>" />
    
    <div class="alert hide alert-error" id="lblCreditAlert">
        <button type="button" class="close">&times;</button>
        You are exceeding your entitled days
    </div>
    
    <label for="cause">Cause</label>
    <textarea name="cause"><?php echo $leave['cause']; ?></textarea>
    
    <label for="status" required>Status</label>
    <select name="status">
        <option value="1" selected>Planned</option>
        <option value="2">Requested</option>
    </select><br />

    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Update leave</button>
    &nbsp;
    <a href="<?php echo base_url(); ?>leaves" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>
</form>

<link href="<?php echo base_url();?>assets/datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('#startdate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
        $('#enddate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
        
        //Check if the user has not exceed the number of entitled days
        $("#duration").keyup(function() {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>leaves/credit",
                data: { id: <?php echo $user_id; ?>, type: $("#type option:selected").text() }
                })
                .done(function( msg ) {
                    var credit = parseInt(msg);
                    var duration = parseInt($("#duration").val());
                    if (duration > credit) {
                        $("#lblCreditAlert").show();
                        
                    } else {
                        $("#lblCreditAlert").hide();
                    }
                });
        });
    });
</script>