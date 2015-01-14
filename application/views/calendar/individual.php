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
$this->lang->load('status', $language);
$this->lang->load('global', $language);?>

<h1><?php echo lang('calendar_individual_title');?></h1>

<div class="row-fluid">
    <div class="span12"><?php echo lang('calendar_individual_description');?></div>
</div>

<div class="row-fluid">
    <div class="span4">
        <button id="cmdPrevious" class="btn btn-primary"><i class="icon-chevron-left icon-white"></i></button>
        <button id="cmdToday" class="btn btn-primary"><?php echo lang('calendar_component_buttonText_today');?></button>
        <button id="cmdNext" class="btn btn-primary"><i class="icon-chevron-right icon-white"></i></button>
    </div>
    <div class="span2">
        <button id="cmdDisplayDayOff" class="btn btn-primary"><i class="icon-calendar icon-white"></i><?php echo lang('calendar_individual_day_offs');?></button>
    </div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><span class="label"><?php echo lang('Planned');?></span></div>
    <div class="span3"><span class="label label-success"><?php echo lang('Accepted');?></span></div>
    <div class="span3"><span class="label label-warning"><?php echo lang('Requested');?></span></div>
    <div class="span3"><span class="label label-important" style="background-color: #ff0000;"><?php echo lang('Rejected');?></span></div>
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

<link href="<?php echo base_url();?>assets/fullcalendar/fullcalendar.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/lib/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/lang/<?php echo $language_code;?>.js"></script>
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
    $("#frmEvent").alert();

    $('#calendar').fullCalendar({
        header: {
            left: "",
            center: "title",
            right: ""
        },
        events: '<?php echo base_url();?>leaves/individual',
        eventClick: function(calEvent, jsEvent, view) {
            var link = "<?php echo base_url();?>leaves/ical/" + calEvent.id;
            $("#lnkDownloadCalEvnt").attr('href', link);
            $('#frmEvent').modal('show');
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
