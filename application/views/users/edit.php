<?php 
/**
 * This view allows to modify an employee record.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<h2><?php echo lang('users_edit_title');?><?php echo $users_item['id']; ?><?php echo $help;?></h2>

<?php echo validation_errors(); ?>

<?php if (isset($_GET['source'])) {
    echo form_open('users/edit/' . $users_item['id'] .'?source=' . $_GET['source']);
} else {
    echo form_open('users/edit/' . $users_item['id']);
} ?>

    <input type="hidden" name="id" value="<?php echo $users_item['id']; ?>" required /><br />

    <label for="firstname"><?php echo lang('users_edit_field_firstname');?></label>
    <input type="text" name="firstname" value="<?php echo $users_item['firstname']; ?>" required /><br />

    <label for="lastname"><?php echo lang('users_edit_field_lastname');?></label>
    <input type="text" name="lastname" value="<?php echo $users_item['lastname']; ?>" required /><br />

    <label for="login"><?php echo lang('users_edit_field_login');?></label>
    <input type="text" name="login" value="<?php echo $users_item['login']; ?>" required /><br />
	
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
    <select name="contract" id="contract" class="selectized input-xlarge">
    <?php foreach ($contracts as $contract): ?>
        <option value="<?php echo $contract['id'] ?>" <?php if ($contract['id'] == $users_item['contract']) echo "selected"; ?>><?php echo $contract['name']; ?></option>
    <?php endforeach ?>
        <option value="0" <?php if ($users_item['contract'] == 0 || is_null($users_item['contract'])) echo "selected"; ?>>&nbsp;</option>
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
         $languages = $this->polyglot->nativelanguages($this->config->item('languages'));
         foreach ($languages as $code => $language): ?>
        <option value="<?php echo $code; ?>" <?php if ($code == $users_item['language']) echo "selected"; ?>><?php echo $language; ?></option>
        <?php endforeach ?>
    </select>
    
    <?php 
        if (!is_null($users_item['timezone'])) {
            $tzdef = $users_item['timezone'];
        } else {
            $tzdef = $this->config->item('default_timezone');
            if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
        }
    $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);?>
    <label for="timezone"><?php echo lang('users_edit_field_timezone');?></label>
    <select id="timezone" name="timezone" class="selectized input-xlarge">
    <?php foreach ($tzlist as $tz) { ?>
        <option value="<?php echo $tz ?>" <?php if ($tz == $tzdef) echo "selected"; ?>><?php echo $tz; ?></option>
    <?php } ?>
    </select>
    
    <?php if ($this->config->item('ldap_basedn_db')) {?>
    <label for="ldap_path"><?php echo lang('users_edit_field_ldap_path');?></label>
    <input type="text" class="input-xxlarge" name="ldap_path" value="<?php echo $users_item['ldap_path']; ?>" />
    <?php }?>
    <br />
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
        <a href="#" onclick="select_manager();" class="btn"><?php echo lang('users_edit_popup_manager_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectManager').modal('hide');" class="btn"><?php echo lang('users_edit_popup_manager_button_cancel');?></a>
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
        <a href="#" onclick="select_entity();" class="btn"><?php echo lang('users_edit_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn"><?php echo lang('users_edit_popup_entity_button_cancel');?></a>
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
        <a href="#" onclick="select_position();" class="btn"><?php echo lang('users_edit_popup_position_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectPosition').modal('hide');" class="btn"><?php echo lang('users_edit_popup_position_button_cancel');?></a>
    </div>
</div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/selectize.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/selectize.bootstrap2.css" />
<script type="text/javascript">
    
    //Popup select postion: on click OK, find the user id for the selected line
    function select_manager() {
        var employees = $('#employees').DataTable();
        if ( employees.rows({ selected: true }).any() ) {
            var manager = employees.rows({selected: true}).data()[0][0];
            var text = employees.rows({selected: true}).data()[0][1] + ' ' + employees.rows({selected: true}).data()[0][2];
            $('#manager').val(manager);
            $('#txtManager').val(text);
        }
        $("#frmSelectManager").modal('hide');
    }
    
    //Popup select entity: on click OK, find the entity id for the selected node
    function select_entity() {
        var entity = $('#organization').jstree('get_selected')[0];
        var text = $('#organization').jstree().get_text(entity);
        $('#entity').val(entity);
        $('#txtEntity').val(text);
        $("#frmSelectEntity").modal('hide');
    }
    
    //Popup select postion: on click OK, find the position id for the selected line
    function select_position() {
        var positions = $('#positions').DataTable();
        if ( positions.rows({ selected: true }).any() ) {
            var position = positions.rows({selected: true}).data()[0][0];
            var text = positions.rows({selected: true}).data()[0][1];
            $('#position').val(position);
            $('#txtPosition').val(text);
        }
        $("#frmSelectPosition").modal('hide');
    }

    $(document).ready(function() {
        $("#viz_datehired").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: '<?php echo lang('global_date_js_format');?>',
            altFormat: "yy-mm-dd",
            altField: "#datehired"
        }, $.datepicker.regional['<?php echo $language_code;?>']);
        $("#viz_datehired").datepicker( "setDate", "<?php echo $date->format(lang('global_date_format'));?>");
        
        $('#timezone').selectize();
        $('#contract').selectize();
        
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
