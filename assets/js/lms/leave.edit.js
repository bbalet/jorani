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
    
var addDays = 0;
    
//Try to calculate the length of the leave
function getLeaveLength() {
    var start = moment($('#startdate').val());
    var end = moment($('#enddate').val());
    var startType = $('#startdatetype option:selected').val();
    var endType = $('#enddatetype option:selected').val();      

    if (start.isValid() && end.isValid()) {
        if (start.isSame(end)) {
            if (startType == "Morning" && endType == "Morning") {
                addDays = 0.5;
                $("#spnDayOff").html("<img src='" + baseURL + "assets/images/leave_1d_MM.png' />");
            }
            if (startType == "Afternoon" && endType == "Afternoon") {
                addDays = 0.5;
                $("#spnDayOff").html("<img src='" + baseURL + "assets/images/leave_1d_AA.png' />");
            }
            if (startType == "Morning" && endType == "Afternoon") {
                addDays = 1;
                $("#spnDayOff").html("<img src='" + baseURL + "assets/images/leave_1d_MA.png' />");
            }
            if (startType == "Afternoon" && endType == "Morning") {
                //Error
                $("#spnDayOff").html("<img src='" + baseURL + "assets/images/date_error.png' />");
            }
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
             }
        }
        getLeaveInfos(false);
    }
}

//Get the leave credit, duration and detect overlapping cases (Ajax request)
function getLeaveInfos(preventDefault) {
        $('#frmModalAjaxWait').modal('show');
        $.ajax({
        type: "POST",
        url: baseURL + "leaves/validate",
        data: {   id: userId,
                    type: $("#type option:selected").text(),
                    startdate: $('#startdate').val(),
                    enddate: $('#enddate').val(),
                    startdatetype: $('#startdatetype').val(),
                    enddatetype: $('#enddatetype').val()
                }
        })
        .done(function(leaveInfo) {
            if (typeof leaveInfo.length !== 'undefined') {
                var duration = parseFloat(leaveInfo.length)  + addDays;
                if (!preventDefault) $('#duration').val(duration);
            }
            if (typeof leaveInfo.credit !== 'undefined') {
                var credit = parseInt(leaveInfo.credit);
                var duration = parseFloat($("#duration").val());
                if (duration > credit) {
                    $("#lblCreditAlert").show();
                } else {
                    $("#lblCreditAlert").hide();
                }
                $("#lblCredit").text(leaveInfo.credit);
            }
            if (typeof leaveInfo.overlap !== 'undefined') {
                if (Boolean(leaveInfo.overlap)) {
                    $("#lblOverlappingAlert").show();
                } else {
                    $("#lblOverlappingAlert").hide();
                }
            }
            $('#frmModalAjaxWait').modal('hide');
        });    
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
    $('#type').change(function() {getLeaveInfos(false);});

    //Check if the user has not exceed the number of entitled days
    $("#duration").keyup(function() {getLeaveInfos(true);});
});
