<?php
/**
 * This view lists the list leave requests created by an employee (from HR menu).
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('hr_leaves_html_title');?><?php echo $user_id; ?>&nbsp;<span class="muted">(<?php echo $name ?>)</span></h2>

<?php echo $flash_partial_view;?>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="leaves" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('hr_leaves_thead_id');?></th>
            <th><?php echo lang('hr_leaves_thead_status');?></th>
            <th><?php echo lang('hr_leaves_thead_start');?></th>
            <th><?php echo lang('hr_leaves_thead_end');?></th>            
            <th><?php echo lang('hr_leaves_thead_duration');?></th>
            <th><?php echo lang('hr_leaves_thead_type');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($leaves as $leave):
    $date = new DateTime($leave['startdate']);
    $tmpStartDate = $date->getTimestamp();
    $startdate = $date->format(lang('global_date_format'));
    $date = new DateTime($leave['enddate']);
    $tmpEndDate = $date->getTimestamp();
    $enddate = $date->format(lang('global_date_format'));?>
    <tr>
        <td data-order="<?php echo $leave['id']; ?>">
            <a href="<?php echo base_url();?>leaves/edit/<?php echo $leave['id']; ?>?source=hr%2Fleaves%2F<?php echo $user_id; ?>" title="<?php echo lang('hr_leaves_thead_tip_edit');?>"><?php echo $leave['id'] ?></a>
            <div class="pull-right">
                &nbsp;
                <a href="<?php echo base_url();?>requests/accept/<?php echo $leave['id']; ?>?source=hr%2Fleaves%2F<?php echo $user_id; ?>" title="<?php echo lang('hr_leaves_thead_tip_accept');?>"><i class="icon-ok"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>requests/reject/<?php echo $leave['id']; ?>?source=hr%2Fleaves%2F<?php echo $user_id; ?>" title="<?php echo lang('hr_leaves_thead_tip_reject');?>"><i class="icon-remove"></i></a>
                &nbsp;
                <a href="#" class="confirm-delete" data-id="<?php echo $leave['id'];?>" title="<?php echo lang('hr_leaves_thead_tip_delete');?>"><i class="icon-trash"></i></a>
            </div>
        </td>
        <td><?php echo lang($leave['status_name']); ?></td>
        <td data-order="<?php echo $tmpStartDate; ?>"><?php echo $startdate . ' (' . lang($leave['startdatetype']). ')'; ?></td>
        <td data-order="<?php echo $tmpEndDate; ?>"><?php echo $enddate . ' (' . lang($leave['enddatetype']) . ')'; ?></td>
        <td><?php echo $leave['duration']; ?></td>
        <td><?php echo $leave['type_name']; ?></td>
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
    <div class="span12">
      <a href="<?php echo base_url();?>hr/leaves/export/<?php echo $user_id; ?>" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp;<?php echo lang('hr_leaves_button_export');?></a>
      &nbsp;&nbsp;
      <a href="<?php echo base_url();?>hr/employees" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('hr_leaves_button_list');?></a>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>

<div id="frmDeleteLeaveRequest" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
         <h3><?php echo lang('hr_leaves_popup_delete_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('hr_leaves_popup_delete_message');?></p>
        <p><?php echo lang('hr_leaves_popup_delete_question');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeleteUser" class="btn btn-danger"><?php echo lang('hr_leaves_popup_delete_button_yes');?></a>
        <a href="#" onclick="$('#frmDeleteLeaveRequest').modal('hide');" class="btn"><?php echo lang('hr_leaves_popup_delete_button_no');?></a>
    </div>
</div>

<link href="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/css/jquery.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$(function () {
    //Transform the HTML table in a fancy datatable
    var oTable = $('#leaves').dataTable({
                order: [[ 2, "desc" ]],
                language: {
                    decimal:            "<?php echo lang('datatable_sInfoThousands');?>",
                    processing:       "<?php echo lang('datatable_sProcessing');?>",
                    search:              "<?php echo lang('datatable_sSearch');?>",
                    lengthMenu:     "<?php echo lang('datatable_sLengthMenu');?>",
                    info:                   "<?php echo lang('datatable_sInfo');?>",
                    infoEmpty:          "<?php echo lang('datatable_sInfoEmpty');?>",
                    infoFiltered:       "<?php echo lang('datatable_sInfoFiltered');?>",
                    infoPostFix:        "<?php echo lang('datatable_sInfoPostFix');?>",
                    loadingRecords: "<?php echo lang('datatable_sLoadingRecords');?>",
                    zeroRecords:    "<?php echo lang('datatable_sZeroRecords');?>",
                    emptyTable:     "<?php echo lang('datatable_sEmptyTable');?>",
                    paginate: {
                        first:          "<?php echo lang('datatable_sFirst');?>",
                        previous:   "<?php echo lang('datatable_sPrevious');?>",
                        next:           "<?php echo lang('datatable_sNext');?>",
                        last:           "<?php echo lang('datatable_sLast');?>"
                    },
                    aria: {
                        sortAscending:  "<?php echo lang('datatable_sSortAscending');?>",
                        sortDescending: "<?php echo lang('datatable_sSortDescending');?>"
                    }
                }
            });

    //On showing the confirmation pop-up, add the user id at the end of the delete url action
    $('#frmDeleteLeaveRequest').on('show', function() {
        var link = "<?php echo base_url();?>leaves/delete/" + $(this).data('id');
        link += "?source=hr%2Fleaves%2F<?php echo $user_id; ?>";
        $("#lnkDeleteUser").attr('href', link);
    })

    //Display a modal pop-up so as to confirm if a leave request has to be deleted or not
    //We build a complex selector because datatable does horrible things on DOM...
    //a simplier selector doesn't work when the delete is on page >1 
    $("#leaves tbody").on('click', '.confirm-delete',  function(){
        var id = $(this).data('id');
        $('#frmDeleteLeaveRequest').data('id', id).modal('show');
    });
    
    $('#frmDeleteLeaveRequest').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>

