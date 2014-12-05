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
$this->lang->load('reports', $language);?>
        
<h1><?php echo lang('reports_balance_title');?></h1>

<div class="row-fluid">
    <div class="span4">
        <label for="txtEntity"><?php echo lang('reports_balance_field_entity');?></label>
        <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" readonly />
        <button id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('reports_balance_button_entity');?></button>
        </div>
    </div>
    <div class="span8">
        <div class="pull-right">    
            <label class="checkbox">
                <input type="checkbox" id="chkIncludeChildren" checked /> <?php echo lang('reports_balance_field_subdepts');?>
            </label>
            &nbsp;
            <button class="btn btn-primary" id="cmdLaunchReport"><i class="icon-file icon-white"></i>&nbsp; <?php echo lang('reports_balance_button_launch');?></button>
            <button class="btn btn-primary" id="cmdExportReport"><i class="icon-file icon-white"></i>&nbsp; <?php echo lang('reports_balance_button_export');?></button>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="span6">&nbsp;</div>
    <div class="span3">
        
    </div>
</div>


<div id="reportResult"></div>

<div class="row-fluid">
	<div class="span12">&nbsp;</div>
</div>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('reports_balance_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn secondary"><?php echo lang('reports_balance_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn secondary"><?php echo lang('reports_balance_popup_entity_button_cancel');?></a>
    </div>
</div>

<script type="text/javascript">

var entity = -1; //Id of the selected entity
var text; //Label of the selected entity

function select_entity() {
    entity = $('#organization').jstree('get_selected')[0];
    text = $('#organization').jstree().get_text(entity);
    $('#txtEntity').val(text);
    $("#frmSelectEntity").modal('hide');
}

$(document).ready(function() {
    $("#frmSelectEntity").alert();
    
    $("#cmdSelectEntity").click(function() {
        $("#frmSelectEntity").modal('show');
        $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
    });
    
    $('#cmdExportReport').click(function() {
        var rtpQuery = '<?php echo base_url();?>reports/balance/export';
        if (entity != -1) {
            rtpQuery += '?entity=' + entity;
        } else {
            rtpQuery += '?entity=0';
        }
        if ($('#chkIncludeChildren').prop('checked') == true) {
            rtpQuery += '&children=true';
        } else {
            rtpQuery += '&children=false';
        }
        document.location.href = rtpQuery;
    });
    
    $('#cmdLaunchReport').click(function() {
        var ajaxQuery = '<?php echo base_url();?>reports/balance/execute';
        if (entity != -1) {
            ajaxQuery += '?entity=' + entity;
        } else {
            ajaxQuery += '?entity=0';
        }
        if ($('#chkIncludeChildren').prop('checked') == true) {
            ajaxQuery += '&children=true';
        } else {
            ajaxQuery += '&children=false';
        }
        $('#reportResult').html("<img src='<?php echo base_url();?>assets/images/loading.gif' />");
        
        $.ajax({
          url: ajaxQuery
        })
        .done(function( data ) {
              $('#reportResult').html(data);
        });

    });
});
</script>
