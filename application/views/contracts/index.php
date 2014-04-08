
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

<table cellpadding="0" cellspacing="0" border="0" class="display" id="users" width="100%">
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
            <a href="<?php echo base_url();?>contracts/<?php echo $contracts_item['id'] ?>" title="view contract details"><i class="icon-eye-open"></i></a>
            &nbsp;
            <a href="<?php echo base_url();?>contracts/edit/<?php echo $contracts_item['id'] ?>" title="edit contract details"><i class="icon-pencil"></i></a>
            &nbsp;
            <a href="#" class="confirm-delete" data-id="<?php echo $contracts_item['id'];?>" title="delete contract"><i class="icon-trash"></i></a>
            &nbsp;
            <a href="<?php echo base_url();?>entitleddays/contract/<?php echo $contracts_item['id'] ?>" data-target="#frmEntitledDays" data-toggle="modal" title="entitled days"><i class="icon-edit"></i></a>
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
    <div class="span2">
      <a href="<?php echo base_url();?>contracts/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp; Create a new contract</a>
    </div>
    <div class="span2">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="modal-from-dom" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#modal-from-dom').modal('hide')" class="close">&times;</a>
         <h3>Delete Contract</h3>
    </div>
    <div class="modal-body">
        <p>You are about to delete one contract, this procedure is irreversible.</p>
        <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
        <a href="<?php echo base_url();?>contracts/delete/" class="btn danger">Yes</a>
        <a href="javascript:$('#modal-from-dom').modal('hide')" class="btn secondary">No</a>
    </div>
</div>

<div id="frmEntitledDays" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmEntitledDays').modal('hide')" class="close">&times;</a>
         <h3>Entitled days</h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="javascript:$('#frmEntitledDays').modal('hide')" class="btn secondary">Cancel</a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#users').dataTable();
    $("#frmChangePwd").alert();
    $("#frmEntitledDays").alert();
	
    //On showing the confirmation pop-up, add the contract id at the end of the delete url action
    $('#modal-from-dom').on('show', function() {
            var id = $(this).data('id'),
            removeBtn = $(this).find('.danger');
            removeBtn.attr('href', removeBtn.attr('href') + id);
    })

    //Display a modal pop-up so as to confirm if a contract has to be deleted or not
    $('.confirm-delete').on('click', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('#modal-from-dom').data('id', id).modal('show');
    });
});
</script>
