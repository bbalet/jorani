<?php 
/**
 * This partial view is intended to be used in a modal. It allows to manage
 * custom lists of employees created by a user. An example of use is into the
 * tabular calendar as an alternative selection (instead of an entity, we can
 * choose a list).
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.6.0
 */
?>

<div class="row-fluid">
    <div class="span12">

        <label for="list">TODO List</label>

<div class="input-prepend input-append">
    <button id="cmdDeleteList" class="btn btn-danger" title="<?php echo lang('organization_lists_button_delete_list');?>"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
    <button id="cmdRenameList" class="btn btn-primary" title="<?php echo lang('organization_lists_button_edit_list');?>"><i class="fa fa-pencil" aria-hidden="true"></i></button>
    <button id="cmdCreateList" class="btn btn-primary" title="<?php echo lang('organization_lists_button_add_list');?>"><i class="fa fa-plus" aria-hidden="true"></i></button>
    <select id="list" name="list">
        <option value="" selected="true"></option>
<?php foreach ($lists as $listItem): ?>
        <option value="<?php echo $listItem['id'];?>"><?php echo $listItem['name'];?></option>
<?php endforeach ?>
    </select>
    <button id="cmdAddUsers" class="btn btn-primary" title="<?php echo lang('organization_lists_button_add_users');?>"><i class="fa fa-user-plus" aria-hidden="true"></i></button>
    <button id="cmdRemoveUsers" class="btn btn-primary" title="<?php echo lang('organization_lists_button_add_users');?>"><i class="fa fa-user-times" aria-hidden="true"></i></button>
</div>
        
<table cellpadding="0" cellspacing="0" border="0" class="display" id="employeesOrgList" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('organization_lists_employees_thead_id');?></th>
            <th><?php echo lang('organization_lists_employees_thead_firstname');?></th>
            <th><?php echo lang('organization_lists_employees_thead_lastname');?></th>
            <th><?php echo lang('organization_lists_employees_thead_entity');?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
	</div>
</div>

    
    <button id="cmdDiscardOrgList" class="btn btn-warning"><?php echo lang('Cancel');?></button>
    <button id="cmdUseThisOrgList" class="btn btn-primary"><?php echo lang('OK');?></button>

<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<link href="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/css/jquery.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/js/jquery.dataTables.min.js"></script>
$lang['organization_lists_employees_prompt_new'] = 'Name of the list';
$lang['organization_lists_employees_confirm_delete'] = 'Are you sure that you want to delete this list?';
$lang['organization_lists_employees_prompt_rename'] = 'New name of the list';

<script type="text/javascript">
var listId;
var listName;
var employeesOrgList;   //DataTable object

function toggleCommands() {
    if ($('#list').val() == "") {
        $('#cmdDeleteList').prop("disabled", true);
        $('#cmdRenameList').prop("disabled", true);
        $('#cmdAddUsers').prop("disabled", true);
        $('#cmdRemoveUsers').prop("disabled", true);
    } else {
        $('#cmdDeleteList').prop("disabled", false);
        $('#cmdRenameList').prop("disabled", false);
        $('#cmdAddUsers').prop("disabled", false);
        $('#cmdRemoveUsers').prop("disabled", false);
        //Reload the list of employees
        listId = $('#list').val();
        var urlListEmployees = '<?php echo base_url();?>organization/lists/employees?list=' + listId;
        employeesOrgList.ajax.url( urlListEmployees ).load();
    }
}
    
    
$(function () {
    //Setup Ajax/CSRF
<?php if ($this->config->item('csrf_protection') == TRUE) {?>
    $.ajaxSetup({
        data: {
            <?php echo $this->security->get_csrf_token_name();?>: "<?php echo $this->security->get_csrf_hash();?>",
        }
    });
<?php }?>    

    //Global Ajax error handling mainly used for session expiration
    $( document ).ajaxError(function(event, jqXHR, settings, errorThrown) {
        $('#frmModalAjaxWait').modal('hide');
        if (jqXHR.status == 401) {
            bootbox.alert("<?php echo lang('global_ajax_timeout');?>", function() {
                //After the login page, we'll be redirected to the current page 
               location.reload();
            });
        } else { //Oups
            bootbox.alert("<?php echo lang('global_ajax_error');?>");
        }
      });
    

    //Toggle buttons
    toggleCommands();
    
    //Transform the HTML table in a fancy datatable
    employeesOrgList = $('#employeesOrgList').DataTable({
        select: 'multiple',
        pageLength: 5,
            columns: [
                { data: "id" },
                { data: "firstname" },
                { data: "lastname" },
                { data: "entity" }
            ],
        language: {
            decimal:            "<?php echo lang('datatable_sInfoThousands');?>",
            processing:       "<?php echo lang('datatable_sProcessing');?>",
            search:              "<?php echo lang('datatable_sSearch');?>",
            lengthMenu:     "<?php echo lang('datatable_sLengthMenu');?>",
            info:                   "<?php echo lang('datatable_sInfo');?>",
            infoEmpty:          "<?php echo lang('datatable_sInfoEmpty');?>",
            infoFiltered:       "<?php echo lang('datatable_sInfoFiltered');?>",
            infoPostFix:        "<?php echo lang('datatable_sInfoPostFix');?>",
            loadingRecords: "<?php echo lang('datatable_sLoadingRecords');?>",
            zeroRecords:    "<?php echo lang('datatable_sZeroRecords');?>",
            emptyTable:     "<?php echo lang('datatable_sEmptyTable');?>",
            paginate: {
                first:          "<?php echo lang('datatable_sFirst');?>",
                previous:   "<?php echo lang('datatable_sPrevious');?>",
                next:           "<?php echo lang('datatable_sNext');?>",
                last:           "<?php echo lang('datatable_sLast');?>"
            },
            aria: {
                sortAscending:  "<?php echo lang('datatable_sSortAscending');?>",
                sortDescending: "<?php echo lang('datatable_sSortDescending');?>"
            }
        }
    });
        
    $("#list").on('change', function() {
        toggleCommands();
    });

/*
cmdCreateList
cmdDeleteList
cmdRenameList
cmdAddUsers
cmdRemoveUsers
*/
    //Create a new list by ajax. Add the new option into select control
    $("#cmdCreateList").click(function() {
        
    });

});
</script>
