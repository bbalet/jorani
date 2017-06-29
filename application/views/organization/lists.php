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

<div class="modal hide" id="frmModalAjaxWait" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <h1><?php echo lang('global_msg_wait');?></h1>
        </div>
        <div class="modal-body">
            <img src="<?php echo base_url();?>assets/images/loading.gif"  align="middle">
        </div>
</div>

<div id="frmSelectEmployees" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEmployees').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_create_popup_manager_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEmployeesBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_employees();" class="btn"><?php echo lang('OK');?></a>
        <a href="#" onclick="$('#frmSelectEmployees').modal('hide');" class="btn"><?php echo lang('Cancel');?></a>
    </div>
</div>

    
<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<link href="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?php echo base_url();?>assets/datatable/RowReorder-1.1.1/css/rowReorder.dataTables.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url();?>assets/datatable/Select-1.1.2/css/select.dataTables.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/Select-1.1.2/js/dataTables.select.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>/assets/datatable/RowReorder-1.1.1/js/dataTables.rowReorder.min.js"></script>
<script type="text/javascript">
var listId;
var listName;
var employeesOrgList;   //DataTable object

//If a list is selected, activate the controls and load the employees
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
        $('#frmModalAjaxWait').modal('show');
        employeesOrgList.ajax.url(urlListEmployees)
            .load(function() {
                $("#frmModalAjaxWait").modal('hide');
            }, true); 
    }
}

//Pick employees to be added into the list
function select_employees() {

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
        rowReorder: true,
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
    
    employeesOrgList.on( 'row-reorder', function ( e, diff, edit ) {
        var result = 'Reorder started on row: ' + edit.triggerRow.data()[1] + '<br>';
        for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
            var rowData = employeesOrgList.row( diff[i].node ).data(); 
            result += rowData[1] + ' updated to be in position ' +
                diff[i].newData + ' (was '+diff[i].oldData+')<br>';
        }
        alert( result );
    });
    
    $("#list").on('change', function() {
        toggleCommands();
    });


/*
$route['organization/lists/adduser'] = 'organization/listsAddUser';
$route['organization/lists/removeuser'] = 'organization/listsRemoveUsser';
$route['organization/lists/reorder'] = 'organization/listsReorder';

cmdAddUsers
cmdRemoveUsers
*/
    //Create a new list by ajax. Add the new option into select control
    $("#cmdCreateList").click(function() {
        bootbox.prompt("<?php echo lang('organization_lists_employees_prompt_new');?>",
          "<?php echo lang('Cancel');?>",
          "<?php echo lang('OK');?>", function(result) {
          if ((result !== null) && (result != '')) {
            listName = result;
            //Call ajax endpoint
            $('#frmModalAjaxWait').modal('show');
            $.ajax({
                url: "<?php echo base_url();?>organization/lists/create",
                type: "POST",
                data: {
                    name: listName
                }
              }).done(function( data ) {
                  $('#frmModalAjaxWait').modal('hide');
                  if ($.isNumeric(data)) {
                    $('#list').append($('<option>', {value: data, text: listName}));
                    $('#list option[value="' + data + '"]').attr('selected','selected');
                  } else {
                      bootbox.alert(data);
                  }
            });//ajax
          }//have prompt
        });//bootbox        
    });

    //Delete a list by ajax. Remove the option from the select control
    $("#cmdDeleteList").click(function() {
        bootbox.confirm("<?php echo lang('organization_lists_employees_confirm_delete');?>",
          "<?php echo lang('Cancel');?>",
          "<?php echo lang('OK');?>", function(result) {
          if (result === true) {
            listId = $('#list').val();
            //Call ajax endpoint
            $('#frmModalAjaxWait').modal('show');
            $.ajax({
                url: "<?php echo base_url();?>organization/lists/delete",
                type: "POST",
                data: {
                    id: listId
                }
              }).done(function( msg ) {
                  $('#frmModalAjaxWait').modal('hide');
                  if (msg == "") {
                    $('#list option:selected').remove();
                    $('#list').val('');
                  } else {
                      bootbox.alert(data);
                  }
            });//ajax
          }//have prompt
        });//bootbox        
    });

    //Rename a list by ajax. Change the option from the select control
    $("#cmdRenameList").click(function() {
        listId = $('#list').val();
        listName = $('#list option:selected').text();
        bootbox.prompt({
            title: "<?php echo lang('organization_lists_employees_prompt_rename');?>",
            value: listName,
            buttons: {
                confirm: {
                    label: "<?php echo lang('OK');?>"
                },
                cancel: {
                    label: "<?php echo lang('Cancel');?>"
                }
            },
            callback: function(result) {
                if ((result !== null) && (result != '')) {
                    listName = result;
                    //Call ajax endpoint
                    $('#frmModalAjaxWait').modal('show');
                    $.ajax({
                        url: "<?php echo base_url();?>organization/lists/rename",
                        type: "POST",
                        data: {
                            id: listId,
                            name: listName
                        }
                      }).done(function( msg ) {
                          $('#frmModalAjaxWait').modal('hide');
                          if (msg == "") {
                            $('#list option:selected').text(listName);
                          } else {
                              bootbox.alert(data);
                          }
                    });//ajax
                }//have prompt
            }//function
        });//bootbox        
    });

});
</script>
