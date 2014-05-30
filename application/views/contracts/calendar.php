<?php
$this->load->helper('language');
$this->lang->load('calendar', $language);

$dDaysOnPage = 37;
$dDay = 1;
?>

<style type="text/css" media="all">
.currentDay {
background:#FFC;
color:red;
}
.days:hover {
background:#999;
border-color:#000;
cursor:pointer;
}
.day6 {
background:#ECECFF;
}
.day7 {
background:#ECECFF;
}
.monthName {
text-align:left;
vertical-align:middle;
}
.monthName div {
padding-left:10px;
}
</style>


<div class="row-fluid">
    <div class="span6">
        <a href="<?php echo base_url() . 'contracts/' . $contract_id . '/calendar/' . (intval($year) - 1);?>" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp; <?php echo intval($year) - 1;?></a>
        &nbsp;
        <strong><?php echo $year;?></strong>
        &nbsp;
        <a href="<?php echo base_url() . 'contracts/' . $contract_id . '/calendar/' . (intval($year) + 1);?>" class="btn btn-primary"><?php echo intval($year) + 1;?>&nbsp; <i class="icon-arrow-right icon-white"></i></a>
    </div>
    <div class="span6">
        <a href="<?php echo base_url();?>contracts/dayoff" class="btn btn-primary" data-target="#frmSetRangeDayOff" data-toggle="modal"><i class="icon-retweet icon-white"></i>&nbsp; Series of non working days</a>
        &nbsp;
        <button class="btn secondary btn-danger" onclick="toggle_delete_mode();" id="cmdDelete">Delete mode</button>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span12">
        Day offs and weekends are not configured by default. Click on a day to edit it individually or use the button "Series".</div>
</div>

<table width="100%" border="1" cellspacing="0" cellpadding="0" id="fullyear">
    <tr>
        <th><?php echo $year; ?></th>
        <th><?php echo lang('calendar_monday_short');?></th>
        <th><?php echo lang('calendar_tuesday_short');?></th>
        <th><?php echo lang('calendar_wednesday_short');?></th>
        <th><?php echo lang('calendar_thursday_short');?></th>
        <th><?php echo lang('calendar_friday_short');?></th>
        <th><?php echo lang('calendar_saturday_short');?></th>
        <th><?php echo lang('calendar_sunday_short');?></th>
        <th><?php echo lang('calendar_monday_short');?></th>
        <th><?php echo lang('calendar_tuesday_short');?></th>
        <th><?php echo lang('calendar_wednesday_short');?></th>
        <th><?php echo lang('calendar_thursday_short');?></th>
        <th><?php echo lang('calendar_friday_short');?></th>
        <th><?php echo lang('calendar_saturday_short');?></th>
        <th><?php echo lang('calendar_sunday_short');?></th>
        <th><?php echo lang('calendar_monday_short');?></th>
        <th><?php echo lang('calendar_tuesday_short');?></th>
        <th><?php echo lang('calendar_wednesday_short');?></th>
        <th><?php echo lang('calendar_thursday_short');?></th>
        <th><?php echo lang('calendar_friday_short');?></th>
        <th><?php echo lang('calendar_saturday_short');?></th>
        <th><?php echo lang('calendar_sunday_short');?></th>
        <th><?php echo lang('calendar_monday_short');?></th>
        <th><?php echo lang('calendar_tuesday_short');?></th>
        <th><?php echo lang('calendar_wednesday_short');?></th>
        <th><?php echo lang('calendar_thursday_short');?></th>
        <th><?php echo lang('calendar_friday_short');?></th>
        <th><?php echo lang('calendar_saturday_short');?></th>
        <th><?php echo lang('calendar_sunday_short');?></th>
        <th><?php echo lang('calendar_monday_short');?></th>
        <th><?php echo lang('calendar_tuesday_short');?></th>
        <th><?php echo lang('calendar_wednesday_short');?></th>
        <th><?php echo lang('calendar_thursday_short');?></th>
        <th><?php echo lang('calendar_friday_short');?></th>
        <th><?php echo lang('calendar_saturday_short');?></th>
        <th><?php echo lang('calendar_sunday_short');?></th>
        <th><?php echo lang('calendar_monday_short');?></th>
        <th><?php echo lang('calendar_tuesday_short');?></th>
    </tr>

<?php

function InsertBlankTd($numberOfTdsToAdd) {
    $tdString = '';
    for($i=1;$i<=$numberOfTdsToAdd;$i++) {
        $tdString .= "<td data-id='0'></td>";
    }
    return $tdString;
}

for ($mC = 1; $mC <= 12; $mC++) {
    $currentDT = mktime(0, 0, 0, $mC, $dDay, $year);
    echo "<tr><td class='monthName'><div>" . date("F", $currentDT) . "</div></td>";
    $daysInMonth = date("t", $currentDT);

    echo InsertBlankTd(date("N", $currentDT) - 1);

    for ($i = 1; $i <= $daysInMonth; $i++) {
        $exactDT = mktime(0, 0, 0, $mC, $i, $year);
        $class = "";
        $type = isset($dayoffs[$exactDT]) ? $dayoffs[$exactDT][0] : 0; //0 working, 1 off, 2 morning working, 3 afternoon working
        $title = isset($dayoffs[$exactDT]) ? $dayoffs[$exactDT][1] : '';
        $image= "&nbsp;";
        switch ($type) {
            case 1: $image= "<img src='" . base_url() . "assets/images/day.png' />"; break;
            case 2: $image= "<img src='" . base_url() . "assets/images/morning.png' />"; break;
            case 3: $image= "<img src='" . base_url() . "assets/images/afternoon.png' />"; break;
        }
        echo "<td class='" . $class . " days day" . date("N", $exactDT) . "' data-id='" . $exactDT . "'>" . $i . "<br/><span id='" . $exactDT . "' data-type='" . $type . "' title='" . $title . "'>" . $image . "</span></td>";
    }
    echo InsertBlankTd($dDaysOnPage - $daysInMonth - date("N", $currentDT) + 1);
    echo "</tr>";
}
?>
</table>

