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

$CI =& get_instance();
$CI->load->library('polyglot');
$CI->load->helper('language');
$this->lang->load('users', $language);
$this->lang->load('global', $language);?>

<h2><?php echo lang('users_create_title');?><?php echo $help;?></h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'target');
echo form_open('users/create', $attributes); ?>

    <input type="hidden" name="CipheredValue" id="CipheredValue" />
    
    <label for="firstname"><?php echo lang('users_create_field_firstname');?></label>
    <input type="text" name="firstname" id="firstname" required /><br />

    <label for="lastname"><?php echo lang('users_create_field_lastname');?></label>
    <input type="text" name="lastname" id="lastname" required /><br />

    <label for="role[]"><?php echo lang('users_create_field_role');?></label>
    <select name="role[]" multiple="multiple" size="2" required>
    <?php foreach ($roles as $roles_item): ?>
        <option value="<?php echo $roles_item['id'] ?>" <?php if ($roles_item['id'] == 2) echo "selected"; ?>><?php echo $roles_item['name'] ?></option>
    <?php endforeach ?>
    </select>

    <label for="login"><?php echo lang('users_create_field_login');?></label>
    <input type="text" name="login" id="login" required /><br />
    <div class="alert hide alert-error" id="lblLoginAlert">
        <button type="button" class="close" onclick="$('#lblLoginAlert').hide();">&times;</button>
        <?php echo lang('users_create_flash_msg_error');?>
    </div>

    <label for="email"><?php echo lang('users_create_field_email');?></label>
    <input type="email" id="email" name="email" required /><br />

    <input type="hidden" name="manager" id="manager" /><br />
    <label for="txtManager"><?php echo lang('users_create_field_manager');?></label>
    <div class="input-append">
        <input type="text" id="txtManager" name="txtManager" required readonly />
        <a id="cmdSelfManager" class="btn btn-primary"><?php echo lang('users_create_button_self');?></a>
        <a id="cmdSelectManager" class="btn btn-primary"><?php echo lang('users_create_button_select');?></a>
    </div><br />
    <i><?php echo lang('users_create_field_manager_description');?></i>
    <br /><br />

    <label for="contract"><?php echo lang('users_create_field_contract');?></label>
    <select name="contract" id="contract" class="selectized input-xlarge">
    <?php $index = 0;
         foreach ($contracts as $contract) { ?>
        <option value="<?php echo $contract['id'] ?>" <?php if ($index == 0) echo "selected"; ?>><?php echo $contract['name']; ?></option>
    <?php 
            $index++;
        } ?>
    </select>

    <input type="hidden" name="entity" id="entity" /><br />
    <label for="txtEntity"><?php echo lang('users_create_field_entity');?></label>
    <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" readonly />
        <a id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('users_create_button_select');?></a>
    </div>
    <br />

    <input type="hidden" name="position" id="position" /><br />
    <label for="txtPosition"><?php echo lang('users_create_field_position');?></label>
    <div class="input-append">
        <input type="text" id="txtPosition" name="txtPosition" readonly />
        <a id="cmdSelectPosition" class="btn btn-primary"><?php echo lang('users_create_button_select');?></a>
    </div>
    <br />

    <label for="viz_datehired"><?php echo lang('users_create_field_hired');?></label>
    <input type="text" id="viz_datehired" name="viz_datehired" /><br />
    <input type="hidden" name="datehired" id="datehired" />

    <label for="identifier"><?php echo lang('users_create_field_identifier');?></label>
    <input type="text" name="identifier" /><br />
    
    <label for="language"><?php echo lang('users_create_field_language');?></label>
    <select name="language">
         <?php 
         $languages = $CI->polyglot->nativelanguages($this->config->item('languages'));
         $default_lang = $CI->polyglot->language2code($this->config->item('language'));
         foreach ($languages as $code => $language): ?>
        <option value="<?php echo $code; ?>" <?php if ($code == $default_lang) echo "selected"; ?>><?php echo $language; ?></option>
        <?php endforeach ?>
    </select>
    
    <?php 
    $tzdef = $this->config->item('default_timezone');
    if ($tzdef == FALSE) $tzdef = 'Europe/Paris';
    $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);?>
    <label for="timezone"><?php echo lang('users_create_field_timezone');?></label>
    <select id="timezone" name="timezone" class="selectized input-xlarge">
    <?php foreach ($tzlist as $tz) { ?>
        <option value="<?php echo $tz ?>" <?php if ($tz == $tzdef) echo "selected"; ?>><?php echo $tz; ?></option>
    <?php 
            $index++;
        } ?>
    </select>

    <?php if ($this->config->item('ldap_basedn_db')) {?>
    <label for="ldap_path"><?php echo lang('users_create_field_ldap_path');?></label>
    <input type="text" class="input-xxlarge" name="ldap_path" />
    <?php }?>
</form>

    <?php if (!$this->config->item('ldap_enabled')) {?>
    <label for="password"><?php echo lang('users_create_field_password');?></label>
    <div class="input-append">
        <input type="password" name="password" id="password" required />
        <a class="btn" id="cmdGeneratePassword"><i class="icon-refresh"></i>&nbsp;<?php echo lang('users_create_button_generate_password');?></a>
    </div>
    <?php }?>
    <br />
    
    <button id="send" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('users_create_button_create');?></button>
    &nbsp;
    <a href="<?php echo base_url(); ?>users" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('users_create_button_cancel');?></a>

