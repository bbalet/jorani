<?php
/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jorani is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('requests', $language);
$this->lang->load('datatable', $language);
$this->lang->load('global', $language);?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('requests_delegations_title');?> <span class="muted">(<?php echo $name; ?>)</span></h2>

<div class="row-fluid"><div class="span12"><?php echo lang('requests_delegations_description');?></div></div>

<table id="delegations">
<thead>
    <tr>
      <th>&nbsp;</th>
      <th><?php echo lang('requests_delegations_thead_employee');?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($delegations as $delegation) { ?>
    <tr data-id="<?php echo $delegation['id']; ?>">
      <td><a href="#" onclick="delete_delegation(<?php echo $delegation['id'] ?>);" title="<?php echo lang('requests_delegations_thead_tip_delete');?>"><i class="icon-remove"></i></a></td>
      <td><?php echo $delegation['delegate_name']; ?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
    
<div class="row-fluid"><div class="span12">&nbsp;</div></div>
<button id="cmdAddDelegate" class="btn btn-primary" onclick="$('#frmSelectDelegate').modal('show');"><i class="icon-plus-sign icon-white"></i>&nbsp;<?php echo lang('requests_delegations_button_add');?></button>
<div class="row-fluid"><div class="span12">&nbsp;</div></div>

    </div>
</div>

<div id="frmSelectDelegate" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectDelegate').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('requests_delegations_popup_delegate_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectDelegateBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_delegate();" class="btn secondary"><?php echo lang('requests_delegations_popup_delegate_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectDelegate').modal('hide');" class="btn secondary"><?php echo lang('requests_delegations_popup_delegate_button_cancel');?></a>
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

<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    var oTable;     //datatable
    
    function delete_delegation(id) {
        bootbox.confirm("<?php echo lang('requests_delegations_confirm_delete_message');?>",
            "<?php echo lang('requests_delegations_confirm_delete_cancel');?>",
            "<?php echo lang('requests_delegations_confirm_delete_yes');?>", function(result) {
            if (result) {
                $('#frmModalAjaxWait').modal('show');
                $.ajax({
                    url: "<?php echo base_url();?>requests/ajax/delegations/delete",
                    type: "POST",
                    data: { manager_id: <?php echo $id; ?>,
                        delegation_id: id
                    }
                  }).done(function() {
                      oTable.rows('tr[data-id="' + id + '"]').remove().draw();
                      $('#frmModalAjaxWait').modal('hide');
                  });
                }
        });
    }

    function select_delegate() {
        employee = $('#employees .row_selected td:first').text();
        if (employee != "") {
            name = $('#employees .row_selected td:eq(1)').text();
            name += ' ' + $('#employees .row_selected td:eq(2)').text();
            $('#frmSelectDelegate').modal('hide');
            if (parseInt(employee) != parseInt('<?php echo $id; ?>')) {
                $('#frmModalAjaxWait').modal('show');
                $.ajax({
                    url: "<?php echo base_url();?>requests/ajax/delegations/add",
                    type: "POST",
                    data: { manager_id: <?php echo $id; ?>,
                            delegate_id: $('#employees .row_selected td:first').text()
                        }
                  }).done(function(id) {
                      if (id != 'null') {
                        htmlRow = '<tr data-id="' + id + '">' +
                                  '<td><a href="#" onclick="delete_delegation(' + id + ');" title="<?php echo lang('requests_delegations_thead_tip_delete');?>"><i class="icon-remove"></i></a></td>' +
                                  '<td>' + name + '</td>' +
                              '</tr>';
                          objRow=$(htmlRow);
                          oTable.row.add(objRow).draw();
                      }
                      $('#frmModalAjaxWait').modal('hide');
                });
            }
        } else {
            $('#frmSelectDelegate').modal('hide');
        }
    }
    
    
    $(function () {
        
        //Transform the HTML table in a fancy datatable
        oTable = $('#delegations').DataTable({
                    "order": [[ 1, "desc" ]],
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
            
        //Popup select delegate
        $("#cmdAddDelegate").click(function() {
            $("#frmSelectDelegate").modal('show');
            $("#frmSelectDelegateBody").load('<?php echo base_url(); ?>users/employees');
        });
    });
</script>
