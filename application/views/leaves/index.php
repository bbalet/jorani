<?php
/**
 * This view displays the list of leave requests created by an employee.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('leaves_index_title');?> &nbsp;<?php echo $help;?></h2>

<?php echo $flash_partial_view;?>

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
            <a href="<?php echo base_url();?>leaves/leaves/<?php echo $leaves_item['id']; ?>" title="<?php echo lang('leaves_index_thead_tip_view');?>"><?php echo $leaves_item['id']; ?></a>
            &nbsp;
            <div class="pull-right">
                <?php
                $show_delete = FALSE;
                $show_edit = FALSE;
                if ($leaves_item['status'] == 1) $show_delete = TRUE;
                if ($leaves_item['status'] == 1) $show_edit = TRUE;
                if (($leaves_item['status'] == 4) && ($this->config->item('delete_rejected_requests') == TRUE))  $show_delete = TRUE;
                if (($leaves_item['status'] == 4) && ($this->config->item('edit_rejected_requests') == TRUE))  $show_edit = TRUE;    
                ?>
                <?php if ($show_edit == TRUE) { ?>
                <a href="<?php echo base_url();?>leaves/edit/<?php echo $leaves_item['id']; ?>" title="<?php echo lang('leaves_index_thead_tip_edit');?>"><i class="icon-pencil"></i></a>
                &nbsp;
                <?php } ?>
                <?php if ($show_delete == TRUE) { ?>
                <a href="#" class="confirm-delete" data-id="<?php echo $leaves_item['id'];?>" title="<?php echo lang('leaves_index_thead_tip_delete');?>"><i class="icon-trash"></i></a>
                &nbsp;
                <?php } ?>
                <a href="<?php echo base_url();?>leaves/leaves/<?php echo $leaves_item['id']; ?>" title="<?php echo lang('leaves_index_thead_tip_view');?>"><i class="icon-eye-open"></i></a>
            </div>
        </td>
        <td data-order="<?php echo $tmpStartDate; ?>"><?php echo $startdate . ' (' . lang($leaves_item['startdatetype']). ')'; ?></td>
        <td data-order="<?php echo $tmpEndDate; ?>"><?php echo $enddate . ' (' . lang($leaves_item['enddatetype']) . ')'; ?></td>
        <td><?php echo $leaves_item['cause']; ?></td>
        <td><?php echo $leaves_item['duration']; ?></td>
        <td><?php echo $leaves_item['type_name']; ?></td>
        <td><?php echo lang($leaves_item['status_name']); ?></td>
    </tr>
<?php endforeach ?>
	</tbody>
</table>
	</div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div class="row-fluid">
    <div class="span12">
      <a href="<?php echo base_url();?>leaves/export" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp; <?php echo lang('leaves_index_button_export');?></a>
      &nbsp;&nbsp;
      <a href="<?php echo base_url();?>leaves/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp; <?php echo lang('leaves_index_button_create');?></a>
    </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.min.css" rel="stylesheet">
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
