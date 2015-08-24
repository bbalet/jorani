<?php
/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jorani is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 */
?>

<h1><?php echo lang('calendar_individual_title');?> &nbsp;<?php echo $help;?></h1>

<div class="row-fluid">
    <div class="span12"><?php echo lang('calendar_individual_description');?></div>
</div>

<div class="row-fluid">
    <div class="span6">
        <button id="cmdPrevious" class="btn btn-primary"><i class="icon-chevron-left icon-white"></i></button>
        <button id="cmdToday" class="btn btn-primary"><?php echo lang('calendar_component_buttonText_today');?></button>
        <button id="cmdNext" class="btn btn-primary"><i class="icon-chevron-right icon-white"></i></button>
    </div>
    <div class="span6">
        <div class="pull-right">
            <button id="cmdDisplayDayOff" class="btn btn-primary"><i class="icon-calendar icon-white"></i>&nbsp;<?php echo lang('calendar_individual_day_offs');?></button>
        </div>
    </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div class="row-fluid">
    <div class="span2"><span class="label"><?php echo lang('Planned');?></span></div>
    <div class="span2"><span class="label label-success"><?php echo lang('Accepted');?></span></div>
    <div class="span2"><span class="label label-warning"><?php echo lang('Requested');?></span></div>
    <div class="span2"><span class="label label-important" style="background-color: #ff0000;"><?php echo lang('Rejected');?></span></div>
    <div class="span4">
        <?php if ($this->config->item('ics_enabled') == FALSE) {?>
        &nbsp;
        <?php } else {?>
        <span class="pull-right"><a id="lnkICS" href="#"><i class="icon-globe"></i> ICS</a></span>
        <?php }?>        
    </div>
</div>

<div id='calendar'></div>

<div id="frmEvent" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmEvent').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('calendar_individual_popup_event_title');?></h3>
    </div>
    <div class="modal-body">
        <a href="#" id="lnkDownloadCalEvnt"><?php echo lang('calendar_individual_popup_event_link_ical');?></a> <?php echo lang('calendar_individual_popup_event_link_ical_description');?>
        
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmEvent').modal('hide');" class="btn secondary"><?php echo lang('calendar_individual_popup_event_button_close');?></a>
    </div>
</div>

<div class="modal hide" id="frmModalAjaxWait" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <h1><?php echo lang('global_msg_wait');?></h1>
        </div>
        <div class="modal-body">
            <img src="<?php echo base_url();?>assets/images/loading.gif"  align="middle">
        </div>
 </div>

<div id="frmLinkICS" class="modal hide fade">
    <div class="modal-header">
        <h3>ICS<a href="#" onclick="$('#frmLinkICS').modal('hide');" class="close">&times;</a></h3>
    </div>
    <div class="modal-body" id="frmSelectDelegateBody">
        <div class='input-append'>
                <input type="text" class="input-xlarge" id="txtIcsUrl" onfocus="this.select();" onmouseup="return false;" 
                    value="<?php echo base_url() . 'ics/individual/' . $user_id;?>" />
                 <button id="cmdCopy" class="btn" data-clipboard-text="<?php echo base_url() . 'ics/individual/' . $user_id;?>">
                     <i class="icon-magnet"></i>
                 </button>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmLinkICS').modal('hide');" class="btn btn-primary"><?php echo lang('OK');?></a>
    </div>
</div>

<link href="<?php echo base_url();?>assets/fullcalendar/fullcalendar.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/lib/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/lang/<?php echo $language_code;?>.js"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script src="<?php echo base_url();?>assets/js/ZeroClipboard.min.js"></script>
<script type="text/javascript">
    var toggleDayoffs = false;
    
    //Refresh the calendar if data is available
    function refresh_calendar() {
        source = '<?php echo base_url();?>leaves/individual';;
        $('#calendar').fullCalendar('removeEvents');
        $('#calendar').fullCalendar('addEventSource', source);
        $('#calendar').fullCalendar('removeEventSource', source);
        source = '<?php echo base_url();?>contracts/calendar/userdayoffs';
        if (toggleDayoffs) {
            $('#calendar').fullCalendar('removeEventSource', source);
            $('#calendar').fullCalendar('addEventSource', source);
            $('#calendar').fullCalendar('rerenderEvents');
            $('#calendar').fullCalendar('removeEventSource', source);
        } else {
            $('#calendar').fullCalendar('removeEventSource', source);
        }
    }
    
$(function () {
    
    //Global Ajax error handling mainly used for session expiration
    $( document ).ajaxError(function(event, jqXHR, settings, errorThrown) {
        $('#frmModalAjaxWait').modal('hide');
        if (jqXHR.status == 401) {
            bootbox.alert("<?php echo lang('global_ajax_timeout');?>", function() {
                //After the login page, we'll be redirected to the current page 
               location.reload();
            });
        } else { //Oups
            bootbox.alert("<?php echo lang('global_ajax_error');?>");
        }
      });
    
    $("#frmEvent").alert();

    $('#calendar').fullCalendar({
        timeFormat: ' ', /*Trick to remove the start time of the event*/
        header: {
            left: "",
            center: "title",
            right: ""
        },
        events: '<?php echo base_url();?>leaves/individual',
        eventClick: function(calEvent, jsEvent, view) {
            if (calEvent.color != '#000000') {
                var link = "<?php echo base_url();?>ics/ical/" + calEvent.id;
                $("#lnkDownloadCalEvnt").attr('href', link);
                $('#frmEvent').modal('show');
            }
        },
        loading: function(isLoading) {
            if (isLoading) { //Display/Hide a pop-up showing an animated icon during the Ajax query.
                $('#frmModalAjaxWait').modal('show');
            } else {
                $('#frmModalAjaxWait').modal('hide');
            }    
        }
    });
    
    //Prevent to load always the same content (refreshed each time)
    $('#frmEvent').on('hidden', function() {
        $(this).removeData('modal');
    });
    
    //Toggle day offs displays
    $('#cmdDisplayDayOff').on('click', function() {
        toggleDayoffs = !toggleDayoffs;
        refresh_calendar();
    });
    
    $('#cmdNext').click(function() {
        $('#calendar').fullCalendar('next');
        if (toggleDayoffs) refresh_calendar();
    });

    $('#cmdPrevious').click(function() {
        $('#calendar').fullCalendar('prev');
        if (toggleDayoffs) refresh_calendar();
    });

    $('#cmdToday').click(function() {
        $('#calendar').fullCalendar('today');
        if (toggleDayoffs) refresh_calendar();
    });
    
    //Copy/Paste ICS Feed
    var client = new ZeroClipboard($("#cmdCopy"));
    $('#lnkICS').click(function () {
        $("#frmLinkICS").modal('show');
    });
});
</script>
