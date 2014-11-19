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
    
var last_startDate = moment("Jan 1, 1970");
var last_endDate = moment("Jan 1, 1970");
var duration = 0;
var last_duration = 0;
var addDays = 0;
var last_type = 0;
    
//Try to calculate the length of the leave
function getLeaveLength() {
    var start = moment($('#startdate').val());
    var end = moment($('#enddate').val());
    var startType = $('#startdatetype option:selected').val();
    var endType = $('#enddatetype option:selected').val();      

    if (start.isValid() && end.isValid()) {
        if (start.isSame(end)) {
            addDays = 0;
            if (startType == "Morning" && endType == "Morning") {
                duration = 0.5;
                $("#spnDayOff").html("<img src='" + baseURL + "assets/images/leave_1d_MM.png' />");
            }
            if (startType == "Afternoon" && endType == "Afternoon") {
                duration = 0.5;
                $("#spnDayOff").html("<img src='" + baseURL + "assets/images/leave_1d_AA.png' />");
            }
            if (startType == "Morning" && endType == "Afternoon") {
                duration = 1;
                $("#spnDayOff").html("<img src='" + baseURL + "assets/images/leave_1d_MA.png' />");
            }
            if (startType == "Afternoon" && endType == "Morning") {
                //Error
                $("#spnDayOff").html("<img src='" + baseURL + "assets/images/date_error.png' />");
            }
            $('#duration').val(duration + addDays);
            checkDuration();
        } else {
             if (start.isBefore(end)) {
                if (startType == "Morning" && endType == "Morning") {
                    $("#spnDayOff").html("<img src='" + baseURL + "assets/images/leave_2d_MM.png' />");
                    addDays = 0.5;
                }
                if (startType == "Afternoon" && endType == "Afternoon") {
                    $("#spnDayOff").html("<img src='" + baseURL + "assets/images/leave_2d_AA.png' />");
                    addDays = 0.5;
                }
                if (startType == "Morning" && endType == "Afternoon") {
                    $("#spnDayOff").html("<img src='" + baseURL + "assets/images/leave_2d_MA.png' />");
                    addDays = 1;
                }
                if (startType == "Afternoon" && endType == "Morning") {
                    $("#spnDayOff").html("<img src='" + baseURL + "assets/images/leave_2d_AM.png' />");
                    addDays = 0;
                }
                //Prevent multiple triggers by UI components
                if (!start.isSame(last_startDate) || !end.isSame(last_endDate)) {
                    $.ajax({
                        type: "POST",
                        url: baseURL + "leaves/length",
                        data: {
                            user_id: userId,
                            start: $('#startdate').val(),
                            end: $('#enddate').val()
                            }
                        })
                        .done(function(msg) {
                            duration = parseFloat(msg);
                            $('#duration').val(duration + addDays);
                            checkDuration();
                        });

                }
                else {
                    $('#duration').val(duration + addDays);
                    checkDuration();
                }
                last_startDate = start;
                last_endDate = end;
             } else {
                //Error
             }
        }
    }   //start and end dates are valid
}

//Check the entered duration of the leave
function checkDuration() {
    //Prevent multiple triggers by UI components
    if ((duration != last_duration) || (last_type != $("#type option:selected").val())) {
        if ($("#duration").val() != "") {
            $.ajax({
                type: "POST",
                url: baseURL + "leaves/credit",
                data: { id: userId, type: $("#type option:selected").text() }
                })
                .done(function(msg) {
                    var credit = parseInt(msg);
                    var duration = parseFloat($("#duration").val());
                    if (duration > credit) {
                        $("#lblCreditAlert").show();
                    } else {
                        $("#lblCreditAlert").hide();
                    }
                });
         }
     }
     last_duration = duration;
     last_type = $("#type option:selected").val();
}

$(function () {
    $("#viz_startdate").datepicker({
        changeMonth: true,
        changeYear: true,
        altFormat: "yy-mm-dd",
        altField: "#startdate",
        numberOfMonths: 1,
              onClose: function( selectedDate ) {
                $( "#viz_enddate" ).datepicker( "option", "minDate", selectedDate );
              }
    }, $.datepicker.regional[languageCode]);
    $("#viz_enddate").datepicker({
        changeMonth: true,
        changeYear: true,
        altFormat: "yy-mm-dd",
        altField: "#enddate",
        numberOfMonths: 1,
              onClose: function( selectedDate ) {
                $( "#viz_startdate" ).datepicker( "option", "maxDate", selectedDate );
              }
    }, $.datepicker.regional[languageCode]);

    //Force decimal separator whatever the locale is
    $( "#days" ).keyup(function() {
        var value = $("#days").val();
        value = value.replace(",", ".");
        $("#days").val(value);
    });

    $('#viz_startdate').change(function() {getLeaveLength();});
    $('#viz_enddate').change(function() {getLeaveLength();});
    $('#startdatetype').change(function() {getLeaveLength();});
    $('#enddatetype').change(function() {getLeaveLength();});
    $('#type').change(function() {checkDuration();});

    //Check if the user has not exceed the number of entitled days
    $("#duration").keyup(function() {
        checkDuration();
    });
});
