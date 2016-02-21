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
      &nbsp;&nbsp;
        <?php if ($this->config->item('ics_enabled') == TRUE) {?>
        <a id="lnkICS" href="#"><i class="icon-globe"></i> ICS</a>
        <?php }?>
    </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

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
        <a href="#" id="lnkDeleteUser" class="btn btn-danger"><?php echo lang('leaves_index_popup_delete_button_yes');?></a>
        <a href="#" onclick="$('#frmDeleteLeaveRequest').modal('hide');" class="btn"><?php echo lang('leaves_index_popup_delete_button_no');?></a>
    </div>
</div>

<div id="frmLinkICS" class="modal hide fade">
    <div class="modal-header">
        <h3>ICS<a href="#" onclick="$('#frmLinkICS').modal('hide');" class="close">&times;</a></h3>
    </div>
    <div class="modal-body" id="frmSelectDelegateBody">
        <div class='input-append'>
                <input type="text" class="input-xlarge" id="txtIcsUrl" onfocus="this.select();" onmouseup="return false;" 
                    value="<?php echo base_url() . 'ics/individual/' . $user_id;?>" />
                 <button id="cmdCopy" class="btn" data-clipboard-text="<?php echo base_url() . 'ics/individual/' . $user_id;?>">
                     <i class="fa fa-clipboard"></i>
                 </button>
                <a href="#" id="tipCopied" data-toggle="tooltip" title="<?php echo lang('copied');?>" data-placement="right" data-container="#cmdCopy"></a>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmLinkICS').modal('hide');" class="btn btn-primary"><?php echo lang('OK');?></a>
    </div>
</div>

<link href="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/css/jquery.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/js/ZeroClipboard.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#frmDeleteLeaveRequest').alert();
    
    //Transform the HTML table in a fancy datatable
    $('#leaves').dataTable({
        order: [[ 1, "desc" ]],
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
    
    //Copy/Paste ICS Feed
    var client = new ZeroClipboard($("#cmdCopy"));
    $('#lnkICS').click(function () {
        $("#frmLinkICS").modal('show');
    });
    client.on( "aftercopy", function( event ) {
        $('#tipCopied').tooltip('show');
        setTimeout(function() {$('#tipCopied').tooltip('hide')}, 1000);
    });
});
</script>
