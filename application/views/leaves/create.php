<?php
/*
 * This file is part of lms.
 *
 * lms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('leaves', $language);
$this->lang->load('status', $language);
?>

<h2><?php echo lang('leaves_create_title');?></h2>

<div class="row-fluid">
    <div class="span8">

<?php echo validation_errors(); ?>

<?php echo form_open('leaves/create') ?>

    <label for="startdate" required><?php echo lang('leaves_create_field_start');?></label>
    <input type="input" name="startdate" id="startdate" value="<?php echo set_value('startdate'); ?>" />
    <select name="startdatetype" id="startdatetype">
        <option value="Morning"><?php echo lang('leaves_date_type_morning');?></option>
        <option value="Afternoon"><?php echo lang('leaves_date_type_afternoon');?></option>
    </select><br />
    
    <label for="enddate" required><?php echo lang('leaves_create_field_end');?></label>
    <input type="input" name="enddate" id="enddate" value="<?php echo set_value('enddate'); ?>" />
    <select name="enddatetype" id="enddatetype">
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

    </div>
    <div class="span4">
        <span id="spnDayOff">&nbsp;</span>
    </div>
</div>

<link href="<?php echo base_url();?>assets/datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-langs.min.js" type="text/javascript"></script>
<script type="text/javascript">
    
    var last_startDate = moment("Jan 1, 1970");
    var last_endDate = moment("Jan 1, 1970");
    var duration = 0;
    var last_duration = 0;
    var addDays = 0;
    var last_type = 0;
    
    //Try to calculate the length of the leave
    function getLeaveLength() {
        var start = moment($('#startdate').val());
        var end = moment($('#enddate').val());
        var startType = $('#startdatetype option:selected').val();
        var endType = $('#enddatetype option:selected').val();      
        
        if (start.isValid() && end.isValid()) {
            if (start.isSame(end)) {
                addDays = 0;
                if (startType == "Morning" && endType == "Morning") {
                    duration = 0.5;
                    $("#spnDayOff").html("<img src='<?php echo base_url();?>assets/images/leave_1d_MM.png' />");
                }
                if (startType == "Afternoon" && endType == "Afternoon") {
                    duration = 0.5;
                    $("#spnDayOff").html("<img src='<?php echo base_url();?>assets/images/leave_1d_AA.png' />");
                }
                if (startType == "Morning" && endType == "Afternoon") {
                    duration = 1;
                    $("#spnDayOff").html("<img src='<?php echo base_url();?>assets/images/leave_1d_MA.png' />");
                }
                if (startType == "Afternoon" && endType == "Morning") {
                    //Error
                    $("#spnDayOff").html("<img src='<?php echo base_url();?>assets/images/date_error.png' />");
                }
                $('#duration').val(duration + addDays);
                checkDuration();
            } else {
                 if (start.isBefore(end)) {
                    if (startType == "Morning" && endType == "Morning") {
                        $("#spnDayOff").html("<img src='<?php echo base_url();?>assets/images/leave_2d_MM.png' />");
                        addDays = 0.5;
                    }
                    if (startType == "Afternoon" && endType == "Afternoon") {
                        $("#spnDayOff").html("<img src='<?php echo base_url();?>assets/images/leave_2d_AA.png' />");
                        addDays = 0.5;
                    }
                    if (startType == "Morning" && endType == "Afternoon") {
                        $("#spnDayOff").html("<img src='<?php echo base_url();?>assets/images/leave_2d_MA.png' />");
                        addDays = 1;
                    }
                    if (startType == "Afternoon" && endType == "Morning") {
                        $("#spnDayOff").html("<img src='<?php echo base_url();?>assets/images/leave_2d_AM.png' />");
                        addDays = 0;
                    }
                    //Prevent multiple triggers by UI components
                    if (!start.isSame(last_startDate) || !end.isSame(last_endDate)) {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url();?>leaves/length",
                            data: {
                                user_id: <?php echo $user_id; ?>,
                                start: $('#startdate').val(),
                                end: $('#enddate').val()
                                }
                            })
                            .done(function(msg) {
                                duration = parseFloat(msg);
                                $('#duration').val(duration + addDays);
                                checkDuration();
                            });
                            
                    }
                    else {
                        $('#duration').val(duration + addDays);
                        checkDuration();
                    }
                    last_startDate = start;
                    last_endDate = end;
                 } else {
                    //Error
                 }
            }
        }   //start and end dates are valid
    }
    
    //Check the entered duration of the leave
    function checkDuration() {
        //Prevent multiple triggers by UI components
        if ((duration != last_duration) || (last_type != $("#type option:selected").val())) {
            if ($("#duration").val() != "") {
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url();?>leaves/credit",
                    data: { id: <?php echo $user_id; ?>, type: $("#type option:selected").text() }
                    })
                    .done(function(msg) {
                        var credit = parseInt(msg);
                        var duration = parseFloat($("#duration").val());
                        if (duration > credit) {
                            $("#lblCreditAlert").show();
                        } else {
                            $("#lblCreditAlert").hide();
                        }
                    });
             }
         }
         last_duration = duration;
         last_type = $("#type option:selected").val();
    }
    
    $(function () {
        $('#startdate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
        $('#enddate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
        
        $('#startdate').change(function() {getLeaveLength();});
        $('#enddate').change(function() {getLeaveLength();});
        $('#startdatetype').change(function() {getLeaveLength();});
        $('#enddatetype').change(function() {getLeaveLength();});
        $('#type').change(function() {checkDuration();});
        
        //Check if the user has not exceed the number of entitled days
        $("#duration").keyup(function() {
            checkDuration();
        });
    });
</script>