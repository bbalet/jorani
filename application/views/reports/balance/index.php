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
$this->lang->load('reports', $language);
$this->lang->load('global', $language);?>
        
<h1><?php echo lang('reports_balance_title');?> &nbsp;<?php echo $help;?></h1>

<div class="row-fluid">
    <div class="span4">
        <label for="refdate"><?php echo lang('reports_balance_date_field');?></label>
        <div class="input-append">
        <input type="text" name="refdate" id="refdate" />
        </div>
    </div>
    <div class="span4">	
        <label for="txtEntity"><?php echo lang('reports_balance_field_entity');?></label>
        <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" readonly />
        <button id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('reports_balance_button_entity');?></button>
        </div>
    </div>
    <div class="span4">
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

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div id="reportResult"></div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

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

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.pers-brow.js"></script>
<script type="text/javascript">

var entity = -1; //Id of the selected entity
var entityName = ''; //Label of the selected entity
var includeChildren = true;

function select_entity() {
    entity = $('#organization').jstree('get_selected')[0];
    entityName = $('#organization').jstree().get_text(entity);
    $('#txtEntity').val(entityName);
    $("#frmSelectEntity").modal('hide');
    $.cookie('rep_entity', entity);
    $.cookie('rep_entityName', entityName);
    $.cookie('rep_includeChildren', includeChildren);
}

$(document).ready(function() {
    
    //Init datepicker widget
    moment.locale('<?php echo $language_code;?>');
    $("#refdate").val(moment().format('L'));
    $('#refdate').datepicker();    
    
    $("#frmSelectEntity").alert();
    
    $("#cmdSelectEntity").click(function() {
        $("#frmSelectEntity").modal('show');
        $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
    });
    
    $('#cmdExportReport').click(function() {
        var rtpQuery = '<?php echo base_url();?>reports/balance/export';
        var tmpUnix = moment($("#refdate").datepicker("getDate")).utc().unix();
        if (entity != -1) {
            rtpQuery += '?entity=' + entity;
        } else {
            rtpQuery += '?entity=0';
        }
        rtpQuery += '&refDate=' + tmpUnix;
        if ($('#chkIncludeChildren').prop('checked') == true) {
            rtpQuery += '&children=true';
        } else {
            rtpQuery += '&children=false';
        }
        document.location.href = rtpQuery;
    });
    
    $('#cmdLaunchReport').click(function() {
        var ajaxQuery = '<?php echo base_url();?>reports/balance/execute';
        var tmpUnix = moment($("#refdate").datepicker("getDate")).utc().unix();
        if (entity != -1) {
            ajaxQuery += '?entity=' + entity;
        } else {
            ajaxQuery += '?entity=0';
        }
        ajaxQuery += '&refDate=' + tmpUnix;
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
    
    //Toggle day offs displays
    $('#chkIncludeChildren').on('change', function() {
        includeChildren = $('#chkIncludeChildren').prop('checked');
        $.cookie('rep_includeChildren', includeChildren);
    });
    
    //Cookie has value ? take -1 by default
    if($.cookie('rep_entity') != null) {
        entity = $.cookie('rep_entity');
        entityName = $.cookie('rep_entityName');
        includeChildren = $.cookie('rep_includeChildren');
        //Parse boolean values
        includeChildren = $.parseJSON(includeChildren.toLowerCase());
        $('#txtEntity').val(entityName);
        $('#chkIncludeChildren').prop('checked', includeChildren);
    } else { //Set default value
        $.cookie('rep_entity', entity);
        $.cookie('rep_entityName', entityName);
        $.cookie('rep_includeChildren', includeChildren);
    }
});
</script>
