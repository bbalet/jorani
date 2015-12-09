/**
 * This Javascript code is used on the create/edit leave request
 * @copyright  Copyright (c) 2014-2015 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.3.0
 */
    
var addDays = 0;
    
//Try to calculate the length of the leave
function getLeaveLength(refreshInfos) {
    refreshInfos = typeof refreshInfos !== 'undefined' ? refreshInfos : true;
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
        if (refreshInfos) getLeaveInfos(false);
    }
}

//Get the leave credit, duration and detect overlapping cases (Ajax request)
//Default behavour is to set the duration field. pass false if you want to disable this behaviour
function getLeaveInfos(preventDefault) {
        $('#frmModalAjaxWait').modal('show');
        var start = moment($('#startdate').val());
        var end = moment($('#enddate').val());
        $.ajax({
        type: "POST",
        url: baseURL + "leaves/validate",
        data: {   id: userId,
                    type: $("#type option:selected").text(),
                    startdate: $('#startdate').val(),
                    enddate: $('#enddate').val(),
                    startdatetype: $('#startdatetype').val(),
                    enddatetype: $('#enddatetype').val(),
                    leave_id: leaveId
                }
        })
        .done(function(leaveInfo) {
            if (typeof leaveInfo.length !== 'undefined') {
                var duration = parseFloat(leaveInfo.length)  + addDays;
                duration = Math.round(duration * 1000) / 1000;  //Round to 3 decimals only if necessary
                if (!preventDefault) {
                    if (start.isValid() && end.isValid()) {
                        $('#duration').val(duration);
                    }
                }
            }
            if (typeof leaveInfo.credit !== 'undefined') {
                var credit = parseFloat(leaveInfo.credit);
                var duration = parseFloat($("#duration").val());
                if (duration > credit) {
                    $("#lblCreditAlert").show();
                } else {
                    $("#lblCreditAlert").hide();
                }
                if (leaveInfo.credit != null) {
                    $("#lblCredit").text('(' + leaveInfo.credit + ')');
                }
            }
            if (typeof leaveInfo.overlap !== 'undefined') {
                if (Boolean(leaveInfo.overlap)) {
                    $("#lblOverlappingAlert").show();
                } else {
                    $("#lblOverlappingAlert").hide();
                }
            }
            //Check if the employee has a contract
            if (leaveInfo.hasContract == false) {
                bootbox.alert(noContractMsg);
            } else {
                //If the employee has a contract, check if the current leave request is not on two yearly leave periods
                var limit = moment(leaveInfo.endentdate);
                if (start.isValid() && end.isValid() && limit.isValid()) {
                    if (start.isBefore(limit) && limit.isBefore(end)) {
                        bootbox.alert(noTwoPeriodsMsg);
                    }
                }
            }
            $('#frmModalAjaxWait').modal('hide');
        });    
}

$(function () {
    //On openning leave/edit, init addDays variable
    getLeaveLength(false);
    
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

    $('#viz_startdate').change(function() {getLeaveLength(true);});
    $('#viz_enddate').change(function() {getLeaveLength();});
    $('#startdatetype').change(function() {getLeaveLength();});
    $('#enddatetype').change(function() {getLeaveLength();});
    $('#type').change(function() {getLeaveInfos(false);});

    //Check if the user has not exceed the number of entitled days
    $("#duration").keyup(function() {getLeaveInfos(true);});
    
    $("#frmLeaveForm").submit(function(e) {
        if (validate_form()) {
            return true; 
        } else {
            e.preventDefault();
            return false; 
        }
    });
});
