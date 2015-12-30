<?php
/**
 * This view lists the list overtime requests created by an employee (from HR menu).
 * @copyright  Copyright (c) 2014-2015 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('hr_overtime_html_title');?><?php echo $user_id; ?>&nbsp;<span class="muted">(<?php echo $name ?>)</span></h2>

<?php echo $flash_partial_view;?>

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
        <td><?php echo lang($extra['status_name']); ?></td>
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
    <div class="span12">
      <a href="<?php echo base_url();?>hr/overtime/export/<?php echo $user_id; ?>" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp;<?php echo lang('hr_overtime_button_export');?></a>
      &nbsp;&nbsp;
      <a href="<?php echo base_url();?>hr/employees" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('hr_overtime_button_list');?></a>
    </div>
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

