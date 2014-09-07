<?php 
/*
 * This file is part of lms.
 *
 * lms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('global', $language);
$this->lang->load('leavetypes', $language);?>

<h1><?php echo lang('hr_leaves_type_title');?> &nbsp;
<a href="http://www.leave-management-system.org/edit-leave-types.html" title="<?php echo lang('global_link_tooltip_documentation');?>" target="_blank"><i class="icon-question-sign"></i></a>
</h1>

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


<table class="table table-bordered table-hover">
<thead>
    <tr>
      <th><?php echo lang('hr_leaves_type_thead_id');?></th>
      <th><?php echo lang('hr_leaves_type_thead_name');?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($leavetypes as $type) { ?>
    <tr>
      <td><?php echo $type['id'] ?> &nbsp; 
          <?php if ($type['id'] !=0 ) { ?>
          <a href="#" class="confirm-delete" data-id="<?php echo $type['id'];?>" title="<?php echo lang('hr_leaves_type_thead_tip_delete');?>"><i class="icon-trash"></i></a></td>
          <?php } ?>
      <td>
          <a href="<?php echo base_url();?>leavetypes/edit/<?php echo $type['id'] ?>" data-target="#frmEditLeaveType" data-toggle="modal" title="<?php echo lang('hr_leaves_type_thead_tip_edit');?>"><i class="icon-pencil"></i></a>
          &nbsp; <?php echo $type['name']; ?></td>
    </tr>
  <?php } ?>
  <?php if (count($leavetypes) == 0) { ?>
    <tr>
        <td colspan="5"><?php echo lang('hr_leaves_type_not_found');?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>

<div class="row-fluid">
	<div class="span12">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3">
      <a href="<?php echo base_url();?>leavetypes/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; <?php echo lang('hr_leaves_type_button_export');?></a>
    </div>
    <div class="span3">
        <a href="<?php echo base_url();?>leavetypes/create" class="btn btn-primary" data-target="#frmAddLeaveType" data-toggle="modal"><i class="icon-plus-sign icon-white"></i>&nbsp; <?php echo lang('hr_leaves_type_button_create');?></a>
    </div>
    <div class="span6">&nbsp;</div>
</div>

<div id="frmAddLeaveType" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmAddLeaveType').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('hr_leaves_popup_create_title');?></h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmAddLeaveType').modal('hide');" class="btn secondary"><?php echo lang('hr_leaves_popup_create_button_cancel');?></a>
    </div>
</div>

<div id="frmEditLeaveType" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmEditLeaveType').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('hr_leaves_popup_update_title');?></h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmEditLeaveType').modal('hide');" class="btn secondary"><?php echo lang('hr_leaves_popup_update_button_cancel');?></a>
    </div>
</div>

<div id="frmDeleteLeaveType" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmDeleteLeaveType').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('hr_leaves_popup_delete_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('hr_leaves_popup_delete_description');?></p>
        <p><?php echo lang('hr_leaves_popup_delete_confirm');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeleteLeaveType" class="btn danger"><?php echo lang('hr_leaves_popup_delete_button_yes');?></a>
        <a href="#" onclick="$('#frmDeleteLeaveType').modal('hide');" class="btn secondary"><?php echo lang('hr_leaves_popup_delete_button_no');?></a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#frmAddLeaveType").alert();
    $("#frmEditLeaveType").alert();
    $("#frmDeleteLeaveType").alert();
	
    //On showing the confirmation pop-up, add the user id at the end of the delete url action
    $('#frmDeleteLeaveType').on('show', function() {
        var link = "<?php echo base_url();?>leavetypes/delete/" + $(this).data('id');
        $("#lnkDeleteLeaveType").attr('href', link);
    })

    //Display a modal pop-up so as to confirm if a user has to be deleted or not
    $('.confirm-delete').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#frmDeleteLeaveType').data('id', id).modal('show');
    });
    
    //Prevent to load always the same content (refreshed each time)
    $('#frmAddLeaveType').on('hidden', function() {
        $(this).removeData('modal');
    });
    $('#frmEditLeaveType').on('hidden', function() {
        $(this).removeData('modal');
    });
    $('#frmDeleteLeaveType').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>

