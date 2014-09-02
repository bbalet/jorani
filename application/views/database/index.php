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

<h1><?php echo lang('database_index_title');?></h1>

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
                <td><input type="checkbox" id="" name=""></td>
                <td><?php echo $leaves_count; ?></td>
                <td><?php echo lang('database_index_table_leaves_desc');?></td>
            </tr>
            <tr>
                <td><input type="checkbox" id="" name=""></td>
                <td><?php echo $overtime_count; ?></td>
                <td><?php echo lang('database_index_table_overtime_desc');?></td>
            </tr>
            <tr>
                <td><input type="checkbox" id="" name=""></td>
                <td><?php echo $dayoffs_count; ?></td>
                <td><?php echo lang('database_index_table_dayoffs_desc');?></td>
            </tr>
            <tr>
                <td><input type="checkbox" id="" name=""></td>
                <td><?php echo $history_users_count; ?></td>
                <td><?php echo lang('database_index_table_history_users_desc');?></td>
            </tr>
            <tr>
                <td><input type="checkbox" id="" name=""></td>
                <td><?php echo $entitleddays_count; ?></td>
                <td><?php echo lang('database_index_table_entitleddays_desc');?></td>
            </tr>
            <tr>
                <td><input type="checkbox" id="" name=""></td>
                <td><?php echo $time_count; ?></td>
                <td><?php echo lang('database_index_table_time_desc');?></td>
            </tr>
        </tbody>
    </table>
	</div>
</div>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3">
      <input type="input" name="todate" id="todate" />
    </div>
    <div class="span3">
      <a href="<?php echo base_url();?>database/purge" class="btn btn-danger"><i class="icon-trash icon-white"></i>&nbsp; <?php echo lang('database_index_button_purge');?></a>
    </div>
    <div class="span6">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<link href="<?php echo base_url();?>assets/datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-langs.min.js" type="text/javascript"></script>

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
        <a href="#" id="lnkPurgeDb" class="btn danger"><?php echo lang('database_index_popup_purge_button_yes');?></a>
        <a href="#" onclick="$('#frmDeleteLeaveRequest').modal('hide');" class="btn secondary"><?php echo lang('database_index_popup_purge_button_no');?></a>
    </div>
</div>


<script type="text/javascript">
$(document).ready(function() {

    $("#frmConfirmPurge").alert();
    $('#todate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    
    //var start = moment($('#startdate').val());

    //On showing the confirmation pop-up, add the contract id at the end of the delete url action
    $('#frmDeleteContract').on('show', function() {
        var link = "<?php echo base_url();?>contracts/delete/" + $(this).data('id');
        $("#lnkDeleteContract").attr('href', link);
    });

    //Display a modal pop-up so as to confirm if a contract has to be deleted or not
    //We build a complex selector because datatable does horrible things on DOM...
    //a simplier selector doesn't work when the delete is on page >1 
    $("#contracts tbody").on('click', '.confirm-delete',  function(){
        var id = $(this).data('id');
        $('#frmDeleteContract').data('id', id).modal('show');
    });
    
});
</script>
