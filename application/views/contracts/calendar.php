<?php
/**
 * This view allows to configure the non-working days for a year and a given contract.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */

$dDaysOnPage = 37;
$dDay = 1;
?>

<h2><?php echo lang('contract_calendar_title');?> <span class="muted"><?php echo $contract_name; ?></span>&nbsp;<?php echo $help;?></h2>

<?php echo $flash_partial_view;?>

<div class="row-fluid">
    <div class="span3">
        <a href="<?php echo base_url() . 'contracts/' . $contract_id . '/calendar/' . (intval($year) - 1);?>" class="btn btn-primary" id="cmdPrevious"><i class="icon-arrow-left icon-white"></i>&nbsp; <?php echo intval($year) - 1;?></a>
        &nbsp;
        <strong><?php echo $year;?></strong>
        &nbsp;
        <a href="<?php echo base_url() . 'contracts/' . $contract_id . '/calendar/' . (intval($year) + 1);?>" class="btn btn-primary" id="cmdNext"><?php echo intval($year) + 1;?>&nbsp; <i class="icon-arrow-right icon-white"></i></a>
    </div>
    <div class="span2">
        <a href="<?php echo base_url() . 'contracts';?>" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp; <?php echo lang('contract_calendar_button_back');?></a>
    </div>
    <div class="span4">
        <button id="cmdImportCalendar" class="btn btn-primary"><i class="icon-calendar icon-white"></i>&nbsp; <?php echo lang('contract_calendar_button_import');?></button>&nbsp;
        <a href="#frmSetRangeDayOff" class="btn btn-primary" data-toggle="modal"><i class="icon-retweet icon-white"></i>&nbsp; <?php echo lang('contract_calendar_button_series');?></a>
    </div>
    <div class="span3">
        <?php if (!empty($contracts)) { ?>
        <select name="contract" id="contract" class="selectized input-large">
        <?php foreach ($contracts as $contract): ?>
            <option value="<?php echo $contract['id'] ?>"><?php echo $contract['name']; ?></option>
        <?php endforeach ?>
        </select>
        <button id="cmdContractCopy" class="btn btn-primary"><i class="fa fa-copy"></i>&nbsp; <?php echo lang('contract_calendar_button_copy');?></button>
        <?php } ?>
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
    <div class="span6">
        <u><?php echo lang('contract_calendar_legend_title');?></u> <img src='<?php echo base_url();?>assets/images/day.png' /> <?php echo lang('contract_calendar_legend_allday');?>, <img src='<?php echo base_url();?>assets/images/morning.png' /> <?php echo lang('contract_calendar_legend_morning');?>, <img src='<?php echo base_url();?>assets/images/afternoon.png' /> <?php echo lang('contract_calendar_legend_afternoon');?>
    </div>
    <div class="span6">
        <?php if ($this->config->item('ics_enabled') == FALSE) {?>
        &nbsp;
        <?php } else {?>
        <span class="pull-right"><a id="lnkICS" href="#"><i class="icon-globe"></i> ICS</a></span>
        <?php }?>
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
 * Insert blank cells into a table row
 * @param int $numberOfTdsToAdd number of cells
 * @return string HTML code for empty cells
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
    echo "<tr><td class='monthName'><div>" . lang(date("F", $currentDT)) . "</div></td>";
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

<div class="row-fluid"><div class="span12"></div></div>

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
        <button onclick="add_day_off();" class="btn"><?php echo lang('contract_calendar_popup_dayoff_button_ok');?></button>
        <button onclick="$('#frmAddDayOff').modal('hide');" class="btn"><?php echo lang('contract_calendar_popup_dayoff_button_cancel');?></button>
    </div>
</div>

<div id="frmSetRangeDayOff" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSetRangeDayOff').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('contract_calendar_popup_series_title');?></h3>
    </div>
    <div class="modal-body">
        <label for="cboDayOffSeriesDay"><?php echo lang('contract_calendar_popup_series_field_occurences');?></label>
        <select name="cboDayOffSeriesDay" id="cboDayOffSeriesDay">
            <option value="saturday" selected><?php echo lang('Saturday');?></option>
            <option value="sunday"><?php echo lang('Sunday');?></option>
            <option value="monday"><?php echo lang('Monday');?></option>
            <option value="tuesday"><?php echo lang('Tuesday');?></option>
            <option value="wednesday"><?php echo lang('Wednesday');?></option>
            <option value="thursday"><?php echo lang('Thursday');?></option>
            <option value="friday"><?php echo lang('Friday');?></option>
            <option value="all"><?php echo lang('All days');?></option>
        </select>
        <label for="txtStartDate"><?php echo lang('contract_calendar_popup_series_field_from');?></label>
        <div class="input-append">
                <input type="text" id="viz_startdate" name="viz_startdate" required />
                <button class="btn" onclick="set_current_period();"><?php echo lang('contract_calendar_popup_series_button_current');?></button>
            </div><br />
        <input type="hidden" name="txtStartDate" id="txtStartDate" /><br />
        <label for="txtEndDate"><?php echo lang('contract_calendar_popup_series_field_to');?></label>
        <input type="text" id="viz_enddate" name="viz_enddate" required /><br />
        <input type="hidden" name="txtEndDate" id="txtEndDate" /><br />
        <label for="cboDayOffSeriesType"><?php echo lang('contract_calendar_popup_series_field_as');?></label>
        <select id="cboDayOffSeriesType" name="cboDayOffType">
            <option value="0" selected><?php echo lang('contract_calendar_popup_series_field_as_working');?></option>
            <option value="1" selected><?php echo lang('contract_calendar_popup_series_field_as_off');?></option>
            <option value="2"><?php echo lang('contract_calendar_popup_series_field_as_morning');?></option>
            <option value="3"><?php echo lang('contract_calendar_popup_series_field_as_afternnon');?></option>
        </select>
        <br />
        <label for="cboDayOffSeriesTitle"><?php echo lang('contract_calendar_popup_series_field_title');?></label>
        <input type="text" id="cboDayOffSeriesTitle" name="cboDayOffSeriesTitle" />
    </div>
    <div class="modal-footer">
        <a href="#" onclick="edit_series();" class="btn"><?php echo lang('contract_calendar_popup_series_button_ok');?></a>
        <a href="#" onclick="$('#frmSetRangeDayOff').modal('hide');" class="btn"><?php echo lang('contract_calendar_popup_series_button_cancel');?></a>
    </div>
