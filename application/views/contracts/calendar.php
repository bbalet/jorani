<?php
CI_Controller::get_instance()->load->helper('language');
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
background:#9F0;
border-color:#000;
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
        <a href="<?php echo base_url();?>contracts/dayoff" class="btn btn-primary" data-target="#frmSetRangeDayOff" data-toggle="modal"><i class="icon-retweet icon-white"></i>&nbsp; Set many days off</a>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
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
        /*if ($i == date("d") && date("m", $currentDT) == date("m") && $year == date("Y")) {
            $class = "currentDay";
        } else {
            $class = "";
        }*/
        $class = "";
        $type = 0; //0 working, 1 off, 2 morning working, 3 afternoon working
        $image= "&nbsp;";
        switch ($type) {
            case 1: $image= "<img src='" . base_url() . "assets/images/day.png' />"; break;
            case 2: $image= "<img src='" . base_url() . "assets/images/morning.png' />"; break;
            case 3: $image= "<img src='" . base_url() . "assets/images/afternoon.png' />"; break;
        }
        echo "<td class='" . $class . " days day" . date("N", $exactDT) . "' data-id='" . $exactDT . "' data-type='" . $type . "'>" . $i . "<br/>" . $image . "</td>";
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
    <label for="cboDayOffType">Set day off</label>
    <select id="cboDayOffType" name="cboDayOffType">
        <option value="0" selected>All day</option>
        <option value="1">Morning</option>
        <option value="2">Afternoon</option>
    </select>
    <span id="timestamp"></span>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmAddDayOff').modal('hide');" class="btn secondary">Cancel</a>
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
$(function() {
    $("#frmAddDayOff").alert();
    $('#enddate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    
    //Display modal form that allow adding a day off
    $("#fullyear").on("click", "td", function() {
        $val = $(this).data("id");
        if ($val != 0) {
            $('#frmAddDayOff').modal('show');
            $("#timestamp").text($val);
        }
    });
    
        //Prevent to load always the same content (refreshed each time)
    $('#frmAddDayOff').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>
