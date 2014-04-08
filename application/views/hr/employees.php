
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
            <th>E-mail</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($users as $users_item): ?>
    <tr>
        <td>
            <a href="<?php echo base_url();?>users/<?php echo $users_item['id'] ?>" title="View user"><?php echo $users_item['id'] ?></a>
            &nbsp;
            <a href="<?php echo base_url();?>users/<?php echo $users_item['id'] ?>" title="view user details"><i class="icon-eye-open"></i></a>
            &nbsp;
            <a href="<?php echo base_url();?>entitleddays/user/<?php echo $users_item['id'] ?>" data-target="#frmEntitledDays" data-toggle="modal" title="entitled days"><i class="icon-edit"></i></a>
        </td>
        <td><?php echo $users_item['firstname'] ?></td>
        <td><?php echo $users_item['lastname'] ?></td>
        <td><?php echo $users_item['email'] ?></td>
    </tr>
<?php endforeach ?>
	</tbody>
</table>
	</div>
</div>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

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
    $("#frmEntitledDays").alert();
});
</script>

