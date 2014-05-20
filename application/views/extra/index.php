<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('extra', $language);
$this->lang->load('datatable', $language);
$this->lang->load('status', $language);
?>

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
    $(".alert").alert();
});
</script>
<?php } ?>

<h1><?php echo lang('extra_index_title');?></h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="extras" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('extra_index_thead_id');?></th>
            <th><?php echo lang('extra_index_thead_date');?></th>
            <th><?php echo lang('extra_index_thead_duration');?></th>
            <th><?php echo lang('extra_index_thead_cause');?></th>
            <th><?php echo lang('extra_index_thead_status');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($extras as $extra_item): ?>
    <tr>
        <td>
            <a href="<?php echo base_url();?>extra/<?php echo $extra_item['id']; ?>" title="View request"><?php echo $extra_item['id']; ?></a>
            &nbsp;
            <div class="pull-right">
                <a href="<?php echo base_url();?>extra/<?php echo $extra_item['id']; ?>" title="view request details"><i class="icon-eye-open"></i></a>
                &nbsp;
                <?php if ($extra_item['status'] == 1) { ?>
                <a href="<?php echo base_url();?>extra/edit/<?php echo $extra_item['id']; ?>" title="edit request details"><i class="icon-pencil"></i></a>
                &nbsp;
                <a href="#" class="confirm-delete" data-id="<?php echo $extra_item['id'];?>" title="delete request"><i class="icon-trash"></i></a>
                <?php } ?>
            </div>
        </td>
        <td><?php echo $extra_item['date']; ?></td>
        <td><?php echo $extra_item['duration']; ?></td>
        <td><?php echo $extra_item['cause']; ?></td>
        <td><?php echo lang($extra_item['status_label']); ?></td>
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
      <a href="<?php echo base_url();?>extra/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; <?php echo lang('extra_index_button_export');?></a>
    </div>
    <div class="span2">
      <a href="<?php echo base_url();?>extra/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp; <?php echo lang('extra_index_button_create');?></a>
    </div>
    <div class="span2">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="frmDeleteExtraRequest" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmDeleteExtraRequest').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('extra_index_popup_delete_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('extra_index_popup_delete_message');?></p>
        <p><?php echo lang('extra_index_popup_delete_question');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeleteUser" class="btn danger"><?php echo lang('extra_index_popup_delete_button_yes');?></a>
        <a href="#" onclick="$('#frmDeleteExtraRequest').modal('hide');" class="btn secondary"><?php echo lang('extra_index_popup_delete_button_no');?></a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#extras').dataTable({
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
        $("#lnkDeleteUser").attr('href', link);
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