<div id="frmAddDayOff" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmAddDayOff').modal('hide');" class="close">&times;</a>
         <h3>Add day off</h3>
    </div>
    <div class="modal-header">
        <label for="txtDayOffTitle">Title</label>
        <input type="text" id="txtDayOffTitle" name="txtDayOffTitle" />
        <label for="cboDayOffType">Type</label>
        <select id="cboDayOffType" name="cboDayOffType">
            <option value="1" selected>All day</option>
            <option value="2">Morning</option>
            <option value="3">Afternoon</option>
        </select>
        <span id="timestamp"></span>
    </div>
    <div class="modal-footer">
        <button onclick="add_day_off();" class="btn secondary">OK</button>
        <button onclick="$('#frmAddDayOff').modal('hide');" class="btn secondary">Cancel</button>
    </div>
</div>

<div id="frmSetRangeDayOff" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSetRangeDayOff').modal('hide');" class="close">&times;</a>
         <h3>Add day off</h3>
    </div>
    Mark every
    <select id="cboDayOffType">
        <option value="" selected>Saturday and sunday</option>
        <option value="" selected>Saturday afternoon and sunday</option>
        <option value="" selected>Saturday</option>
        <option value="" selected>Sunday</option>
        <option value="" selected>Monday</option>
        <option value="" selected>Tuesday</option>
        <option value="" selected>Wednesday</option>
        <option value="" selected>Thursday</option>
        <option value="" selected>Friday</option>
        <option value="" selected>Sunday</option>
        
        <option value="" selected>Saturday morning</option>
        <option value="" selected>Sunday morning</option>
        <option value="" selected>Monday morning</option>
        <option value="" selected>Tuesday morning</option>
        <option value="" selected>Wednesday morning</option>
        <option value="" selected>Thursday morning</option>
        <option value="" selected>Friday morning</option>
        <option value="" selected>Sunday morning</option>
        
        <option value="" selected>Saturday afternoon</option>
        <option value="" selected>Sunday afternoon</option>
        <option value="" selected>Monday afternoon</option>
        <option value="" selected>Tuesday afternoon</option>
        <option value="" selected>Wednesday afternoon</option>
        <option value="" selected>Thursday afternoon</option>
        <option value="" selected>Friday afternoon</option>
        <option value="" selected>Sunday afternoon</option>
    </select>
    until 
    <input type="text" id="enddate" />
    as a non working day
    <span id="timestamp"></span>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmSetRangeDayOff').modal('hide');" class="btn secondary">Apply</a>
        <a href="#" onclick="$('#frmSetRangeDayOff').modal('hide');" class="btn secondary">Cancel</a>
    </div>
</div>

<link href="<?php echo base_url();?>assets/datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<!--Avoid datepicker to appear behind the modal form//-->
<style>
    .datepicker{z-index:1151 !important;}
</style>
<script type="text/javascript">

var timestamp;
var delete_mode = false;

function toggle_delete_mode() {
    delete_mode = !delete_mode;
    if (delete_mode) {
        $(".days:hover").css("cursor", "crosshair");
        $("#cmdDelete").html("Create mode");
    } else {
        $(".days:hover").css("cursor", "pointer");
        $("#cmdDelete").html("Delete mode");
    }
}

function add_day_off() {
    $("#cboType").val($('#' + timestamp).data("type"));
    $.ajax({
        url: "<?php echo base_url();?>contracts/calendar/add",
        type: "POST",
        data: { contract: <?php echo $contract_id;?>,
                timestamp: timestamp,
                type: $("#cboDayOffType").val(),
                title: $("#txtDayOffTitle").val()
            }
      }).done(function( msg ) {
            var image;
            switch ($("#cboDayOffType").val()) {
                case "1": image= "<img src='<?php echo base_url();?>assets/images/day.png' />"; break;
                case "2": image= "<img src='<?php echo base_url();?>assets/images/morning.png' />"; break;
                case "3": image= "<img src='<?php echo base_url();?>assets/images/afternoon.png' />"; break;
            }
            $('#' + timestamp).html(image);
            $('#frmAddDayOff').modal('hide');
        });
}

function delete_day_off() {
    $.ajax({
        url: "<?php echo base_url();?>contracts/calendar/delete",
        type: "POST",
        data: { contract: <?php echo $contract_id;?>,
                timestamp: timestamp
            }
      }).done(function( msg ) {
            $('#' + timestamp).html("&nbsp;");
            $('#frmAddDayOff').modal('hide');
        });
}
    
$(function() {
    $("#frmAddDayOff").alert();
    $('#enddate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    
    //Display modal form that allow adding a day off
    $("#fullyear").on("click", "td", function() {
        timestamp = $(this).data("id");
        switch ($('#' + timestamp).data("type")) {
            case 0:
                $("#txtDayOffTitle").val('');
                $("#cmdDelete").hide();
                break;
            case 1:
            case 2:
            case 3:
                $("#cmdDelete").show();
                $('#cboDayOffType option[value="' + $('#' + timestamp).data("type") + '"]').prop('selected', true);
                $("#txtDayOffTitle").val($('#' + timestamp).attr("title"));
                break;
        }
        if (timestamp != 0) {
            $('#frmAddDayOff').modal('show');
        }
    });
    
    //Prevent to load always the same content
    $('#frmAddDayOff').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>
