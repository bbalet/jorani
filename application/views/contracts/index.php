
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
        
<h1>List of contracts</h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="contracts" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Start period</th>
            <th>End period</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($contracts as $contracts_item): ?>
    <tr>
        <td>
            <a href="<?php echo base_url();?>contracts/<?php echo $contracts_item['id'] ?>" title="View contract"><?php echo $contracts_item['id'] ?></a>
            &nbsp;
            <div class="pull-right">
                <a href="<?php echo base_url();?>contracts/<?php echo $contracts_item['id'] ?>" title="view contract details"><i class="icon-eye-open"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>contracts/edit/<?php echo $contracts_item['id'] ?>" title="edit contract details"><i class="icon-pencil"></i></a>
                &nbsp;
                <a href="#" class="confirm-delete" data-id="<?php echo $contracts_item['id'];?>" title="delete contract"><i class="icon-trash"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>entitleddays/contract/<?php echo $contracts_item['id'] ?>" data-target="#frmEntitledDays" data-toggle="modal" title="entitled days"><i class="icon-edit"></i></a>
            </div>
        </td>
        <td><?php echo $contracts_item['name'] ?></td>
        <td><?php echo $contracts_item['startentdate'] ?></td>
        <td><?php echo $contracts_item['endentdate'] ?></td>
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
      <a href="<?php echo base_url();?>contracts/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; Export this list</a>
    </div>
    <div class="span3">
      <a href="<?php echo base_url();?>contracts/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp; Create a new contract</a>
    </div>
    <div class="span7">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="frmDeleteContract" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmDeleteContract').modal('hide');" class="close">&times;</a>
         <h3>Delete Contract</h3>
    </div>
    <div class="modal-body">
        <p>You are about to delete one contract, this procedure is irreversible.</p>
        <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeleteContract" class="btn danger">Yes</a>
        <a href="#" onclick="$('#frmDeleteContract').modal('hide');" class="btn secondary">No</a>
    </div>
</div>

<div id="frmEntitledDays" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmEntitledDays').modal('hide');" class="close">&times;</a>
         <h3>Entitled days</h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmEntitledDays').modal('hide');" class="btn secondary">Cancel</a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#contracts').dataTable();
    $("#frmChangePwd").alert();
    $("#frmEntitledDays").alert();
	
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
    
    $('#frmEntitledDays').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>
