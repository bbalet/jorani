<?php
/**
 * This view displays the list of employees.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<div class="row-fluid">
    <div class="span12">
        
<h2><?php echo lang('hr_employees_title');?>&nbsp;<?php echo $help;?></h2>

<?php echo $flash_partial_view;?>

<div class="row-fluid">
    <div class="span4">
        <input type="hidden" name="entity" id="entity" />
         <label for="txtEntity"><?php echo lang('hr_employees_field_entity');?>
            <div class="input-append">
                <input type="text" id="txtEntity" name="txtEntity" readonly />
                <a id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('hr_employees_button_select');?></a>
            </div>
         </label>
    </div>
    <div class="span4">
      <input type="checkbox" id="chkIncludeChildren" /> <?php echo lang('hr_employees_field_subdepts');?>
    </div>
    <div class="span4">
      <?php echo lang('hr_employees_description');?>
    </div>
</div>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="users" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('hr_employees_thead_id');?></th>
            <th><?php echo lang('hr_employees_thead_firstname');?></th>
            <th><?php echo lang('hr_employees_thead_lastname');?></th>
            <th><?php echo lang('hr_employees_thead_email');?></th>
            <th><?php echo lang('hr_employees_thead_entity');?></th>
            <th><?php echo lang('hr_employees_thead_contract');?></th>
            <th><?php echo lang('hr_employees_thead_manager');?></th>
            <th><?php echo lang('hr_employees_thead_identifier');?></th>
            <th><?php echo lang('hr_employees_thead_datehired');?></th>
            <th><?php echo lang('hr_employees_thead_position');?></th>
        </tr>
    </thead>
    <tbody class="context" data-toggle="context" data-target="#context-menu">
    </tbody>
</table>

        </div>
</div>
    
<div class="row-fluid">
    <div class="span12">
      <a href="<?php echo base_url();?>users/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp;<?php echo lang('hr_employees_button_create_user');?></a>
      &nbsp;
      <a href="#" id="cmdExportEmployees" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp;<?php echo lang('hr_employees_button_export');?></a>
      &nbsp;
        <div class="btn-group dropup">
            <button id="cmdSelection" class="btn dropdown-toggle btn-primary" data-toggle="dropdown">
              <i class="fa fa-pencil"></i>&nbsp;Selection&nbsp;<span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="#" id="cmdSelectManager"><i class="fa fa-user"></i>&nbsp;Select Manager</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div id="frmSelectManager" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectManager').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('hr_employees_popup_manager_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectManagerBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_manager();" class="btn btn-primary"><?php echo lang('OK');?></a>
        <a href="#" onclick="$('#frmSelectManager').modal('hide');" class="btn btn-danger"><?php echo lang('Cancel');?></a>
    </div>
</div>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('hr_employees_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn secondary"><?php echo lang('OK');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn secondary"><?php echo lang('Cancel');?></a>
    </div>
</div>

<div id="context-menu">
  <ul class="dropdown-menu" role="menu">
        <li><a tabindex="-1" href="#" data-action="<?php echo base_url();?>hr/leaves/create/{id}"><i class="icon-plus"></i>&nbsp;<?php echo lang('hr_employees_thead_link_create_leave');?></a></li>
        <li><a tabindex="-1" href="#" data-action="<?php echo base_url();?>users/edit/{id}?source=hr%2Femployees"><i class="icon-pencil"></i>&nbsp;<?php echo lang('hr_employees_thead_tip_edit');?></a></li>
        <li><a tabindex="-1" href="#" data-action="<?php echo base_url();?>entitleddays/user/{id}"><i class="icon-edit"></i>&nbsp;<?php echo lang('hr_employees_thead_tip_entitlment');?></a></li>
        <li><a tabindex="-1" href="#" data-action="<?php echo base_url();?>hr/leaves/{id}"><i class="icon-list-alt"></i>&nbsp;<?php echo lang('hr_employees_thead_link_leaves');?></a></li>
        <?php if ($this->config->item('disable_overtime') == FALSE) { ?>
        <li><a tabindex="-1" href="#" data-action="<?php echo base_url();?>hr/overtime/{id}"><i class="icon-list-alt"></i>&nbsp;<?php echo lang('hr_employees_thead_link_extra');?></a></li>
        <?php } ?>
        <li><a tabindex="-1" href="#" data-action="<?php echo base_url();?>hr/counters/employees/{id}"><i class="icon-info-sign"></i>&nbsp;<?php echo lang('hr_employees_thead_link_balance');?></a></li>
        <li><a tabindex="-1" href="#" data-action="<?php echo base_url();?>hr/presence/employees/{id}"><i class="fa fa-pie-chart"></i>&nbsp;<?php echo lang('hr_employees_thead_link_presence');?></a></li>
        <li><a tabindex="-1" href="#" data-action="<?php echo base_url();?>calendar/year/{id}"><i class="icon-calendar"></i>&nbsp;<?php echo lang('hr_employees_thead_link_calendar');?></a></li>
        <li><a tabindex="-1" href="#" data-action="<?php echo base_url();?>requests/delegations/{id}"><i class="icon-share-alt"></i>&nbsp;<?php echo lang('hr_employees_thead_link_delegation');?></a></li>
  </ul>
</div>

<div class="modal hide fade" id="frmContextMenu">
    <div class="modal-body">
        <a class="context-mobile" href="<?php echo base_url();?>hr/leaves/create/{id}"><i class="icon-plus"></i>&nbsp;<?php echo lang('hr_employees_thead_link_create_leave');?></a><br />
        <a class="context-mobile" href="<?php echo base_url();?>users/edit/{id}?source=hr%2Femployees"><i class="icon-pencil"></i>&nbsp;<?php echo lang('hr_employees_thead_tip_edit');?></a><br />
        <a class="context-mobile" href="<?php echo base_url();?>entitleddays/user/{id}"><i class="icon-edit"></i>&nbsp;<?php echo lang('hr_employees_thead_tip_entitlment');?></a><br />
        <a class="context-mobile" href="<?php echo base_url();?>hr/leaves/{id}"><i class="icon-list-alt"></i>&nbsp;<?php echo lang('hr_employees_thead_link_leaves');?></a><br />
        <?php if ($this->config->item('disable_overtime') == FALSE) { ?>
        <a class="context-mobile" href="<?php echo base_url();?>hr/overtime/{id}"><i class="icon-list-alt"></i>&nbsp;<?php echo lang('hr_employees_thead_link_extra');?></a><br />
        <?php } ?>
        <a class="context-mobile" href="<?php echo base_url();?>hr/counters/employees/{id}"><i class="icon-info-sign"></i>&nbsp;<?php echo lang('hr_employees_thead_link_balance');?></a><br />
        <a class="context-mobile" href="<?php echo base_url();?>hr/presence/employees/{id}"><i class="fa fa-pie-chart" style="color:black;"></i>&nbsp;<?php echo lang('hr_employees_thead_link_presence');?></a><br />
        <a class="context-mobile" href="<?php echo base_url();?>calendar/year/{id}"><i class="icon-calendar"></i>&nbsp;<?php echo lang('hr_employees_thead_link_calendar');?></a><br />
        <a class="context-mobile" href="<?php echo base_url();?>requests/delegations/{id}"><i class="icon-share-alt"></i>&nbsp;<?php echo lang('hr_employees_thead_link_delegation');?></a>
  </div>
</div>

<div class="modal hide" id="frmModalAjaxWait" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <h1><?php echo lang('global_msg_wait');?></h1>
        </div>
        <div class="modal-body">
            <img src="<?php echo base_url();?>assets/images/loading.gif"  align="middle">
        </div>
 </div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?php echo base_url();?>assets/datatable/buttons/css/buttons.dataTables.min.css" rel="stylesheet"/>
<link href="<?php echo base_url();?>assets/datatable/colreorder/css/colReorder.dataTables.min.css" rel="stylesheet">
<link href="<?php echo base_url();?>assets/datatable/select/css/select.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/buttons/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/buttons/js/buttons.colVis.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/colreorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/select/js/dataTables.select.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.pers-brow.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/context.menu.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/toe.min.js"></script>

<script type="text/javascript">
var entity = 0; //Root of the tree by default
var entityName = '';
var includeChildren = true;
var contextObject;
var oTable;

//Handle choose of entity with the modal form "select an entity". Update cookie with selected values
function select_entity() {
    entity = $('#organization').jstree('get_selected')[0];
    entityName = $('#organization').jstree().get_text(entity);
    includeChildren = $('#chkIncludeChildren').is(':checked');
    $('#entity').val(entity);
    $('#txtEntity').val(entityName);
    $.cookie('entity', entity);
    $.cookie('entityName', entityName);
    $.cookie('includeChildren', includeChildren);
    $("#frmSelectEntity").modal('hide');
    //Refresh datatable
    $('#frmModalAjaxWait').modal('show');
    oTable.ajax.url('<?php echo base_url();?>hr/employees/entity/' + entity + '/' + includeChildren)
        .load(function() {
            $("#frmModalAjaxWait").modal('hide');
        }, true);
}

//Popup select manager: on click OK, find the id of all selected employees and update their manager.
function select_manager() {
    var employees = $('#employees').DataTable();
    if ( employees.rows({ selected: true }).any() ) {
        var manager_id = employees.rows({selected: true}).data()[0][0];
        var employeeIds = [];;
        //Get the list of selected employees into datatable users
       oTable.rows({selected: true}).every( function () {
           employeeIds.push(this.data().id);
        });
        //Call a web service that changes the manager of a list of employees
        $.ajax({
            url: "<?php echo base_url();?>hr/employees/edit/manager",
            type: "POST",
            dataType: 'json',
            data: {
                    manager: manager_id,
                    employees: JSON.stringify({employeeIds})
                }
          }).done(function() {
              oTable.ajax.reload();
              $('#frmModalAjaxWait').modal('hide');
        });
    }
    $("#frmSelectManager").modal('hide');
}

//Prevent text selection after double click
function clearSelection() {
    if(document.selection && document.selection.empty) {
        document.selection.empty();
    } else if(window.getSelection) {
        var sel = window.getSelection();
        sel.removeAllRanges();
    }
}

$(function () {
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
    
    //Handle a context menu of the DataTable
    $('.context').contextmenu({
        before: function (e, element, target) {
            e.preventDefault();
            if (oTable.data().any()) {
                contextObject = e.target;
                return true;
            } else {
                return false;
            }
        },
        onItem: function(context,e) {
            var action = null;
            if (e != "a") {
                action = $(e.target).closest("a").data("action");
            } else {
                action = $(e.target).data("action");
            }
            var id = $(contextObject).closest("tr").find('td:eq(0)').text();
            var url = action.replace("{id}", id.trim());
            window.location = url;
        }
      });
        
    //Taphold on mobile, display contextual menu as a popup
    $(document).on('taphold', '.context', function(e){
        id = $(e.target).closest("tr").find('td:eq(0)').text();
        $("#frmContextMenu").modal('show');
        $('.context-mobile').each(function() {
            action =  $(this).attr( 'href');
            var url = action.replace("{id}", id.trim());
            $(this).attr( 'href', url);
        });
      });
      
    //On double click, display contextual menu as a popup
    $(document).on('dblclick', '.context', function (e) {
        clearSelection();
        id = $(e.target).closest("tr").find('td:eq(0)').text();
        $("#frmContextMenu").modal('show');
        $('.context-mobile').each(function() {
            action =  $(this).attr( 'href');
            var url = action.replace("{id}", id.trim());
            $(this).attr( 'href', url);
        });
    });
    
    //On keying ESC, hide context menu
    $("body").on("keyup", function(e){
        if (e.keyCode == 27) {
            if ($('#frmContextMenu').hasClass('in')) {
                $('#frmContextMenu').modal('hide');
            }
        }
    });
    
    //Cookie has value ? take -1 by default
    if($.cookie('entity') != null) {
        entity = $.cookie('entity');
        entityName = $.cookie('entityName');
        includeChildren = $.cookie('includeChildren');
        //Parse boolean value contained into the string
        includeChildren = $.parseJSON(includeChildren.toLowerCase());
        $('#txtEntity').val(entityName);
        $('#chkIncludeChildren').prop('checked', includeChildren);
    } else { //Set default value
        $.cookie('entity', entity);
        $.cookie('entityName', entityName);
        $.cookie('includeChildren', includeChildren);
    }    

    //Transform the HTML table in a fancy datatable:
    // * Column ID cannot be moved or hidden because it is used for contextual actions
    oTable = $('#users').DataTable({
            "ajax": '<?php echo base_url();?>hr/employees/entity/' + entity + '/' + includeChildren,
            columns: [
                { data: "id" },
                { data: "firstname" },
                { data: "lastname" },
                { data: "email" },
                { data: "entity" },
                { data: "contract" },
                { data: "manager_name" },
                { data: "identifier" },
                { data: {
                        _: "datehired.display",
                        sort: "datehired.timestamp"
                    },
                    "orderDataType": "dom-text", "type": "numeric"
                },
                { data: "position" }
            ],
            stateSave: true,
            select: 'multiple',
            dom: 'Bfrtip',
            buttons: [
                            {
                                extend: 'pageLength',
                                text: '<?php echo lang('datatable_pagination');?>'
                            },
                            {
                                extend: 'colvis',
                                columns: ':not(:first-child)',
                                postfixButtons: [
                                    {
                                        extend: 'colvisRestore',
                                        text: '<?php echo lang('datatable_colvisRestore');?>'
                                    }
                                ]
                            }
            ],
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [
                    '<?php echo lang('datatable_10_rows');?>',
                    '<?php echo lang('datatable_25_rows');?>',
                    '<?php echo lang('datatable_50_rows');?>',
                    '<?php echo lang('datatable_all_rows');?>' 
                ]
            ],
            colReorder: {
                fixedColumnsLeft: 1
            },
        language: {
            buttons: {
                colvis: '<?php echo lang('datatable_colvis');?>'
            },
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
        },
    });
    
    //Popup select entity
    $("#cmdSelectEntity").click(function() {
        $("#frmSelectEntity").modal('show');
        $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
    });
    
    //Popup select manager
    $("#cmdSelectManager").click(function() {
        if (oTable.rows({selected: true}).any()) {
            $("#frmSelectManager").modal('show');
            $("#frmSelectManagerBody").load('<?php echo base_url(); ?>users/employees');
        }
        else {
            bootbox.alert("<?php echo lang('hr_employees_multiple_edit_selection_msg');?>");
        }
    });
    
    //If we opt-in the include children box, we'll recursively include the children of the selected entity
    //and the attached employees
    $("#chkIncludeChildren").on('change', function() {
        includeChildren = $('#chkIncludeChildren').is(':checked');
        $.cookie('includeChildren', includeChildren);
        //Refresh datatable
        $('#frmModalAjaxWait').modal('show');
        oTable.ajax.url('<?php echo base_url();?>hr/employees/entity/' + entity + '/' + includeChildren)
            .load(function() {
                $("#frmModalAjaxWait").modal('hide');
            }, true);
    });
    
    //On click button export, call the export to Excel view
    $("#cmdExportEmployees").click(function() {
        window.location = '<?php echo base_url();?>hr/employees/export/' + entity + '/' + includeChildren;
    });
});
</script>
