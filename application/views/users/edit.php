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
$this->lang->load('users', $language);
$this->lang->load('global', $language);
CI_Controller::get_instance()->load->library('language');?>

<h2><?php echo lang('users_edit_title');?><?php echo $users_item['id']; ?> &nbsp;
<a href="http://www.leave-management-system.org/en/documentation/page-create-a-new-user/" title="<?php echo lang('global_link_tooltip_documentation');?>" target="_blank"><i class="icon-question-sign"></i></a>
</h2>

<?php echo validation_errors(); ?>

<?php if (isset($_GET['source'])) {
    echo form_open('users/edit/' . $users_item['id'] .'?source=' . $_GET['source']);
} else {
    echo form_open('users/edit/' . $users_item['id']);
} ?>

    <input type="hidden" name="id" value="<?php echo $users_item['id']; ?>" required /><br />

    <label for="firstname"><?php echo lang('users_edit_field_firstname');?></label>
    <input type="input" name="firstname" value="<?php echo $users_item['firstname']; ?>" required /><br />

    <label for="lastname"><?php echo lang('users_edit_field_lastname');?></label>
    <input type="input" name="lastname" value="<?php echo $users_item['lastname']; ?>" required /><br />

    <label for="login"><?php echo lang('users_edit_field_login');?></label>
    <input type="input" name="login" value="<?php echo $users_item['login']; ?>" required /><br />
	
    <label for="email"><?php echo lang('users_edit_field_email');?></label>
    <input type="email" id="email" name="email" value="<?php echo $users_item['email']; ?>" required /><br />
		
    <label for="role[]"><?php echo lang('users_edit_field_role');?></label>
    <select name="role[]" multiple="multiple" size="2">
    <?php foreach ($roles as $roles_item): ?>
        <option value="<?php echo $roles_item['id'] ?>" <?php if ((((int)$roles_item['id']) & ((int) $users_item['role']))) echo "selected" ?>><?php echo $roles_item['name'] ?></option>
    <?php endforeach ?>
    </select>

    <br />
    <input type="hidden" name="manager" id="manager" value="<?php echo $users_item['manager']; ?>" /><br />
    <label for="txtManager"><?php echo lang('users_edit_field_manager');?></label>
    <div class="input-append">
        <input type="text" id="txtManager" name="txtManager" value="<?php echo $manager_label; ?>" required readonly/>
        <a id="cmdSelfManager" class="btn btn-primary"><?php echo lang('users_edit_button_self');?></a>
        <a id="cmdSelectManager" class="btn btn-primary"><?php echo lang('users_edit_button_select');?></a>
    </div><br />
    <i><?php echo lang('users_edit_field_manager_description');?></i>
    <br /><br />

    <label for="contract"><?php echo lang('users_edit_field_contract');?></label>
    <select name="contract">
    <?php foreach ($contracts as $contract): ?>
        <option value="<?php echo $contract['id'] ?>" <?php if ($contract['id'] == $users_item['contract']) echo "selected"; ?>><?php echo $contract['name']; ?></option>
    <?php endforeach ?>
    </select>
    
    <input type="hidden" name="entity" id="entity" value="<?php echo $users_item['organization']; ?>" /><br />
    <label for="txtEntity"><?php echo lang('users_edit_field_entity');?></label>
    <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" value="<?php echo $organization_label; ?>" required readonly />
        <a id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('users_edit_button_select');?></a>
    </div>
    <br />
    
    <input type="hidden" name="position" id="position" value="<?php echo $users_item['position']; ?>" /><br />
    <label for="txtPosition"><?php echo lang('users_create_field_position');?></label>
    <div class="input-append">
        <input type="text" id="txtPosition" name="txtPosition" value="<?php echo $position_label; ?>" required readonly />
        <a id="cmdSelectPosition" class="btn btn-primary"><?php echo lang('users_edit_button_select');?></a>
    </div>    
    <br />
    
    <label for="viz_datehired"><?php echo lang('users_edit_field_hired');?></label>
    <input type="text" id="viz_datehired" name="viz_datehired" value="<?php 
