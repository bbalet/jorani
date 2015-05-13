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
$this->lang->load('leaves', $language);
$this->lang->load('datatable', $language);
$this->lang->load('calendar', $language);
$this->lang->load('global', $language);?>

<div class="row-fluid">
    <div class="span12">

<?php echo $flash_partial_view;?>

<h1><?php echo lang('leaves_index_title');?> &nbsp;<?php echo $help;?></h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="leaves" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('leaves_index_thead_id');?></th>
            <th><?php echo lang('leaves_index_thead_start_date');?></th>
            <th><?php echo lang('leaves_index_thead_end_date');?></th>
            <th><?php echo lang('leaves_index_thead_cause');?></th>
            <th><?php echo lang('leaves_index_thead_duration');?></th>
            <th><?php echo lang('leaves_index_thead_type');?></th>
            <th><?php echo lang('leaves_index_thead_status');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($leaves as $leaves_item): 
    $date = new DateTime($leaves_item['startdate']);
    $tmpStartDate = $date->getTimestamp();
    $startdate = $date->format(lang('global_date_format'));
    $date = new DateTime($leaves_item['enddate']);
    $tmpEndDate = $date->getTimestamp();
    $enddate = $date->format(lang('global_date_format'));?>
    <tr>
        <td data-order="<?php echo $leaves_item['id']; ?>">
            <a href="<?php echo base_url();?>leaves/<?php echo $leaves_item['id']; ?>" title="<?php echo lang('leaves_index_thead_tip_view');?>"><?php echo $leaves_item['id']; ?></a>
            &nbsp;
            <div class="pull-right">
                <?php if ($leaves_item['status'] == 1) { ?>
                <a href="<?php echo base_url();?>leaves/edit/<?php echo $leaves_item['id']; ?>" title="<?php echo lang('leaves_index_thead_tip_edit');?>"><i class="icon-pencil"></i></a>
                &nbsp;
                <a href="#" class="confirm-delete" data-id="<?php echo $leaves_item['id'];?>" title="<?php echo lang('leaves_index_thead_tip_delete');?>"><i class="icon-trash"></i></a>
                <?php } ?>
                &nbsp;
                <a href="<?php echo base_url();?>leaves/<?php echo $leaves_item['id']; ?>" title="<?php echo lang('leaves_index_thead_tip_view');?>"><i class="icon-eye-open"></i></a>
            </div>
        </td>
        <td data-order="<?php echo $tmpStartDate; ?>"><?php echo $startdate . ' (' . lang($leaves_item['startdatetype']). ')'; ?></td>
        <td data-order="<?php echo $tmpEndDate; ?>"><?php echo $enddate . ' (' . lang($leaves_item['enddatetype']) . ')'; ?></td>
        <td><?php echo $leaves_item['cause']; ?></td>
        <td><?php echo $leaves_item['duration']; ?></td>
        <td><?php echo $leaves_item['type_label']; ?></td>
        <td><?php echo lang($leaves_item['status_label']); ?></td>
    </tr>
<?php endforeach ?>
	</tbody>
</table>
	</div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div class="row-fluid">
    <div class="span2">
      <a href="<?php echo base_url();?>leaves/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; <?php echo lang('leaves_index_button_export');?></a>
    </div>
    <div class="span2">
      <a href="<?php echo base_url();?>leaves/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp; <?php echo lang('leaves_index_button_create');?></a>
    </div>
    <div class="span2">&nbsp;</div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="frmDeleteLeaveRequest" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
         <h3><?php echo lang('leaves_index_popup_delete_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('leaves_index_popup_delete_message');?></p>
        <p><?php echo lang('leaves_index_popup_delete_question');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeleteUser" class="btn danger"><?php echo lang('leaves_index_popup_delete_button_yes');?></a>
        <a href="#" onclick="$('#frmDeleteLeaveRequest').modal('hide');" class="btn secondary"><?php echo lang('leaves_index_popup_delete_button_no');?></a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#frmDeleteLeaveRequest').alert();
    
    //Transform the HTML table in a fancy datatable
    $('#leaves').dataTable({
                  "order": [[ 1, "desc" ]],
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
    $('#frmDeleteLeaveRequest').on('show', function() {
        var link = "<?php echo base_url();?>leaves/delete/" + $(this).data('id');
        $("#lnkDeleteUser").attr('href', link);
    })
    
    //Display a modal pop-up so as to confirm if a leave request has to be deleted or not
    //We build a complex selector because datatable does horrible things on DOM...
    //a simplier selector doesn't work when the delete is on page >1 
    $("#leaves tbody").on('click', '.confirm-delete',  function(){
        var id = $(this).data('id');
        $('#frmDeleteLeaveRequest').data('id', id).modal('show');
    });
    
    //Prevent to load always the same content (refreshed each time)
    $('#frmDeleteLeaveRequest').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>
