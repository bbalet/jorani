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

$this->load->helper('language');
$this->lang->load('calendar', $language);
$this->lang->load('contract', $language);

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
        <a href="#frmSetRangeDayOff" class="btn btn-primary" data-toggle="modal"><i class="icon-retweet icon-white"></i>&nbsp; Series of non working days</a>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span12">
        Day offs and weekends are not configured by default. Click on a day to edit it individually or use the button "Series".
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <u>Legend:</u> <img src='<?php echo base_url();?>assets/images/day.png' /> All day, <img src='<?php echo base_url();?>assets/images/morning.png' /> Morning, <img src='<?php echo base_url();?>assets/images/afternoon.png' /> Afternoon
    </div>
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

/**
 * Insert a blank cell for a working day
 * @param type $numberOfTdsToAdd
 * @return string
 */
function InsertBlankTd($numberOfTdsToAdd) {
    $tdString = '';
    for($i=1;$i<=$numberOfTdsToAdd;$i++) {
        $tdString .= "<td data-id='0'></td>";
    }
    return $tdString;
}

//This loop creates the calendar displayed on the page
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
         <h3>Edit day off</h3>
    </div>
    <div class="modal-body">
        <label for="txtDayOffTitle">Title</label>
        <input type="text" id="txtDayOffTitle" name="txtDayOffTitle" />
        <label for="cboDayOffType">Type</label>
        <select id="cboDayOffType" name="cboDayOffType">
            <option value="0" selected>Working day</option>
            <option value="1" selected>All day is off</option>
            <option value="2">Morning is off</option>
            <option value="3">Afternoon is off</option>
        </select>
        <span id="timestamp"></span>
    </div>
    <div class="modal-footer">
        <button id="cmdDeleteDayOff" onclick="delete_day_off();" class="btn btn-danger">Delete</button>
        <button onclick="add_day_off();" class="btn secondary">OK</button>
        <button onclick="$('#frmAddDayOff').modal('hide');" class="btn secondary">Cancel</button>
    </div>
</div>

<div id="frmSetRangeDayOff" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSetRangeDayOff').modal('hide');" class="close">&times;</a>
         <h3>Edit a series of day offs</h3>
    </div>
    <div class="modal-body">
        <label for="cboDayOffSeriesDay">Mark every</label>
        <select name="cboDayOffSeriesDay" id="cboDayOffSeriesDay">
            <option value="saturday" selected><?php echo lang('Saturday');?></option>
            <option value="sunday"><?php echo lang('Sunday');?></option>
            <option value="monday"><?php echo lang('Monday');?></option>
            <option value="tuesday"><?php echo lang('Tuesday');?></option>
            <option value="wednesday"><?php echo lang('Wednesday');?></option>
            <option value="thursday"><?php echo lang('Thursday');?></option>
            <option value="friday"><?php echo lang('Friday');?></option>
        </select>
        <label for="txtStartDate">From</label>
        <input name="txtStartDate" id="txtStartDate" type="text" /><br />
        <label for="txtEndDate">To</label>
        <input name="txtEndDate" id="txtEndDate" type="text" /><br />
        <label for="cboDayOffSeriesType">As a</label>
        <select id="cboDayOffSeriesType" name="cboDayOffType">
            <option value="0" selected>Working day</option>
            <option value="1" selected>All day is off</option>
            <option value="2">Morning is off</option>
            <option value="3">Afternoon is off</option>
        </select>
        <br />
        <label for="cboDayOffSeriesTitle">Title</label>
        <input type="text" id="cboDayOffSeriesTitle" name="cboDayOffSeriesTitle" />
    </div>
    <div class="modal-footer">
        <a href="#" onclick="edit_series();" class="btn secondary">OK</a>
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

function add_day_off() {
    $("#cboType").val($('#' + timestamp).data("type"));
    $.ajax({
        url: "<?php echo base_url();?>contracts/calendar/edit",
        type: "POST",
        data: { contract: <?php echo $contract_id;?>,
                timestamp: timestamp,
                type: $("#cboDayOffType").val(),
                title: $("#txtDayOffTitle").val()
            }
      }).done(function( msg ) {
            var image = "&nbsp;";
            switch ($("#cboDayOffType").val()) {
                case "1": image= "<img src='<?php echo base_url();?>assets/images/day.png' />"; break;
                case "2": image= "<img src='<?php echo base_url();?>assets/images/morning.png' />"; break;
                case "3": image= "<img src='<?php echo base_url();?>assets/images/afternoon.png' />"; break;
            }
            $('#' + timestamp).html(image);
            $('#' + timestamp).attr("title", $("#txtDayOffTitle").val());
            $('#frmAddDayOff').modal('hide');
        });
}

function delete_day_off() {
    $.ajax({
        url: "<?php echo base_url();?>contracts/calendar/edit",
        type: "POST",
        data: { contract: <?php echo $contract_id;?>,
                timestamp: timestamp,
                type: 0,
                title: ""
            }
      }).done(function( msg ) {
            $('#' + timestamp).html("&nbsp;");
            $('#' + timestamp).attr("title", "");
            $('#frmAddDayOff').modal('hide');
        });
}

function edit_series() {
    $("#cboType").val($('#' + timestamp).data("type"));
    $.ajax({
        url: "<?php echo base_url();?>contracts/calendar/series",
        type: "POST",
        data: { contract: <?php echo $contract_id;?>,
                start: $("#txtStartDate").val(),
                end: $("#txtEndDate").val(),
                day: $("#cboDayOffSeriesDay").val(),
                type: $("#cboDayOffSeriesType").val(),
                title: $("#cboDayOffSeriesTitle").val()
            }
      }).done(function( msg ) {
            //Reload the page
            location.reload(true);
        });
}

$(function() {
    $("#frmAddDayOff").alert();
    $("#frmSetRangeDayOff").alert();
    $('#txtStartDate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    $('#txtEndDate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    
    //Display modal form that allow adding a day off
    $("#fullyear").on("click", "td", function() {
        timestamp = $(this).data("id");
        switch ($('#' + timestamp).data("type")) {
            case 0:
                $("#txtDayOffTitle").val('');
                $("#cmdDeleteDayOff").hide();
                break;
            case 1:
            case 2:
            case 3:
                $("#cmdDeleteDayOff").show();
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
