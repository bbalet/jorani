<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('calendar', $language);?>

<h1><?php echo lang('calendar_individual_title');?></h1>

<div id='calendar'></div>

<div id="frmEvent" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmEvent').modal('hide')" class="close">&times;</a>
         <h3><?php echo lang('calendar_individual_popup_event_title');?></h3>
    </div>
    <div class="modal-body">
        <a href="#" id="lnkDownloadCalEvnt"><?php echo lang('calendar_individual_popup_event_link_ical');?></a> <?php echo lang('calendar_individual_popup_event_link_ical_description');?>
        
    </div>
    <div class="modal-footer">
        <a href="javascript:$('#frmEvent').modal('hide')" class="btn secondary"><?php echo lang('calendar_individual_popup_event_button_close');?></a>
    </div>
</div>

<link href="<?php echo base_url();?>assets/fullcalendar/fullcalendar.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript">
$(function () {
    $("#frmEvent").alert();
    
    $('#calendar').fullCalendar({
        monthNames: [<?php echo lang('calendar_component_monthNames');?>],
        monthNamesShort: [<?php echo lang('calendar_component_monthNamesShort');?>],
        dayNames: [<?php echo lang('calendar_component_dayNames');?>],
        dayNamesShort: [<?php echo lang('calendar_component_dayNamesShort');?>],
        titleFormat: {
            month: '<?php echo lang('calendar_component_titleFormat_month');?>',
            week: "<?php echo lang('calendar_component_titleFormat_week');?>",
            day: '<?php echo lang('calendar_component_titleFormat_day');?>'
        },
        columnFormat: {
            month: '<?php echo lang('calendar_component_columnFormat_month');?>',
            week: '<?php echo lang('calendar_component_columnFormat_week');?>',
            day: '<?php echo lang('calendar_component_columnFormat_day');?>'
        },
        axisFormat: "<?php echo lang('calendar_component_axisFormat');?>",
        timeFormat: {
            '': "<?php echo lang('calendar_component_timeFormat');?>",
            agenda: "<?php echo lang('calendar_component_timeFormat_agenda');?>"
        },
        firstDay: <?php echo lang('calendar_component_firstDay');?>,
        buttonText: {
            today: "<?php echo lang('calendar_component_buttonText_today');?>",
            day: "<?php echo lang('calendar_component_buttonText_day');?>",
            week: "<?php echo lang('calendar_component_buttonText_week');?>",
            month: "<?php echo lang('calendar_component_buttonText_month');?>"
        },
        header: {
            left: "<?php echo lang('calendar_component_header_left');?>",
            center: "<?php echo lang('calendar_component_header_center');?>",
            right: "<?php echo lang('calendar_component_header_right');?>"
        },
        events: '<?php echo base_url();?>leaves/individual',
        eventClick: function(calEvent, jsEvent, view) {
            var link = "<?php echo base_url();?>leaves/ical/" + calEvent.id;
            $("#lnkDownloadCalEvnt").attr('href', link);
            $('#frmEvent').modal('show');
        }
    });
    
    //Prevent to load always the same content (refreshed each time)
    $('#frmEvent').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>
