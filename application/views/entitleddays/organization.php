<?php 
/**
 * This view allows an HR admin to credit entitled days to many employees
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.5.0
 */
?>    

<h2><?php echo lang('entitleddays_organization_title');?> &nbsp;<?php echo $help;?></h2>

<p>Vous pouvez également ajouter des jours de congé au niveau d'un contrat ou individuel à un seul employé.</p>

<div class="row-fluid">
    <div class="span4">
        <div class="input-append">
            <input type="text" class="input-medium" placeholder="<?php echo lang('organization_index_field_search_placeholder');?>" id="txtSearch" />
            <button id="cmdClearSearch" class="btn btn-primary"><i class="icon-remove icon-white"></i></button>
            <button id="cmdSearch" class="btn btn-primary"><i class="icon-search icon-white"></i>&nbsp;<?php echo lang('organization_index_button_search');?></button>
        </div>
        <div style="text-align: left;" id="organization"></div>
    </div>
    
    
    <div class="span4">
        <h3><?php echo lang('organization_index_title_employees');?></h3>
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="collaborators" width="100%">
            <thead>
                <tr>
                    <th><?php echo lang('organization_selection_thead_id');?></th>
                    <th><?php echo lang('organization_selection_thead_fullname');?></th>
                    <th><?php echo lang('organization_selection_thead_entry_date');?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <br />
        <button id="cmdAddEmployee" class="btn btn-primary"><?php echo lang('organization_index_button_add_employee');?></button>
        <button id="cmdRemoveEmployee" class="btn btn-primary"><?php echo lang('organization_index_button_remove_employee');?></button>
    </div>
    
    
    <div class="span4">
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="collaborators" width="100%">
            <thead>
                <tr>
                    <th><?php echo lang('organization_selection_thead_id');?></th>
                    <th><?php echo lang('organization_selection_thead_fullname');?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    
</div>

<div class="row-fluid">
    <div class="span12">
        <label for="txtDays">Days
            <input name="txtDays" />
        </label>
        <label for="txtType">Type
            <input name="txtType" />
        </label>
        <label for="txtDescription">Description
            <input name="txtDescription" />
        </label>
    </div>
</div>  

<style>
    tr.row_selected td{background-color:#b0bed9 !important;}
</style>





<div class="modal hide" id="frmModalAjaxWait" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <h1><?php echo lang('global_msg_wait');?></h1>
        </div>
        <div class="modal-body">
            <img src="<?php echo base_url();?>assets/images/loading.gif"  align="middle">
        </div>
 </div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<link rel="stylesheet" href='<?php echo base_url(); ?>assets/jsTree/themes/default/style.css' type="text/css" media="screen, projection" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/jsTree/jstree.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>

<script type="text/javascript">
    //In order to manipulate datable object
    var oTable;
    //Mutex to prevent rename the root node
    var createMtx = false;
    

    

    
    function delete_supervisor() {
        $('#frmModalAjaxWait').modal('show');
        var entity = $('#organization').jstree('get_selected')[0];
        $.ajax({
            type: "GET",
            url: "<?php echo base_url(); ?>organization/setsupervisor",
            data: { 'user': null, 'entity': entity }
          })
          .done(function(msg) {
            //Update field with the name of employee (the supervisor)
            $('#txtSupervisor').val("");
            $('#frmModalAjaxWait').modal('hide');
          });
    }
    
    $(function () {
        //On confirm the deletion of the node, launch heavy cascade deletion
        $("#lnkDeleteEntity").click(function() {
            $.ajax({
                type: "GET",
                url: "<?php echo base_url(); ?>organization/delete",
                data: { 'entity': $('#frmConfirmDelete').data('id') }
              })
              .done(function(msg) {
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
                $("#lblError").text("<?php echo lang('organization_index_error_msg_select_entity');?>");
                $("#frmError").modal('show');
            }
        });



        
        //Search in the treeview
        $("#cmdSearch").click(function () {
            $("#organization").jstree("search", $("#txtSearch").val(), true, true);
        });
        $("#txtSearch").keyup(function(e) {
            if (e.keyCode == 13) { $("#organization").jstree("search", $("#txtSearch").val(), true, true); }   // enter key
        });
        
        //Clear the Search option in the treeview
        $("#cmdClearSearch").click(function () {
            $("#organization").jstree("clear_search");
        });
        $(document).keyup(function(e) {
            if (e.keyCode == 27) { $("#organization").jstree("clear_search"); }   // escape key
        });
        
        
        //Transform the HTML table in a fancy datatable
        oTable = $('#collaborators').DataTable({
            fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                //As the datatable is populated with Ajax we need to add a callback this way
                $('td', nRow).on('click', function() {
                    $("#collaborators tbody tr").removeClass('row_selected');
                    $(nRow).addClass("row_selected");
                });
            },
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
        
        //Initialize the tree of the organization
        $('#organization').jstree({
            contextmenu: {
                items: function(n) {
                    var tmp = $.jstree.defaults.contextmenu.items();
                    tmp.create.label = '<?php echo lang('treeview_context_menu_create');?>';
                    tmp.rename.label = '<?php echo lang('treeview_context_menu_rename');?>';
                    tmp.remove.label = '<?php echo lang('treeview_context_menu_remove');?>';
                    tmp.ccp.label = '<?php echo lang('treeview_context_menu_edit');?>';
                    tmp.ccp.submenu.copy.label = '<?php echo lang('treeview_context_menu_copy');?>';
                    tmp.ccp.submenu.cut.label = '<?php echo lang('treeview_context_menu_cut');?>';
                    tmp.ccp.submenu.paste.label = '<?php echo lang('treeview_context_menu_paste');?>';
                    return tmp;
                }
            },
            core: {
              multiple : false,
              data: {
                url: function (node) {
                  return node.id === '#' ? 
                    '<?php echo base_url(); ?>organization/root' : 
                    '<?php echo base_url(); ?>organization/children';
                },
                data: function (node) {
                  return { 'id' : node.id };
                }
              },
              check_callback : true
            },
            plugins: ["search", "state", "sort", "unique"]
        })
        .on('changed.jstree', function(e, data) {
            if (data && data.selected && data.selected.length) {
                $('#frmModalAjaxWait').modal('show');
                oTable.ajax.url("<?php echo base_url(); ?>organization/employeesDateHired?id=" + data.selected.join(':'))
                    .load(function() {
                            $("#frmModalAjaxWait").modal('hide');
                        }, true);
            }
        });
    });
</script>
