<?php 
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
 * along with lms.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('reports', $language);?>

<div class="row-fluid">
    <div class="span12">
        
<h1><?php echo lang('reports_history_title');?></h1>

<div class="row-fluid">
    <div class="span8">
    <label for="txtModifiedBy"><?php echo lang('reports_history_field_modifiedby');?></label>
    <div class="input-append">
        <input type="text" id="txtModifiedBy" name="txtModifiedBy" required readonly />
       <a href="#" onclick="clear_user();" class="btn btn-primary btn-danger"><?php echo lang('reports_history_popup_user_button_clear');?></a>
        <a id="cmdSelectUser" class="btn btn-primary"><?php echo lang('reports_history_button_select');?></a>
    </div>
    <label for="startdate"><?php echo lang('reports_history_field_startdate');?></label>
    <input type="text" name="startdate" id="startdate" />
    <label for="enddate"><?php echo lang('reports_history_field_enddate');?></label>
    <input type="text" name="enddate" id="enddate" />
    
    <label for="cboTable"><?php echo lang('reports_history_field_table');?></label>
    <select name="cboTable" id="cboTable" required>
        <option value="users" selected>users</option>
        <option value="users">users</option>
    </select>

    <label for="cboModificationType"><?php echo lang('reports_history_field_modification_type');?></label>
    <select name="cboModificationType" id="cboModificationType" required>
        <option value="0" selected><?php echo lang('reports_history_field_modification_any');?></option>
        <option value="1"><?php echo lang('reports_history_field_modification_create');?></option>
        <option value="2"><?php echo lang('reports_history_field_modification_update');?></option>
        <option value="3"><?php echo lang('reports_history_field_modification_delete');?></option>
    </select>
    
    </div>
    <div class="span4">
        <div class="pull-right">
            &nbsp;
            <button class="btn btn-primary" id="cmdLaunchReport"><i class="icon-file icon-white"></i>&nbsp; <?php echo lang('reports_history_button_launch');?></button>
            <button class="btn btn-primary" id="cmdExportReport"><i class="icon-file icon-white"></i>&nbsp; <?php echo lang('reports_history_button_export');?></button>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>

<div id="reportResult"></div>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>

<div id="frmSelectUser" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectManager').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('reports_history_popup_user_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectUserBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_user();" class="btn secondary"><?php echo lang('reports_history_popup_user_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectUser').modal('hide');" class="btn secondary"><?php echo lang('reports_history_popup_user_button_cancel');?></a>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript">

var user = -1; //Id of the selected user (modified by)

function select_user() {
     user = $('#employees .row_selected td:first').text();
     var text = $('#employees .row_selected td:eq(1)').text();
     text += ' ' + $('#employees .row_selected td:eq(2)').text();
     $('#txtModifiedBy').val(text);
     $("#frmSelectUser").modal('hide');
 }

//button to clear selected user
function clear_user() {
     user = -1;
     $('#txtModifiedBy').val("");
     $("#frmSelectUser").modal('hide');
 }

$(document).ready(function() {
    $("#frmSelectUser").alert();
    $('#startdate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    $('#enddate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    
    $("#cmdSelectUser").click(function() {
        $("#frmSelectUser").modal('show');
        $("#frmSelectUserBody").load('<?php echo base_url(); ?>users/employees');
    });
    
    $('#cmdExportReport').click(function() {
        var exportQuery = '<?php echo base_url();?>reports/history/export';
        exportQuery += '?table=' + $('#cboTable option:selected').val();
        exportQuery += '&modification_type=' + $('#cboModificationType option:selected').val();
        if (user != -1) exportQuery += '&modified_by=' + user;
        if ($('#startdate').val() != "") exportQuery += '&startdate=' + $('#startdate').val();
        if ($('#enddate').val() != "") exportQuery += '&enddate=' + $('#enddate').val();
        document.location.href = exportQuery;
    });
    
    $('#cmdLaunchReport').click(function() {
        var ajaxQuery = '<?php echo base_url();?>reports/history/execute';
        ajaxQuery += '?table=' + $('#cboTable option:selected').val();
        ajaxQuery += '&modification_type=' + $('#cboModificationType option:selected').val();
        if (user != -1) ajaxQuery += '&modified_by=' + user;
        if ($('#startdate').val() != "") ajaxQuery += '&startdate=' + $('#startdate').val();
        if ($('#enddate').val() != "") ajaxQuery += '&enddate=' + $('#enddate').val();
        
        $('#reportResult').html("<img src='<?php echo base_url();?>assets/images/loading.gif' />");
        
        $.ajax({
          url: ajaxQuery
        })
        .done(function( data ) {
              $('#reportResult').html(data);
        });

    });
});
</script>
