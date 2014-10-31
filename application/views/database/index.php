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
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('database', $language);?>

<div class="row-fluid">
    <div class="span12">

<?php if($this->session->flashdata('msg')){ ?>
<div class="alert fade in" id="flashbox">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php echo $this->session->flashdata('msg'); ?>
</div>
 
<script type="text/javascript">
//Flash message
$(document).ready(function() {
    $("#flashbox").alert();
});
</script>
<?php } ?>

        <h2><?php echo lang('database_index_title');?></h2>
    </div>
</div>

<form id="formPurge" action="<?php echo base_url();?>database/purge" method="POST">
<div class="row-fluid">
    <div class="span12">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-hover" id="contracts" width="100%">
            <thead>
                <tr>
                    <th><?php echo lang('database_index_thead_select');?></th>
                    <th><?php echo lang('database_index_thead_rows');?></th>
                    <th><?php echo lang('database_index_thead_table_name');?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value="leaves"></td>
                    <td><?php echo $leaves_count; ?></td>
                    <td><?php echo lang('database_index_table_leaves_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value="overtime"></td>
                    <td><?php echo $overtime_count; ?></td>
                    <td><?php echo lang('database_index_table_overtime_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value="dayoffs"></td>
                    <td><?php echo $dayoffs_count; ?></td>
                    <td><?php echo lang('database_index_table_dayoffs_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value="entitleddays"></td>
                    <td><?php echo $entitleddays_count; ?></td>
                    <td><?php echo lang('database_index_table_entitleddays_desc');?></td>
                </tr>
                <?php if ($this->config->item('enable_time') == true) { ?>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value="activities_employee"></td>
                    <td><?php echo $activities_employee_history_count; ?></td>
                    <td><?php echo lang('database_index_table_history_users_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value="activities_history"></td>
                    <td><?php echo $activities_history_count; ?></td>
                    <td><?php echo lang('database_index_table_history_users_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value="time"></td>
                    <td><?php echo $time_count; ?></td>
                    <td><?php echo lang('database_index_table_time_desc');?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($this->config->item('enable_history') == true) { ?>

<div class="row-fluid">
    <div class="span12">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-hover" id="contracts" width="100%">
            <thead>
                <tr>
                    <th><?php echo lang('database_index_thead_select');?></th>
                    <th><?php echo lang('database_index_thead_rows');?></th>
                    <th><?php echo lang('database_index_thead_table_name');?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value=""></td>
                    <td><?php echo $contracts_history_count; ?></td>
                    <td><?php echo lang('database_index_table_history_users_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value=""></td>
                    <td><?php echo $entitleddays_history_count; ?></td>
                    <td><?php echo lang('database_index_table_history_users_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value=""></td>
                    <td><?php echo $organization_history_count; ?></td>
                    <td><?php echo lang('database_index_table_history_users_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value=""></td>
                    <td><?php echo $overtime_history_count; ?></td>
                    <td><?php echo lang('database_index_table_history_users_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value=""></td>
                    <td><?php echo $positions_history_count; ?></td>
                    <td><?php echo lang('database_index_table_history_users_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value=""></td>
                    <td><?php echo $types_history_count; ?></td>
                    <td><?php echo lang('database_index_table_history_users_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value=""></td>
                    <td><?php echo $users_history_count; ?></td>
                    <td><?php echo lang('database_index_table_history_users_desc');?></td>
                </tr>
                <?php if ($this->config->item('enable_time') == true) { ?>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value=""></td>
                    <td><?php echo $activities_employee_history_count; ?></td>
                    <td><?php echo lang('database_index_table_history_users_desc');?></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="" name="chkTable[]" value=""></td>
                    <td><?php echo $activities_history_count; ?></td>
                    <td><?php echo lang('database_index_table_history_users_desc');?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>
</form>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span5">
      <label for="viz_todate"><?php echo lang('database_index_field_date');?>
      <input type="input" name="viz_todate" id="viz_todate" />
      <input type="hidden" name="todate" id="todate" /></label>
    </div>
    <div class="span3">
        <a href="#" id="cmdDelete" class="btn btn-danger"><i class="icon-trash icon-white"></i>&nbsp; <?php echo lang('database_index_button_purge');?></a>
    </div>
    <div class="span4">&nbsp;</div>
</div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui-1.10.4.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-langs.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>

<div id="frmConfirmPurge" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
         <h3><?php echo lang('database_index_popup_purge_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('database_index_popup_purge_message');?></p>
        <p><?php echo lang('database_index_popup_purge_question');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#formPurge').submit();" class="btn danger"><?php echo lang('database_index_popup_purge_button_yes');?></a>
        <a href="#" onclick="$('#frmConfirmPurge').modal('hide');" class="btn secondary"><?php echo lang('database_index_popup_purge_button_no');?></a>
    </div>
</div>

<script type="text/javascript">
    
    function validate_form() {
        result = false;
        var fieldname = "";
        if ($('#viz_todate').val() == "") fieldname = "<?php echo lang('database_index_field_date_desc');?>";
        if (fieldname == "") {
            return true;
        } else {
            bootbox.alert(<?php echo lang('database_purge_mandatory_js_msg');?>);
            return false;
        }
    }    
    
$(document).ready(function() {

    $("#frmConfirmPurge").alert();
    $("#viz_todate").datepicker({
            changeMonth: true,
            changeYear: true,
            altFormat: "yy-mm-dd",
            altField: "#date"
        }, $.datepicker.regional['<?php echo $language_code;?>']);
    
    //var start = moment($('#startdate').val());

    //Display a modal pop-up so as to confirm the purge
    $("#cmdDelete").on('click', function(){
        $('#frmConfirmPurge').modal('show');
    });

});
</script>
