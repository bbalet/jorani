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

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('calendar', $language);
$this->lang->load('global', $language);?>

<link rel="stylesheet" href="<?php echo base_url();?>assets/font-awesome/css/font-awesome.min.css">
<h1><?php echo lang('calendar_organization_title');?><?php echo $help;?></h1>

<div class="row-fluid">
    <div class="span4">
        <label for="txtEntity"><?php echo lang('calendar_organization_field_select_entity');?></label>
        <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" readonly />
        <button id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('calendar_organization_button_select_entity');?></button>
        </div>
    </div>
    <div class="span3">
        <label class="checkbox">
            <input type="checkbox" value="" id="chkIncludeChildren"> <?php echo lang('calendar_organization_check_include_subdept');?>
        </label>
    </div>
    <?php if ($this->config->item('ics_enabled') == TRUE) {?>
    <div class="span5 pull-right"><a id="lnkICS" href="#"><i class="icon-globe"></i> ICS</a></div>
    <?php } else {?>
    <div class="span5">&nbsp;</div>
    <?php }?>
    
</div>

<div class="row-fluid">
    <div class="span4">
        <button id="cmdPrevious" class="btn btn-primary"><i class="icon-chevron-left icon-white"></i></button>
        <button id="cmdToday" class="btn btn-primary"><?php echo lang('calendar_component_buttonText_today');?></button>
        <button id="cmdNext" class="btn btn-primary"><i class="icon-chevron-right icon-white"></i></button>
    </div>
    <div class="span2">
        <button id="cmdDisplayDayOff" class="btn btn-primary"><i class="icon-calendar icon-white"></i>&nbsp;<?php echo lang('calendar_individual_day_offs');?></button>
    </div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><span class="label"><?php echo lang('Planned');?></span></div>
    <div class="span3"><span class="label label-success"><?php echo lang('Accepted');?></span></div>
    <div class="span3"><span class="label label-warning"><?php echo lang('Requested');?></span></div>
    <div class="span3">&nbsp;</div>
