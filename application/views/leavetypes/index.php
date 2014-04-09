
<h1>Leave types</h1>

<table class="table table-bordered table-hover">
<thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($leavetypes as $type) { ?>
    <tr>
      <td><?php echo $type['id'] ?> &nbsp; <a href="#" class="confirm-delete" data-id="<?php echo $type['id'];?>" title="delete leave type"><i class="icon-trash"></i></a></td>
      <td>
          <a href="<?php echo base_url();?>leavetypes/edit/<?php echo $type['id'] ?>" data-target="#frmEditLeaveType" data-toggle="modal" title="edit leave type"><i class="icon-pencil"></i></a>
          &nbsp; <?php echo $type['name']; ?></td>
    </tr>
  <?php } ?>
  <?php if (count($leavetypes) == 0) { ?>
    <tr>
        <td colspan="5">No leave type found into the database.</td>
    </tr>
  <?php } ?>
  </tbody>
</table>

<div class="row-fluid">
	<div class="span12">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3">
      <a href="<?php echo base_url();?>leavetypes/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; Export this list</a>
    </div>
    <div class="span3">
        <a href="<?php echo base_url();?>leavetypes/create" class="btn btn-primary" data-target="#frmAddLeaveType" data-toggle="modal"><i class="icon-plus-sign icon-white"></i>&nbsp; Create a new type</a>
    </div>
    <div class="span6">&nbsp;</div>
</div>

<div id="frmAddLeaveType" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmAddLeaveType').modal('hide')" class="close">&times;</a>
         <h3>Add a leave type</h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="javascript:$('#frmAddLeaveType').modal('hide')" class="btn secondary">Cancel</a>
    </div>
</div>

<div id="frmEditLeaveType" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmEditLeaveType').modal('hide')" class="close">&times;</a>
         <h3>Edit a  Leave type</h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="javascript:$('#frmEditLeaveType').modal('hide')" class="btn secondary">Cancel</a>
    </div>
</div>

<div id="modal-from-dom" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#modal-from-dom').modal('hide')" class="close">&times;</a>
         <h3>Delete Leave Type</h3>
    </div>
    <div class="modal-body">
        <p>You are about to delete one leave type, this procedure is irreversible.</p>
        <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
        <a href="<?php echo base_url();?>leavetypes/delete/" class="btn danger">Yes</a>
        <a href="javascript:$('#modal-from-dom').modal('hide')" class="btn secondary">No</a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#frmAddLeaveType").alert();
    $("#frmEditLeaveType").alert();
	
    //On showing the confirmation pop-up, add the user id at the end of the delete url action
    $('#modal-from-dom').on('show', function() {
            var id = $(this).data('id'),
            removeBtn = $(this).find('.danger');
            removeBtn.attr('href', removeBtn.attr('href') + id);
    })

    //Display a modal pop-up so as to confirm if a user has to be deleted or not
    $('.confirm-delete').on('click', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('#modal-from-dom').data('id', id).modal('show');
    });
});
</script>

