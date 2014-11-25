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
 * along with lms.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('contract', $language);
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

<h1><?php echo lang('contract_index_title');?></h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="contracts" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('contract_index_thead_id');?></th>
            <th><?php echo lang('contract_index_thead_name');?></th>
            <th><?php echo lang('contract_index_thead_start');?></th>
            <th><?php echo lang('contract_index_thead_end');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($contracts as $contracts_item): ?>
    <tr>
        <td data-order="<?php echo $contracts_item['id']; ?>">
            <?php echo $contracts_item['id'] ?>
            &nbsp;
            <div class="pull-right">
                <a href="#" class="confirm-delete" data-id="<?php echo $contracts_item['id'];?>" title="<?php echo lang('contract_index_tip_delete');?>"><i class="icon-trash"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>contracts/<?php echo $contracts_item['id'] ?>" title="<?php echo lang('contract_index_tip_view');?>"><i class="icon-eye-open"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>contracts/edit/<?php echo $contracts_item['id'] ?>" title="<?php echo lang('contract_index_tip_edit');?>"><i class="icon-pencil"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>entitleddays/contract/<?php echo $contracts_item['id'] ?>" title="<?php echo lang('contract_index_tip_entitled');?>"><i class="icon-edit"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>contracts/<?php echo $contracts_item['id'] ?>/calendar" title="<?php echo lang('contract_index_tip_dayoffs');?>"><i class="icon-calendar"></i></a>
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
      <a href="<?php echo base_url();?>contracts/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; <?php echo lang('contract_index_button_export');?></a>
    </div>
    <div class="span3">
      <a href="<?php echo base_url();?>contracts/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp; <?php echo lang('contract_index_button_create');?></a>
    </div>
    <div class="span7">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="frmDeleteContract" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmDeleteContract').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('contract_index_popup_delete_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('contract_index_popup_delete_description');?></p>
        <p><?php echo lang('contract_index_popup_delete_confirm');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeleteContract" class="btn danger"><?php echo lang('contract_index_popup_delete_button_yes');?></a>
        <a href="#" onclick="$('#frmDeleteContract').modal('hide');" class="btn secondary"><?php echo lang('contract_index_popup_delete_button_no');?></a>
    </div>
</div>

<div id="frmEntitledDays" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmEntitledDays').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('contract_index_popup_entitled_title');?></h3>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmEntitledDays').modal('hide');" class="btn secondary"><?php echo lang('contract_index_popup_entitled_button_close');?></a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#contracts').dataTable({
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
