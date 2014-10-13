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
$this->lang->load('global', $language);
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

<h2><?php echo lang('contract_calendar_title');?> <span class="muted"><?php echo $name; ?></span></h2>

<div class="row-fluid">
    <div class="span6">
        <a href="<?php echo base_url() . 'contracts/' . $contract_id . '/calendar/' . (intval($year) - 1);?>" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp; <?php echo intval($year) - 1;?></a>
        &nbsp;
        <strong><?php echo $year;?></strong>
        &nbsp;
        <a href="<?php echo base_url() . 'contracts/' . $contract_id . '/calendar/' . (intval($year) + 1);?>" class="btn btn-primary"><?php echo intval($year) + 1;?>&nbsp; <i class="icon-arrow-right icon-white"></i></a>
    </div>
    <div class="span3">
        <a href="<?php echo base_url() . 'contracts';?>" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp; <?php echo lang('contract_calendar_button_back');?></a>
    </div>
    <div class="span3">
        <a href="#frmSetRangeDayOff" class="btn btn-primary" data-toggle="modal"><i class="icon-retweet icon-white"></i>&nbsp; <?php echo lang('contract_calendar_button_series');?></a>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php echo lang('contract_calendar_description');?>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <u><?php echo lang('contract_calendar_legend_title');?></u> <img src='<?php echo base_url();?>assets/images/day.png' /> <?php echo lang('contract_calendar_legend_allday');?>, <img src='<?php echo base_url();?>assets/images/morning.png' /> <?php echo lang('contract_calendar_legend_morning');?>, <img src='<?php echo base_url();?>assets/images/afternoon.png' /> <?php echo lang('contract_calendar_legend_afternoon');?>
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
        echo "<td class='" . $class . " days day" . date("N", $exactDT) . "' data-id='" . $exactDT . "'>" . $i . "<br/><span id='" . $exactDT . "' data-type='" . $type . "' title='" . htmlspecialchars($title, ENT_QUOTES) . "'>" . $image . "</span></td>";
    }
    echo InsertBlankTd($dDaysOnPage - $daysInMonth - date("N", $currentDT) + 1);
    echo "</tr>";
}
?>
</table>

<div id="frmAddDayOff" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmAddDayOff').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('contract_calendar_popup_dayoff_title');?></h3>
    </div>
    <div class="modal-body">
        <label for="txtDayOffTitle"><?php echo lang('contract_calendar_popup_dayoff_field_title');?></label>
        <input type="text" id="txtDayOffTitle" name="txtDayOffTitle" />
        <label for="cboDayOffType"><?php echo lang('contract_calendar_popup_dayoff_field_type');?></label>
        <select id="cboDayOffType" name="cboDayOffType">
            <option value="0" selected><?php echo lang('contract_calendar_popup_dayoff_type_working');?></option>
            <option value="1" selected><?php echo lang('contract_calendar_popup_dayoff_type_off');?></option>
            <option value="2"><?php echo lang('contract_calendar_popup_dayoff_type_morning');?></option>
            <option value="3"><?php echo lang('contract_calendar_popup_dayoff_type_afternoon');?></option>
        </select>
    </div>
    <div class="modal-footer">
        <button id="cmdDeleteDayOff" onclick="delete_day_off();" class="btn btn-danger"><?php echo lang('contract_calendar_popup_dayoff_button_delete');?></button>
        <button onclick="add_day_off();" class="btn secondary"><?php echo lang('contract_calendar_popup_dayoff_button_ok');?></button>
        <button onclick="$('#frmAddDayOff').modal('hide');" class="btn secondary"><?php echo lang('contract_calendar_popup_dayoff_button_cancel');?></button>
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
        <input type="text" id="viz_startdate" name="viz_startdate" required /><br />
        <input type="hidden" name="txtStartDate" id="txtStartDate" /><br />
        <label for="txtEndDate">To</label>
        <input type="text" id="viz_enddate" name="viz_enddate" required /><br />
        <input type="hidden" name="txtEndDate" id="txtEndDate" /><br />
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

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui-1.10.4.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
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
    
        $("#viz_startdate").datepicker({
        changeMonth: true,
        changeYear: true,
        altFormat: "yy-mm-dd",
        altField: "#txtStartDate",
        numberOfMonths: 3,
              onClose: function( selectedDate ) {
                $( "#viz_enddate" ).datepicker( "option", "minDate", selectedDate );
              }
    }, $.datepicker.regional['<?php echo $language_code;?>']);
    $("#viz_enddate").datepicker({
        changeMonth: true,
        changeYear: true,
        altFormat: "yy-mm-dd",
        altField: "#txtEndDate",
        numberOfMonths: 3,
              onClose: function( selectedDate ) {
                $( "#viz_startdate" ).datepicker( "option", "maxDate", selectedDate );
              }
    }, $.datepicker.regional['<?php echo $language_code;?>']);
    
    
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
    
    //Give focus on first field on opening add day off dialog
    $('#frmAddDayOff').on('shown', function () {
        $('input:text:visible:first', this).focus();
    });
});
</script>
