
<div class="row-fluid">
    <div class="span12">
	
<h1>List of users</h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="users" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>Login</th>
            <th>E-mail</th>
            <th>Role</th>
            <th>Manager</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($users as $users_item): ?>
    <tr>
        <td>
            <a href="<?php echo base_url();?>index.php/users/<?php echo $users_item['id'] ?>" title="View user"><?php echo $users_item['id'] ?></a>
            &nbsp;
            <a href="<?php echo base_url();?>index.php/users/<?php echo $users_item['id'] ?>" title="view user details"><i class="icon-eye-open"></i></a>
            &nbsp;
            <a href="<?php echo base_url();?>index.php/users/edit/<?php echo $users_item['id'] ?>" title="edit user details"><i class="icon-pencil"></i></a>
            &nbsp;
            <a href="#" class="confirm-delete" data-id="<?php echo $users_item['id'];?>" title="delete user"><i class="icon-trash"></i></a>
        </td>
        <td><?php echo $users_item['firstname'] ?></td>
        <td><?php echo $users_item['lastname'] ?></td>
        <td><?php echo $users_item['login'] ?></td>
        <td><?php echo $users_item['email'] ?></td>
        <td><?php echo $users_item['role'] ?></td>
        <td><?php echo $users_item['manager'] ?></td>
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
      <a href="<?php echo base_url();?>users/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; Export this list</a>
    </div>
    <div class="span2">
      <a href="<?php echo base_url();?>users/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp; Create a new user</a>
    </div>
    <div class="span2">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="modal-from-dom" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
         <h3>Delete User</h3>
    </div>
    <div class="modal-body">
        <p>You are about to delete one user, this procedure is irreversible.</p>
        <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
        <a href="<?php echo base_url();?>users/delete/" class="btn danger">Yes</a>
        <a href="javascript:$('#modal-from-dom').modal('hide')" class="btn secondary">No</a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	//Transform the HTML table in a fancy datatable
    $('#users').dataTable();
	
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
