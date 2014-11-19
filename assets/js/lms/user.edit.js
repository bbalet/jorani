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

//baseURL
//managerMessage
//languageCode
//mandatoryMessage

function select_manager() {
    var manager = $('#employees .row_selected td:first').text();
    var text = $('#employees .row_selected td:eq(1)').text();
    text += ' ' + $('#employees .row_selected td:eq(2)').text();
    $('#manager').val(manager);
    $('#txtManager').val(text);
    $("#frmSelectManager").modal('hide');
}

function select_entity() {
    var entity = $('#organization').jstree('get_selected')[0];
    var text = $('#organization').jstree().get_text(entity);
    $('#entity').val(entity);
    $('#txtEntity').val(text);
    $("#frmSelectEntity").modal('hide');
}

function select_position() {
    var position = $('#positions .row_selected td:first').text();
    var text = $('#positions .row_selected td:eq(1)').text();
    $('#position').val(position);
    $('#txtPosition').val(text);
    $("#frmSelectPosition").modal('hide');
}

function validate_form() {
    result = false;
    var fieldname = "";
    if ($('#firstname').val() == "") fieldname = "firstname";
    if ($('#lastname').val() == "") fieldname = "lastname";
    //if ($('#role:selected').length == 0) fieldname = "role";
    if ($('#login').val() == "") fieldname = "login";
    if ($('#email').val() == "") fieldname = "email";
    if ($('#txtManager').val() == "") fieldname = "manager";
    if ($('#contract').val() == "") fieldname = "contract";
    //if ($('#txtEntity').val() == "") fieldname = "entity";
    //if ($('#txtPosition').val() == "") fieldname = "position";
    //if ($('#datehired').val() == "") fieldname = "datehired";
    //if ($('#identifier').val() == "") fieldname = "identifier";
    if ($('#password').val() == "") fieldname = "password";
    if (fieldname == "") {
        return true;
    } else {
        bootbox.alert(mandatoryMessage);
        return false;
    }
}

$(document).ready(function() {

    //Load datepicker for Date Hired field
    $("#viz_datehired").datepicker({
        changeMonth: true,
        changeYear: true,
        altFormat: "yy-mm-dd",
        altField: "#datehired"
    }, $.datepicker.regional['<?php echo $language_code;?>']);

    //Popup select position
    $("#cmdSelectManager").click(function() {
        $("#frmSelectManager").modal('show');
        $("#frmSelectManagerBody").load(baseURL + 'users/employees');
    });

    //Popup select position
    $("#cmdSelectPosition").click(function() {
        $("#frmSelectPosition").modal('show');
        $("#frmSelectPositionBody").load(baseURL + 'positions/select');
    });

    //Popup select entity
    $("#cmdSelectEntity").click(function() {
        $("#frmSelectEntity").modal('show');
        $("#frmSelectEntityBody").load(baseURL + 'organization/select');
    });

    //Load alert forms
    $("#frmSelectEntity").alert();
    //Prevent to load always the same content (refreshed each time)
    $('#frmSelectEntity').on('hidden', function() {
        $(this).removeData('modal');
    });

    //Self manager button
    $("#cmdSelfManager").click(function() {
        $("#manager").val('-1');
        $('#txtManager').val(managerMessage);
    });
});