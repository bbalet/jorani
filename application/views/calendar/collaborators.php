<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('calendar', $language);
$this->lang->load('status', $language);?>

<h1><?php echo lang('calendar_collaborators_title');?></h1>

Leaves of my collaborators (will be empty if I'm not a manager).

<div class="row-fluid">
    <div class="span3"><span class="label"><?php echo lang('Planned');?></span></div>
    <div class="span3"><span class="label label-success"><?php echo lang('Accepted');?></span></div>
    <div class="span3"><span class="label label-warning"><?php echo lang('Requested');?></span></div>
    <div class="span3"><span class="label label-important"><?php echo lang('Rejected');?></span></div>
</div>

<div id='calendar'></div>

<link href="<?php echo base_url();?>assets/fullcalendar/fullcalendar.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    //Create a calendar and fill it with AJAX events
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
        events: '<?php echo base_url();?>leaves/collaborators'
    });
});
</script>

