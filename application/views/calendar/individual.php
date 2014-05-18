<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('calendar', $language);
$this->lang->load('status', $language);?>

<h1><?php echo lang('calendar_individual_title');?></h1>

<?php echo lang('calendar_individual_description');?>

<div class="row-fluid">
    <div class="span3"><span class="label"><?php echo lang('Planned');?></span></div>
    <div class="span3"><span class="label label-success"><?php echo lang('Accepted');?></span></div>
    <div class="span3"><span class="label label-warning"><?php echo lang('Requested');?></span></div>
    <div class="span3"><span class="label label-important"><?php echo lang('Rejected');?></span></div>
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

<?php if ($googleApi) { ?>
<!--Add a button for the user to click to initiate auth sequence -->
    <button id="authorize-button" style="visibility: hidden">Authorize</button>
    <script type="text/javascript">

      var clientId = '<?php echo $clientId;?>';

      var apiKey = '<?php echo $apiKey;?>';

      var scopes = 'https://www.googleapis.com/auth/plus.me';

      function handleClientLoad() {
        // Step 2: Reference the API key
        gapi.client.setApiKey(apiKey);
        window.setTimeout(checkAuth,1);
      }

      function checkAuth() {
        gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: true}, handleAuthResult);
      }

      function handleAuthResult(authResult) {
        var authorizeButton = document.getElementById('authorize-button');
        if (authResult && !authResult.error) {
          authorizeButton.style.visibility = 'hidden';
          makeApiCall();
        } else {
          authorizeButton.style.visibility = '';
          authorizeButton.onclick = handleAuthClick;
        }
      }

      function handleAuthClick(event) {
        // Step 3: get authorization to use private data
        gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: false}, handleAuthResult);
        return false;
      }

      // Load the API and make an API call.  Display the results on the screen.
      function makeApiCall() {
          
          var resource = {
                "summary": "Appointment",
                "location": "Somewhere",
                "start": {
                  "dateTime": "2013-04-23T10:00:00.000-07:00"
                },
                "end": {
                  "dateTime": "2013-04-23T10:25:00.000-07:00"
                }
              };
              var request = gapi.client.calendar.events.insert({
                'calendarId': 'primary',
                'resource': resource
              });
              request.execute(function(resp) {
                console.log(resp);
              });

        // Step 4: Load the Google+ API
        gapi.client.load('plus', 'v1', function() {
          // Step 5: Assemble the API request
          var request = gapi.client.plus.people.get({
            'userId': 'me'
          });
          // Step 6: Execute the API request
          request.execute(function(resp) {
            var heading = document.createElement('h4');
            var image = document.createElement('img');
            image.src = resp.image.url;
            heading.appendChild(image);
            heading.appendChild(document.createTextNode(resp.displayName));

            document.getElementById('content').appendChild(heading);
          });
        });
      }
    </script>
    <script src="https://apis.google.com/js/client.js?onload=handleClientLoad"></script>

<?php } ?>