</div>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('calendar_organization_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn secondary"><?php echo lang('calendar_organization_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn secondary"><?php echo lang('calendar_organization_popup_entity_button_cancel');?></a>
    </div>
</div>

<div id='calendar'></div>

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
                    value="" />
                 <button id="cmdCopy" class="btn" data-clipboard-text="">
                     <i class="fa fa-clipboard"></i>
                 </button>
                <a href="#" id="tipCopied" data-toggle="tooltip" title="copied" data-placement="right" data-container="#cmdCopy"></a>
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
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.pers-brow.js"></script>
<script src="<?php echo base_url();?>assets/js/ZeroClipboard.min.js"></script>
<script type="text/javascript">
    var entity = -1; //Id of the selected entity
    var entityName = '';
    var includeChildren = true;
    var text; //Label of the selected entity
    var toggleDayoffs = false;
    
    //Refresh the calendar if data is available
    function refresh_calendar() {
        if (entity != -1) {
            var source = '<?php echo base_url();?>leaves/organization/' + entity;
            if ($('#chkIncludeChildren').prop('checked') == true) {
                source += '?children=true';
            } else {
                source += '?children=false';
            }
            $('#calendar').fullCalendar('removeEvents');
            $('#calendar').fullCalendar('addEventSource', source);
            $('#calendar').fullCalendar('rerenderEvents');
            $('#calendar').fullCalendar('removeEventSource', source);
        }
        source = '<?php echo base_url();?>contracts/calendar/alldayoffs?entity=' + entity;
        if ($('#chkIncludeChildren').prop('checked') == true) {
            source += '&children=true';
        } else {
            source += '&children=false';
        }
        if (toggleDayoffs) {
            $('#calendar').fullCalendar('addEventSource', source);
            $('#calendar').fullCalendar('rerenderEvents');
            $('#calendar').fullCalendar('removeEventSource', source);
        } else {
            $('#calendar').fullCalendar('removeEventSource', source);
        }
    }
    
    function select_entity() {
        entity = $('#organization').jstree('get_selected')[0];
        entityName = $('#organization').jstree().get_text(entity);
        $('#txtEntity').val(entityName);
        refresh_calendar();
        $.cookie('cal_entity', entity);
        $.cookie('cal_entityName', entityName);
        $.cookie('cal_includeChildren', includeChildren);
        $("#frmSelectEntity").modal('hide');
    }

    $(document).ready(function() {

        //Popup select entity
        $("#cmdSelectEntity").click(function() {
            $("#frmSelectEntity").modal('show');
            $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
        });

        //On click the check box "include sub-department", refresh the content if a department was selected
        $('#chkIncludeChildren').click(function() {
            $.cookie('cal_includeChildren', $('#chkIncludeChildren').prop('checked'));
            refresh_calendar();
        });

        //Load alert forms
        $("#frmSelectEntity").alert();
        //Prevent to load always the same content (refreshed each time)
        $('#frmSelectEntity').on('hidden', function() {
            $(this).removeData('modal');
        });

        //Create a calendar and fill it with AJAX events
        $('#calendar').fullCalendar({
            timeFormat: ' ', /*Trick to remove the start time of the event*/
             header: {
                    left: "",
                    center: "title",
                    right: ""
            },
            loading: function(isLoading) {
            if (isLoading) { //Display/Hide a pop-up showing an animated icon during the Ajax query.
                $('#frmModalAjaxWait').modal('show');
            } else {
                $('#frmModalAjaxWait').modal('hide');
            }    
        }
        });
        
        //Toggle day offs displays
        $('#cmdDisplayDayOff').on('click', function() {
            toggleDayoffs = !toggleDayoffs;
            $.cookie('cal_dayoffs', toggleDayoffs);
            refresh_calendar();
        });
        
        $('#cmdNext').click(function() {
            $('#calendar').fullCalendar('next');
            refresh_calendar();
        });
        
        $('#cmdPrevious').click(function() {
            $('#calendar').fullCalendar('prev');
            refresh_calendar();
        });
        
        $('#cmdToday').click(function() {
            $('#calendar').fullCalendar('today');
            refresh_calendar();
        });
        
        //Cookie has value ? take -1 by default
        if($.cookie('cal_entity') != null) {
            entity = $.cookie('cal_entity');
            entityName = $.cookie('cal_entityName');
            includeChildren = $.cookie('cal_includeChildren');
            toggleDayoffs = $.cookie('cal_dayoffs');
            //Parse boolean values
            includeChildren = $.parseJSON(includeChildren.toLowerCase());
            toggleDayoffs = $.parseJSON(toggleDayoffs.toLowerCase());
            $('#txtEntity').val(entityName);
            $('#chkIncludeChildren').prop('checked', includeChildren);
            //Load the calendar events
            refresh_calendar();
        } else { //Set default value
            $.cookie('cal_entity', entity);
            $.cookie('cal_entityName', entityName);
            $.cookie('cal_includeChildren', includeChildren);
            $.cookie('cal_dayoffs', toggleDayoffs);
        }
        
        //Copy/Paste ICS Feed
        var client = new ZeroClipboard($("#cmdCopy"));
        $('#lnkICS').click(function () {
            if (!('ZeroClipboard' in window)) {
                alert('e');
            }
            if (entity == -1) {
                var UrlICS = '<?php echo base_url(); ?>ics/entity/<?php echo $user_id; ?>/0/' + $('#chkIncludeChildren').prop('checked');
            } else {
                var UrlICS = '<?php echo base_url(); ?>ics/entity/<?php echo $user_id; ?>/' + entity + '/' + $('#chkIncludeChildren').prop('checked');
            }
            $('#txtIcsUrl').val(UrlICS);
            ZeroClipboard.setData( "text/plain", UrlICS);
            $("#frmLinkICS").modal('show');
        });
        client.on( "aftercopy", function( event ) {
            $('#tipCopied').tooltip('show');
            setTimeout(function() {$('#tipCopied').tooltip('hide')}, 1000);
        });
    });
</script>

