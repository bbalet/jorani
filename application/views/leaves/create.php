<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('leaves', $language);
$this->lang->load('status', $language);
?>

<h2><?php echo lang('leaves_create_title');?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('leaves/create') ?>

    <label for="startdate" required><?php echo lang('leaves_create_field_start');?></label>
    <input type="input" name="startdate" id="startdate" value="<?php echo set_value('startdate'); ?>" />
    <select name="startdatetype">
        <option value="Morning"><?php echo lang('leaves_date_type_morning');?></option>
        <option value="Afternoon"><?php echo lang('leaves_date_type_afternoon');?></option>
    </select><br />
    
    <label for="enddate" required><?php echo lang('leaves_create_field_end');?></label>
    <input type="input" name="enddate" id="enddate" value="<?php echo set_value('enddate'); ?>" />
    <select name="enddatetype">
        <option value="Morning"><?php echo lang('leaves_date_type_morning');?></option>
        <option value="Afternoon"><?php echo lang('leaves_date_type_afternoon');?></option>
    </select><br />
    
    <label for="type" required><?php echo lang('leaves_create_field_type');?></label>
    <select name="type" id="type">
    <?php foreach ($types as $types_item): ?>
        <option value="<?php echo $types_item['id'] ?>" <?php if ($types_item['id'] == 1) echo "selected" ?>><?php echo $types_item['name'] ?></option>
    <?php endforeach ?> 
    </select><br />
    
    <label for="duration" required><?php echo lang('leaves_create_field_duration');?></label>
    <input type="input" name="duration" id="duration" value="<?php echo set_value('duration'); ?>" />
    
    <div class="alert hide alert-error" id="lblCreditAlert" onclick="$('#lblCreditAlert').hide();">
        <button type="button" class="close">&times;</button>
        <?php echo lang('leaves_create_field_duration_message');?>
    </div>
    
    <label for="cause"><?php echo lang('leaves_create_field_cause');?></label>
    <textarea name="cause"><?php echo set_value('cause'); ?></textarea>
    
    <label for="status" required><?php echo lang('leaves_create_field_status');?></label>
    <select name="status">
        <option value="1" selected><?php echo lang('Planned');?></option>
        <option value="2"><?php echo lang('Requested');?></option>
    </select><br />

    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp; <?php echo lang('leaves_create_button_create');?></button>
    &nbsp;
    <a href="<?php echo base_url(); ?>leaves" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp; <?php echo lang('leaves_create_button_cancel');?></a>
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