<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('organization', $language);?>

<h1><?php echo lang('organization_index_title');?></h1>

<div class="row-fluid">
    <div class="span4">
        <input type="text" placeholder="Search for an entity" id="txtSearch" />
        <button id="cmdSearch" class="btn btn-primary">Search</button>
        <div style="text-align: left;" id="organization"></div>
    </div>
    <div class="span8">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="collaborators" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>E-mail</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <br /><br />
        <button id="cmdAddEmployee" class="btn btn-primary">Attach an Employee</button>
        <button id="cmdRemoveEmployee" class="btn btn-primary">Detach an Employee</button>
    </div>
</div>

<style>
    tr.row_selected td{background-color:#b0bed9 !important;}
</style>

<div id="frmConfirmDelete" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmConfirmDelete').modal('hide')" class="close">&times;</a>
         <h3>Delete User</h3>
    </div>
    <div class="modal-body">
        <p>You are about to delete one user, this procedure is irreversible.</p>
        <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn danger" id="lnkDeleteUser">Yes</a>
        <a href="javascript:$('#frmConfirmDelete').modal('hide')" class="btn secondary">No</a>
    </div>
</div>

<div id="frmAddEmployee" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmAddEmployee').modal('hide')" class="close">&times;</a>
         <h3>Add an Employee</h3>
    </div>
    <div class="modal-body" id="frmAddEmployeeBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="javascript:add_employee();" class="btn secondary">OK</a>
        <a href="javascript:$('#frmAddEmployee').modal('hide')" class="btn secondary">Cancel</a>
    </div>
</div>

<div id="frmError" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmError').modal('hide')" class="close">&times;</a>
         <h3>An error occured</h3>
    </div>
    <div class="modal-body" id="lblError"></div>
    <div class="modal-footer">
        <a href="javascript:$('#frmError').modal('hide')" class="btn secondary">OK</a>
    </div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<link rel="stylesheet" href='<?php echo base_url(); ?>assets/jsTree/themes/default/style.css' type="text/css" media="screen, projection" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/jsTree/jstree.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/datatable.ajax.reload.js"></script>

<script type="text/javascript">
    //In order to manipulate datable object
    var oTable;
    
    function add_employee() {
        var id = $('#employees .row_selected td:first').text();
        var entity = $('#organization').jstree('get_selected')[0];
        $.ajax({
            type: "GET",
            url: "<?php echo base_url(); ?>organization/addemployee",
            data: { 'user': id, 'entity': entity }
          })
          .done(function( msg ) {
            //Update table of users
            oTable.fnReloadAjax("<?php echo base_url(); ?>organization/employees?id=" + entity);
            $("#frmAddEmployee").modal('hide');
          });
    }
    
    $(function () {
        
        //On confirm the deletion of the node, launch heavy cascade deletion
        $("#lnkDeleteUser").click(function() {
            $.ajax({
                type: "GET",
                url: "<?php echo base_url(); ?>organization/delete",
                data: { 'entity': $('#frmConfirmDelete').data('id') }
              })
              .done(function( msg ) {
                $("#organization").jstree("select_node", "0"); 
                $("#organization").jstree("refresh");
                $("#frmConfirmDelete").modal('hide');
              });
        });
       
        //Attach an employee to an entity
        $("#cmdAddEmployee").click(function() {
            if ($("#organization").jstree('get_selected').length == 1) {
                $("#frmAddEmployee").modal('show');
                $("#frmAddEmployeeBody").load('<?php echo base_url(); ?>users/employees');
            } else {
                $("#lblError").text("Please select one entity in the organization (treeview on the right).");
                $("#frmError").modal('show');
            }
        });

        //Remove an employee to an entity
        $("#cmdRemoveEmployee").click(function() {
            var id = $('#collaborators .row_selected td:first').text();
            if (id != "") {
                if ($("#organization").jstree('get_selected').length == 1) {
                    var entity = $('#organization').jstree('get_selected')[0];
                    $.ajax({
                        type: "GET",
                        url: "<?php echo base_url(); ?>organization/delemployee",
                        data: { 'user': id }
                      })
                      .done(function( msg ) {
                        //Update table of users
                        oTable.fnReloadAjax("<?php echo base_url(); ?>organization/employees?id=" + entity);
                    });
                } else {
                    $("#lblError").text("Please select one entity in the organization (treeview on the right).");
                    $("#frmError").modal('show');
                }
            } else {
                $("#lblError").text("Please select one employee in the table of users belonging to the selected entity.");
                $("#frmError").modal('show');
                $("#frmErrorEmployee").modal('show');
            }
        });

        //Load alert forms
        $("#frmAddEmployee").alert();
        //Prevent to load always the same content (refreshed each time)
        $('#frmAddEmployee').on('hidden', function() {
            $(this).removeData('modal');
        });
        
        //Search in the treeview
        $("#cmdSearch").click(function () {
            $("#organization").jstree("search", $("#txtSearch").val());
        });
        
        $('#organization').jstree({
            rules : {
                /*multiple   : false,*/
                deletable  : [ "folder" ],
                creatable  : [ "folder" ],
                draggable  : [ "folder" ],
                dragrules  : [ "folder * folder", "folder inside root" ],
                renameable : "all"
              },
                          /*ui : { select_multiple_modifier : false },*/
            'core' : {
              'data' : {
                'url' : function (node) {
                  return node.id === '#' ? 
                    'organization/root' : 
                    'organization/children';
                },
                'data' : function (node) {
                  return { 'id' : node.id };
                }
              },
              'check_callback' : true
            },
            "plugins" : [
              "contextmenu", "dnd", "search",
              "state", "types", "sort", "unique"
            ]
        })
        .on('delete_node.jstree', function (e, data) {
            e.preventDefault();
            var id = data.node.id;
            if (id == 0) {
                $("#lblError").text("You cannot delete the root entity.");
                $("#frmError").modal('show');
                $("#organization").jstree("refresh");
            } else {
                $('#frmConfirmDelete').data('id', id).modal('show');
            }
        })
        .on('create_node.jstree', function (e, data) {
            $.get('organization/create', { 'id' : data.node.parent, 'position' : data.position, 'text' : data.node.text })
                .done(function (d) {
                        data.instance.set_id(data.node, d.id);
                    })
                    .fail(function() {
                        data.instance.refresh();
                    });
                })
                .on('rename_node.jstree', function(e, data) {
                    $.get('organization/rename', {'id': data.node.id, 'text': data.text})
                        .fail(function() {
                            data.instance.refresh();
                        });
                })
                .on('move_node.jstree', function(e, data) {
                    $.get('organization/move', {'id': data.node.id, 'parent': data.parent, 'position': data.position})
                        .fail(function() {
                            data.instance.refresh();
                        });
                })
                .on('copy_node.jstree', function(e, data) {
                    $.get('organization/copy', {'id': data.original.id, 'parent': data.parent, 'position': data.position})
                        .always(function() {
                            data.instance.refresh();
                        });
                })
                .on('changed.jstree', function(e, data) {
                    if (data && data.selected && data.selected.length) {
                        oTable.fnReloadAjax("<?php echo base_url(); ?>organization/employees?id=" + data.selected.join(':'));
                    }
                });

            //Transform the HTML table in a fancy datatable
            oTable = $('#collaborators').dataTable({
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                    //As the datatable is populated with Ajax we need to add a callback this way
                    $('td', nRow).on('click', function() {
                        $("#collaborators tbody tr").removeClass('row_selected');
                        $(nRow).addClass("row_selected");
                    });
		}
            });
        });
</script>