<div id="frmSelectManager" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectManager').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_create_popup_manager_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectManagerBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_manager();" class="btn secondary"><?php echo lang('users_create_popup_manager_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectManager').modal('hide');" class="btn secondary"><?php echo lang('users_create_popup_manager_button_cancel');?></a>
    </div>
</div>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_create_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn secondary"><?php echo lang('users_create_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn secondary"><?php echo lang('users_create_popup_entity_button_cancel');?></a>
    </div>
</div>

<div id="frmSelectPosition" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectPosition').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('users_create_popup_position_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectPositionBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_position();" class="btn secondary"><?php echo lang('users_create_popup_position_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectPosition').modal('hide');" class="btn secondary"><?php echo lang('users_create_popup_position_button_cancel');?></a>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url();?>assets/js/jsencrypt.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui-1.10.4.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/selectize.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/selectize.bootstrap2.css" />
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

    function validate_form() {
        result = false;
        var fieldname = "";
        if ($('#firstname').val() == "") fieldname = "firstname";
        if ($('#lastname').val() == "") fieldname = "lastname";
        //if ($('#role:selected').length == 0) fieldname = "role";
        if ($('#login').val() == "") fieldname = "login";
        if ($('#email').val() == "") fieldname = "email";
        if ($('#txtManager').val() == "") fieldname = "manager";
        if ($('#contract').val() == "") fieldname = "contract";
        //if ($('#txtEntity').val() == "") fieldname = "entity";
        //if ($('#txtPosition').val() == "") fieldname = "position";
        //if ($('#datehired').val() == "") fieldname = "datehired";
        //if ($('#identifier').val() == "") fieldname = "identifier";
        if ($('#password').val() == "") fieldname = "password";
        if (fieldname == "") {
            return true;
        } else {
            bootbox.alert(<?php echo lang('users_create_mandatory_js_msg');?>);
            return false;
        }
    }
    
    function submit_form() {
        var encrypt = new JSEncrypt();
        encrypt.setPublicKey($('#pubkey').val());
        var encrypted = encrypt.encrypt($('#password').val());
        $('#CipheredValue').val(encrypted);
        $('#target').submit();
    }
    
    /**
     * Generate a password of the specified length
     * @param int len   length of password to be generated
     * @returns string  generated password
     */
    function password_generator(len) {
        var length = (len)?(len):(10);
        var string = "abcdefghijklnopqrstuvwxyz";
        var numeric = '0123456789';
        var punctuation = '!@#$%;:?,./-=';
        var password = "";
        var character = "";
        while(password.length < length) {
            entity1 = Math.ceil(string.length * Math.random() * Math.random());
            entity2 = Math.ceil(numeric.length * Math.random() * Math.random());
            entity3 = Math.ceil(punctuation.length * Math.random() * Math.random());
            hold = string.charAt(entity1);
            hold = (entity1 % 2 == 0)?(hold.toUpperCase()):(hold);
            character += hold;
            character += numeric.charAt( entity2 );
            character += punctuation.charAt( entity3 );
            password = character;
        }
        return password;
    }
    
    $(function () {
        $("#viz_datehired").datepicker({
            changeMonth: true,
            changeYear: true,
            altFormat: "yy-mm-dd",
            altField: "#datehired"
        }, $.datepicker.regional['<?php echo $language_code;?>']);
        $("#lblLoginAlert").alert();
        
        $('#timezone').selectize();
        $('#contract').selectize();
        
        $("#cmdGeneratePassword").click(function() {
            $("#password").val(password_generator(<?php echo $this->config->item('password_length');?>));
        });
        
        //On any change on firstname or lastname fields, automatically build the
        //login identifier with first character of firstname and lastname
        $("#firstname").change(function() {
            $("#login").val($("#firstname").val().charAt(0).toLowerCase() +
                $("#lastname").val().toLowerCase());            
        });
        $("#lastname").change(function() {
            $("#lastname").val($("#lastname").val().toUpperCase());
            $("#login").val($("#firstname").val().charAt(0).toLowerCase() +
                $("#lastname").val().toLowerCase());            
        });
        
        //Check if the user has not exceed the number of entitled days
        $("#login").change(function() {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>users/check/login",
                data: { login: $("#login").val() }
                })
                .done(function( msg ) {
                    if (msg == "true") {
                        $("#lblLoginAlert").hide();
                    } else {
                        $("#lblLoginAlert").show();
                    }
                });
        });
        
        $('#send').click(function() {
            if (validate_form() == false) {
                //Error of validation
            } else {
                $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>users/check/login",
                data: { login: $("#login").val() }
                })
                .done(function( msg ) {
                    if (msg == "true") {
                        if ($('#contract').val() == "") {
                            bootbox.confirm("<?php echo lang('users_create_no_contract_confirm');?>", function(result) {
                                if (result == true) {
                                    submit_form();
                                }
                            });
                        } else {
                            submit_form()
                        }
                    } else {
                        bootbox.alert("<?php echo lang('users_create_login_check');?>");
                    }
                });
            }
        });
        
        //Popup select manager
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
            $('#txtManager').val('<?php echo lang('users_create_field_manager_alt');?>');
        });
    });

</script>

<textarea id="pubkey" style="visibility:hidden;"><?php echo $public_key; ?></textarea>
