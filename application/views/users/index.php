<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('users', $language);
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
        
<h1><?php echo lang('users_index_title');?></h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="users" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('users_index_thead_id');?></th>
            <th><?php echo lang('users_index_thead_firstname');?></th>
            <th><?php echo lang('users_index_thead_lastname');?></th>
            <th><?php echo lang('users_index_thead_login');?></th>
            <th><?php echo lang('users_index_thead_email');?></th>
            <th><?php echo lang('users_index_thead_role');?></th>
            <th><?php echo lang('users_index_thead_manager');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($users as $users_item): ?>
    <tr>
        <td>
            <a href="<?php echo base_url();?>users/<?php echo $users_item['id'] ?>" title="<?php echo lang('users_index_thead_tip_view');?>"><?php echo $users_item['id'] ?></a>
            &nbsp;
            <div class="pull-right">
                <a href="<?php echo base_url();?>users/<?php echo $users_item['id'] ?>" title="<?php echo lang('users_index_thead_tip_view');?>"><i class="icon-eye-open"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>users/edit/<?php echo $users_item['id'] ?>" title="<?php echo lang('users_index_thead_tip_edit');?>"><i class="icon-pencil"></i></a>
                &nbsp;
                <a href="#" class="confirm-delete" data-id="<?php echo $users_item['id'];?>" title="<?php echo lang('users_index_thead_tip_delete');?>"><i class="icon-trash"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>users/reset/<?php echo $users_item['id'] ?>" title="<?php echo lang('users_index_thead_tip_reset');?>" data-target="#frmResetPwd" data-toggle="modal"><i class="icon-lock"></i></a>
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
      <a href="<?php echo base_url();?>users/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp;<?php echo lang('users_index_button_export');?></a>
    </div>
    <div class="span2">
      <a href="<?php echo base_url();?>users/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp;<?php echo lang('users_index_button_create_user');?></a>
    </div>
    <div class="span2">
        &nbsp;
        <!--<a href="<?php echo base_url();?>users/import" class="btn btn-primary" data-target="#frmImportUsers" data-toggle="modal"><i class="icon-arrow-up icon-white"></i>&nbsp;<?php echo lang('users_index_button_import_user');?></a><//-->
    </div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="frmConfirmDelete" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmConfirmDelete').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_index_popup_delete_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('users_index_popup_delete_title');?></p>
        <p><?php echo lang('users_index_popup_delete_title');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn danger" id="lnkDeleteUser"><?php echo lang('users_index_popup_delete_button_yes');?></a>
        <a href="#" onclick="$('#frmConfirmDelete').modal('hide');" class="btn secondary"><?php echo lang('users_index_popup_delete_button_no');?></a>
    </div>
</div>

<div id="frmResetPwd" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h3><?php echo lang('users_index_popup_password_title');?></h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal"><?php echo lang('users_index_popup_password_button_cancel');?></button>
    </div>
</div>

<div id="frmImportUsers" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmImportUsers').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_index_button_export');?></h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmImportUsers').modal('hide');" class="btn secondary"><?php echo lang('users_index_button_export');?></a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#users').dataTable({
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
