
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
            <a href="<?php echo base_url();?>users/<?php echo $users_item['id'] ?>" title="View user"><?php echo $users_item['id'] ?></a>
            &nbsp;
            <div class="pull-right">
                <a href="<?php echo base_url();?>users/<?php echo $users_item['id'] ?>" title="view user details"><i class="icon-eye-open"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>users/edit/<?php echo $users_item['id'] ?>" title="edit user details"><i class="icon-pencil"></i></a>
                &nbsp;
                <a href="#" class="confirm-delete" data-id="<?php echo $users_item['id'];?>" title="delete user"><i class="icon-trash"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>users/reset/<?php echo $users_item['id'] ?>" title="reset password" data-target="#frmResetPwd" data-toggle="modal"><i class="icon-lock"></i></a>
            </div>
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
    <div class="span2">
        <a href="<?php echo base_url();?>users/import" class="btn btn-primary" data-target="#frmImportUsers" data-toggle="modal"><i class="icon-arrow-up icon-white"></i>&nbsp; Import users</a>
    </div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="frmConfirmDelete" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmConfirmDelete').modal('hide');" class="close">&times;</a>
         <h3>Delete User</h3>
    </div>
    <div class="modal-body">
        <p>You are about to delete one user, this procedure is irreversible.</p>
        <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn danger" id="lnkDeleteUser">Yes</a>
        <a href="#" onclick="$('#frmConfirmDelete').modal('hide');" class="btn secondary">No</a>
    </div>
</div>

<div id="frmResetPwd" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmResetPwd').modal('hide');" class="close">&times;</a>
         <h3>Change password</h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmResetPwd').modal('hide');" class="btn secondary">Cancel</a>
    </div>
</div>

<div id="frmImportUsers" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmImportUsers').modal('hide');" class="close">&times;</a>
         <h3>Import Users</h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmImportUsers').modal('hide');" class="btn secondary">Cancel</a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#users').dataTable();
    $("#frmResetPwd").alert();
    $("#frmImportUsers").alert();
	
    //On showing the confirmation pop-up, add the user id at the end of the delete url action
    $('#frmConfirmDelete').on('show', function() {
        var link = "<?php echo base_url();?>users/delete/" + $(this).data('id');
        $("#lnkDeleteUser").attr('href', link);
    })

    //Display a modal pop-up so as to confirm if a user has to be deleted or not
    //We build a complex selector because datatable does horrible things on DOM...
    //a simplier selector doesn't work when the delete is on page >1 
    $("#users tbody").on('click', '.confirm-delete',  function(){
        var id = $(this).data('id');
        $('#frmConfirmDelete').data('id', id).modal('show');
    });
    
    //Prevent to load always the same content (refreshed each time)
    $('#frmConfirmDelete').on('hidden', function() {
        $(this).removeData('modal');
    });
    $('#frmResetPwd').on('hidden', function() {
        $(this).removeData('modal');
    });
    $('#frmImportUsers').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>
