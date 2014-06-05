<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('positions', $language);
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
        
<h1><?php echo lang('positions_index_title');?></h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="positions" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('positions_index_thead_id');?></th>
            <th><?php echo lang('positions_index_thead_name');?></th>
            <th><?php echo lang('positions_index_thead_description');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($positions as $position): ?>
    <tr>
        <td>
            <a href="#" class="confirm-delete" data-id="<?php echo $position['id'];?>" title="<?php echo lang('positions_index_thead_tip_delete');?>"><i class="icon-trash"></i></a>&nbsp; 
            <a href="<?php echo base_url();?>positions/edit/<?php echo $position['id']; ?>" title="<?php echo lang('positions_index_thead_tip_edit');?>"><i class="icon-pencil"></i></a>&nbsp; 
            <?php echo $position['id'];?>
        </td>
        <td><?php echo $position['name']; ?></td>
        <td><?php echo $position['description']; ?></td>
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
      <a href="<?php echo base_url();?>positions/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp;<?php echo lang('positions_index_button_export');?></a>
    </div>
    <div class="span3">
      <a href="<?php echo base_url();?>positions/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp;<?php echo lang('positions_index_button_create');?></a>
    </div>
    <div class="span7">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="frmDeletePosition" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmDeletePosition').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('positions_index_popup_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('positions_index_popup_description');?></p>
        <p><?php echo lang('positions_index_popup_confirm');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeletePosition" class="btn danger"><?php echo lang('positions_index_popup_button_yes');?></a>
        <a href="#" onclick="$('#frmDeletePosition').modal('hide');" class="btn secondary"><?php echo lang('positions_index_popup_button_no');?></a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#positions').dataTable({
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
    
    $("#frmDeletePosition").alert();
    
    //On showing the confirmation pop-up, add the contract id at the end of the delete url action
    $('#frmDeletePosition').on('show', function() {
        var link = "<?php echo base_url();?>positions/delete/" + $(this).data('id');
        $("#lnkDeletePosition").attr('href', link);
    });

    //Display a modal pop-up so as to confirm if a contract has to be deleted or not
    //We build a complex selector because datatable does horrible things on DOM...
    //a simplier selector doesn't work when the delete is on page >1 
    $("#positions tbody").on('click', '.confirm-delete',  function(){
            var id = $(this).data('id');
            $('#frmDeletePosition').data('id', id).modal('show');
    });
});
</script>
