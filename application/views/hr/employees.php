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
$this->lang->load('hr', $language);
$this->lang->load('datatable', $language);
$this->lang->load('global', $language);?>

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
        
<h1><?php echo lang('hr_employees_title');?> &nbsp;
<a href="<?php echo lang('global_link_doc_page_list_employees');?>" title="<?php echo lang('global_link_tooltip_documentation');?>" target="_blank" rel="nofollow"><i class="icon-question-sign"></i></a>
</h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="users" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('hr_employees_thead_id');?></th>
            <th><?php echo lang('hr_employees_thead_firstname');?></th>
            <th><?php echo lang('hr_employees_thead_lastname');?></th>
            <th><?php echo lang('hr_employees_thead_email');?></th>
            <th><?php echo lang('hr_employees_thead_contract');?></th>
            <th><?php echo lang('hr_employees_thead_manager');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($users as $users_item): ?>
    <tr>
        <td data-order="<?php echo $users_item['id']; ?>">
            <?php echo $users_item['id'] ?>
            <div class="pull-right">
                &nbsp;
                <a href="<?php echo base_url();?>users/edit/<?php echo $users_item['id'] ?>?source=hr%2Femployees" title="<?php echo lang('hr_employees_thead_tip_edit');?>"><i class="icon-pencil"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>entitleddays/user/<?php echo $users_item['id'] ?>" title="<?php echo lang('hr_employees_thead_tip_entitlment');?>"><i class="icon-edit"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>hr/leaves/<?php echo $users_item['id'] ?>" title="<?php echo lang('hr_employees_thead_link_leaves');?>"><i class="icon-list-alt"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>hr/overtime/<?php echo $users_item['id'] ?>" title="<?php echo lang('hr_employees_thead_link_extra');?>"><i class="icon-list-alt"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>hr/counters/<?php echo $users_item['id'] ?>" title="<?php echo lang('hr_employees_thead_link_balance');?>"><i class="icon-info-sign"></i></a>
            </div>
        </td>
        <td><?php echo $users_item['firstname']; ?></td>
        <td><?php echo $users_item['lastname']; ?></td>
        <td><a href="mailto:<?php echo $users_item['email']; ?>"><?php echo $users_item['email']; ?></a></td>
        <td><?php echo $users_item['contract']; ?></td>
        <td><?php echo $users_item['manager_firstname'] . ' ' . $users_item['manager_lastname']; ?></td>
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
      <a href="<?php echo base_url();?>users/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp;<?php echo lang('hr_employees_button_create_user');?></a>
    </div>
    <div class="span2">
      <a href="<?php echo base_url();?>hr/employees/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp;<?php echo lang('hr_employees_button_export');?></a>
    </div>
    <div class="span8">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$(function () {
    //Transform the HTML table in a fancy datatable
    $('#users').dataTable({
                    "iDisplayLength": 50,
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
    $("#frmEntitledDays").alert();
    
    //Prevent to load always the same content (refreshed each time)
    $('#frmEntitledDays').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>

