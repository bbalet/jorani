<h1>My calendar</h1>

<div id='calendar'></div>

<link href="<?php echo base_url();?>assets/fullcalendar/fullcalendar.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    ///myfeed.php?start=1262332800&end=1265011200&_=1263178646
    $('#calendar').fullCalendar({
        events: '<?php echo base_url();?>leaves/individual'
    });
});
</script>
