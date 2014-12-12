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
$this->lang->load('global', $language);
$this->lang->load('hr', $language);
$this->lang->load('status', $language);
$this->lang->load('datatable', $language);?>

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
        
<h1><?php echo lang('hr_overtime_html_title');?><?php echo $user_id; ?></h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="extras" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('hr_overtime_thead_id');?></th>
            <th><?php echo lang('hr_overtime_thead_status');?></th>
            <th><?php echo lang('hr_overtime_thead_date');?></th>
            <th><?php echo lang('hr_overtime_thead_duration');?></th>
            <th><?php echo lang('hr_overtime_thead_cause');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($extras as $extra): 
    $date = new DateTime($extra['date']);
    $tmpDate = $date->getTimestamp();
    $date = $date->format(lang('global_date_format'));?>
    <tr>
        <td data-order="<?php echo $extra['id']; ?>">
            <a href="<?php echo base_url();?>extra/edit/<?php echo $extra['id']; ?>?source=hr%2Fovertime%2F<?php echo $user_id; ?>" title="<?php echo lang('hr_overtime_thead_tip_edit');?>"><?php echo $extra['id'] ?></a>
            <div class="pull-right">
                &nbsp;
                <a href="<?php echo base_url();?>overtime/accept/<?php echo $extra['id']; ?>?source=hr%2Fovertime%2F<?php echo $user_id; ?>" title="<?php echo lang('hr_overtime_thead_tip_accept');?>"><i class="icon-ok"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>overtime/reject/<?php echo $extra['id']; ?>?source=hr%2Fovertime%2F<?php echo $user_id; ?>" title="<?php echo lang('hr_overtime_thead_tip_reject');?>"><i class="icon-remove"></i></a>
                &nbsp;
                <a href="#" class="confirm-delete" data-id="<?php echo $extra['id'];?>" title="<?php echo lang('hr_overtime_thead_tip_delete');?>"><i class="icon-trash"></i></a>
            </div>
        </td>
        <td><?php echo lang($extra['status']); ?></td>
        <td data-order="<?php echo $tmpDate; ?>"><?php echo $date; ?></td>
        <td><?php echo $extra['duration']; ?></td>
        <td><?php echo $extra['cause']; ?></td>
    </tr>
<?php endforeach ?>
	</tbody>
</table>
	</div>
</div>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span2">
      <a href="<?php echo base_url();?>hr/overtime/export/<?php echo $user_id; ?>" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp;<?php echo lang('hr_overtime_button_export');?></a>
    </div>
    <div class="span3">
      <a href="<?php echo base_url();?>hr/employees" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('hr_overtime_button_list');?></a>
    </div>
    <div class="span7">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="frmDeleteExtraRequest" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
         <h3><?php echo lang('hr_overtime_popup_delete_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('hr_overtime_popup_delete_message');?></p>
        <p><?php echo lang('hr_overtime_popup_delete_question');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeleteExtra" class="btn danger"><?php echo lang('hr_overtime_popup_delete_button_yes');?></a>
        <a href="#" onclick="$('#frmDeleteExtraRequest').modal('hide');" class="btn secondary"><?php echo lang('hr_overtime_popup_delete_button_no');?></a>
    </div>
</div>

<script type="text/javascript">
$(function () {
    //Transform the HTML table in a fancy datatable
    var oTable = $('#extras').dataTable({
                  "order": [[ 2, "desc" ]],
	"oLanguage": {
                    "sEmptyTable":     "<?php echo lang('datatable_sEmptyTable');?>",
                    "sInfo":           "<?php echo lang('datatable_sInfo');?>",
                    "sInfoEmpty":      "<?php echo lang('datatable_sInfoEmpty');?>",
                    "sInfoFiltered":   "<?php echo lang('datatable_sInfoFiltered');?>",
                    "sInfoPostFix":    "<?php echo lang('datatable_sInfoPostFix');?>",
                    "sInfoThousands":  "<?php echo lang('datatable_sInfoThousands');?>",
                    "sLengthMenu":     "<?php echo lang('datatable_sLengthMenu');?>",
                    "sLoadingRecords": "<?php echo lang('datatable_sLoadingRecords');?>",
                    "sProcessing":     "<?php echo lang('datatable_sProcessing');?>",
                    "sSearch":         "<?php echo lang('datatable_sSearch');?>",
                    "sZeroRecords":    "<?php echo lang('datatable_sZeroRecords');?>",
                    "oPaginate": {
                        "sFirst":    "<?php echo lang('datatable_sFirst');?>",
                        "sLast":     "<?php echo lang('datatable_sLast');?>",
                        "sNext":     "<?php echo lang('datatable_sNext');?>",
                        "sPrevious": "<?php echo lang('datatable_sPrevious');?>"
                    },
                    "oAria": {
                        "sSortAscending":  "<?php echo lang('datatable_sSortAscending');?>",
                        "sSortDescending": "<?php echo lang('datatable_sSortDescending');?>"
                    }
                }
            });

    //On showing the confirmation pop-up, add the user id at the end of the delete url action
    $('#frmDeleteExtraRequest').on('show', function() {
        var link = "<?php echo base_url();?>extra/delete/" + $(this).data('id');
        link += "?source=hr%2Fovertime%2F<?php echo $user_id; ?>";
        $("#lnkDeleteExtra").attr('href', link);
    })

    //Display a modal pop-up so as to confirm if a leave request has to be deleted or not
    //We build a complex selector because datatable does horrible things on DOM...
    //a simplier selector doesn't work when the delete is on page >1 
    $("#extras tbody").on('click', '.confirm-delete',  function(){
        var id = $(this).data('id');
        $('#frmDeleteExtraRequest').data('id', id).modal('show');
    });
    
    $('#frmDeleteExtraRequest').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>