</div>

<div id="frmLinkICS" class="modal hide fade">
    <div class="modal-header">
        <h3>ICS<a href="#" onclick="$('#frmLinkICS').modal('hide');" class="close">&times;</a></h3>
    </div>
    <div class="modal-body" id="frmSelectDelegateBody">
        <div class='input-append'>
                <input type="text" class="input-xlarge" id="txtIcsUrl" onfocus="this.select();" onmouseup="return false;" 
                    value="<?php echo base_url() . 'ics/dayoffs/' . $user_id . '/' . $contract_id;?>" />
                 <button id="cmdCopy" class="btn" data-clipboard-text="<?php echo base_url() . 'ics/dayoffs/' . $user_id . '/' . $contract_id;?>">
                     <i class="fa fa-clipboard"></i>
                 </button>
                <a href="#" id="tipCopied" data-toggle="tooltip" title="<?php echo lang('copied');?>" data-placement="right" data-container="#cmdCopy"></a>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmLinkICS').modal('hide');" class="btn btn-primary"><?php echo lang('OK');?></a>
    </div>
</div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/selectize.bootstrap2.css" />
<script type="text/javascript" src="<?php echo base_url();?>assets/js/selectize.min.js"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script src="<?php echo base_url();?>assets/js/clipboard-1.6.1.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.pers-brow.js"></script>
<script type="text/javascript">
    var timestamp;
    //Global locale for moment objects
    moment.locale('<?php echo $language_code;?>', {longDateFormat : {L : '<?php echo lang('global_date_momentjs_format');?>'}});

    //Compute the end and start dates of the civil year being displayed
    function set_current_period() {
        var startEntDate = moment();
        var endEntDate = moment();

        //Compute boundaries
        startEntDate.year(<?php echo $year;?>);
        startEntDate.month(0);
        startEntDate.date(1);
        endEntDate.year(<?php echo $year;?>);
        endEntDate.month(11);
        endEntDate.date(31);

        //Presentation for DB and Human
        startEntDate.locale('<?php echo $language_code;?>');
        endEntDate.locale('<?php echo $language_code;?>');
        $("#txtStartDate").val(startEntDate.format("YYYY-MM-DD"));
        $("#txtEndDate").val(endEntDate.format("YYYY-MM-DD"));
        $("#viz_startdate").val(startEntDate.format("L"));
        $("#viz_enddate").val(endEntDate.format("L"));
    }

//Add a day off by an Ajax query
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

//Delete a day off by an Ajax query
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

//Edit a serie of days off by an Ajax query
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

//On load
$(function() {
<?php if ($this->config->item('csrf_protection') == TRUE) {?>
    $.ajaxSetup({
        data: {
            <?php echo $this->security->get_csrf_token_name();?>: "<?php echo $this->security->get_csrf_hash();?>",
        }
    });
<?php }?>
    $("#frmAddDayOff").alert();
    $("#frmSetRangeDayOff").alert();
    $('#contract').selectize();
    
        $("#viz_startdate").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: '<?php echo lang('global_date_js_format');?>',
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
        dateFormat: '<?php echo lang('global_date_js_format');?>',
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
    
    //Copy a contract (if a source contract was selected)
    $("#cmdContractCopy").on("click", function() {
        if ((!$("#contract option:selected").length) || ($("#contract option:selected").text() == '')) {
            bootbox.alert('<?php echo lang('contract_calendar_copy_destination_js_msg');?>');
        } else {
            document.location = '<?php echo base_url() . 'contracts/' . $contract_id . '/calendar/' . $year . '/copy/';?>' + $("#contract").val();
        }
    });
    
    //Import an iCalendar feed by using its URL
    $("#cmdImportCalendar").on("click", function() {
        var last_imported = '';
        if($.cookie('import_ical_url') != null) {
            last_imported = $.cookie('import_ical_url');
        }
        bootbox.prompt(
        "<?php echo lang('contract_calendar_prompt_import');?>",
        "<?php echo lang('Cancel');?>",
        "<?php echo lang('OK');?>",
        function(result) {
            if (result === null) {
              //NOP
            } else {
              //http://localhost/import.ics
              //Ajax call to ics import function (using SabreDAV/VObject
              $.ajax({
                  url: "<?php echo base_url(); ?>contracts/calendar/import",
                  type: "POST",
                  data: { 
                          contract: <?php echo $contract_id; ?>,
                          url: result
                      }
                }).done(function( msg ) {
                      //If no error, reload the page and save last used URL
                      if (msg == "") {
                          $.cookie('import_ical_url', result);
                          location.reload(true);
                      } else {
                          bootbox.alert(msg);
                      }
                  });
            }
          },
          last_imported
        );
    });

    //Copy/Paste ICS Feed
    var client = new Clipboard("#cmdCopy");
    $('#lnkICS').click(function () {
        $("#frmLinkICS").modal('show');
    });
    client.on( "success", function() {
        $('#tipCopied').tooltip('show');
        setTimeout(function() {$('#tipCopied').tooltip('hide')}, 1000);
    });
});
</script>