$date = new DateTime($users_item['datehired']);
echo $date->format(lang('global_date_format'));
?>" /><br />
    <input type="hidden" name="datehired" id="datehired" /><br />
    
    <label for="identifier"><?php echo lang('users_edit_field_identifier');?></label>
    <input type="text" name="identifier" value="<?php echo $users_item['identifier']; ?>" /><br />
    
    <label for="language"><?php echo lang('users_edit_field_language');?></label>
    <select name="language">
         <?php 
         $languages = $this->language->nativelanguages($this->config->item('languages'));
         foreach ($languages as $code => $language): ?>
        <option value="<?php echo $code; ?>" <?php if ($code == $users_item['language']) echo "selected" ?>><?php echo $language; ?></option>
        <?php endforeach ?>
    </select>
    
    <br />
    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('users_edit_button_update');?></button>
    &nbsp;
    <?php if (isset($_GET['source'])) {?>
        <a href="<?php echo base_url() . $_GET['source']; ?>" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('users_edit_button_cancel');?></a>
    <?php } else {?>
        <a href="<?php echo base_url();?>users" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('users_edit_button_cancel');?></a>
    <?php } ?>
</form>

<div id="frmSelectManager" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectManager').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_edit_popup_manager_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectManagerBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_manager();" class="btn secondary"><?php echo lang('users_edit_popup_manager_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectManager').modal('hide');" class="btn secondary"><?php echo lang('users_edit_popup_manager_button_cancel');?></a>
    </div>
</div>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_edit_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn secondary"><?php echo lang('users_edit_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn secondary"><?php echo lang('users_edit_popup_entity_button_cancel');?></a>
    </div>
</div>

<div id="frmSelectPosition" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectPosition').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_edit_popup_position_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectPositionBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_position();" class="btn secondary"><?php echo lang('users_edit_popup_position_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectPosition').modal('hide');" class="btn secondary"><?php echo lang('users_edit_popup_position_button_cancel');?></a>
    </div>
</div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui-1.10.4.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<script type="text/javascript">
    
    function select_manager() {
        var manager = $('#employees .row_selected td:first').text();
        var text = $('#employees .row_selected td:eq(1)').text();
        text += ' ' + $('#employees .row_selected td:eq(2)').text();
        $('#manager').val(manager);
        $('#txtManager').val(text);
        $("#frmSelectManager").modal('hide');
    }
    
    function select_entity() {
        var entity = $('#organization').jstree('get_selected')[0];
        var text = $('#organization').jstree().get_text(entity);
        $('#entity').val(entity);
        $('#txtEntity').val(text);
        $("#frmSelectEntity").modal('hide');
    }
    
    function select_position() {
        var position = $('#positions .row_selected td:first').text();
        var text = $('#positions .row_selected td:eq(1)').text();
        $('#position').val(position);
        $('#txtPosition').val(text);
        $("#frmSelectPosition").modal('hide');
    }

    $(document).ready(function() {
        $("#viz_datehired").datepicker({
            changeMonth: true,
            changeYear: true,
            altFormat: "yy-mm-dd",
            altField: "#datehired"
        }, $.datepicker.regional['<?php echo $language_code;?>']);
        
        //Popup select position
        $("#cmdSelectManager").click(function() {
            $("#frmSelectManager").modal('show');
            $("#frmSelectManagerBody").load('<?php echo base_url(); ?>users/employees');
        });
        
        //Popup select position
        $("#cmdSelectPosition").click(function() {
            $("#frmSelectPosition").modal('show');
            $("#frmSelectPositionBody").load('<?php echo base_url(); ?>positions/select');
        });
        
        //Popup select entity
        $("#cmdSelectEntity").click(function() {
            $("#frmSelectEntity").modal('show');
            $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
        });

        //Load alert forms
        $("#frmSelectEntity").alert();
        //Prevent to load always the same content (refreshed each time)
        $('#frmSelectEntity').on('hidden', function() {
            $(this).removeData('modal');
        });
        //Self manager button
        $("#cmdSelfManager").click(function() {
            $("#manager").val('-1');
            $('#txtManager').val('<?php echo lang('users_edit_field_manager_alt');?>');
        });
    });
</script>
