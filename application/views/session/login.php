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

$CI =& get_instance();
$CI->load->library('polyglot');
$CI->load->helper('language');
$this->lang->load('session', $language);
$this->lang->load('global', $language);?>

<h2><?php echo lang('session_login_title');?> &nbsp;
<a href="<?php echo lang('global_link_doc_page_login');?>" title="<?php echo lang('global_link_tooltip_documentation');?>" target="_blank" rel="nofollow"><i class="icon-question-sign"></i></a></h2>

<?php if($this->session->flashdata('msg')){ ?>
<div class="alert fade in" id="flashbox">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php echo $this->session->flashdata('msg'); ?>
</div>
<script type="text/javascript">
//Flash message
$(document).ready(function() {
    $(".alert").alert();
});
</script>
<?php } ?>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'loginFrom');
echo form_open('session/login', $attributes);
$languages = $CI->polyglot->nativelanguages($this->config->item('languages'));?>

    <input type="hidden" name="last_page" value="session/login" />
    <?php if (count($languages) == 1) { ?>
    <input type="hidden" name="language" value="<?php echo $language_code; ?>" />
    <?php } else { ?>
    <label for="language"><?php echo lang('session_login_field_language');?></label>
    <select name="language" id="language" onchange="Javascript:change_language();">
        <?php foreach ($languages as $lang_code => $lang_name) { ?>
        <option value="<?php echo $lang_code; ?>" <?php if ($language_code == $lang_code) echo 'selected'; ?>><?php echo $lang_name; ?></option>
        <?php }?>
    </select>
    <?php } ?>
    <label for="login"><?php echo lang('session_login_field_login');?></label>
    <input type="input" name="login" id="login" value="<?php echo set_value('login'); ?>" autofocus required /><br />
    <input type="hidden" name="CipheredValue" id="CipheredValue" />
</form>
    <input type="hidden" name="salt" id="salt" value="<?php echo $salt; ?>" />
    <label for="password"><?php echo lang('session_login_field_password');?></label>
    <input type="password" name="password" id="password" required /><br />
    <br />
    <button id="send" class="btn btn-primary"><?php echo lang('session_login_button_login');?></button><br />
    <br />
    <button id="cmdForgetPassword" class="btn btn-info"><i class="icon-envelope icon-white"></i>&nbsp;<?php echo lang('session_login_button_forget_password');?></button>
    
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.pers-brow.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jsencrypt.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>

<script type="text/javascript">
    //Refresh page language
    function change_language() {
        $.cookie('language', $('#language option:selected').val(), { expires: 90, path: '/'});
        $('#loginFrom').prop('action', '<?php echo base_url();?>session/language');
        $('#loginFrom').submit();
    }
    
    $(function () {
        //Memorize the last selected language with a cookie
        if($.cookie('language') != null) {
            var IsLangAvailable = 0 != $('#language option[value=' + $.cookie('language') + ']').length;
            if ($.cookie('language') != "<?php echo $language_code; ?>") {
                //Test if the former selected language is into the list of available languages
                if (IsLangAvailable) {
                    $('#language option[value="' + $.cookie('language') + '"]').attr('selected', 'selected');
                    $('#loginFrom').prop('action', '<?php echo base_url();?>session/language');
                    $('#loginFrom').submit();
                }
            }
        }
        
        $('#send').click(function() {
            var encrypt = new JSEncrypt();
            encrypt.setPublicKey($('#pubkey').val());
            //Encrypt the concatenation of the password and the salt
            var encrypted = encrypt.encrypt($('#password').val() + $('#salt').val());
            $('#CipheredValue').val(encrypted);
            $('#loginFrom').submit();
        });
        
        //If the user has forgotten his password, send an e-mail
        $('#cmdForgetPassword').click(function() {
            if ($('#login').val() == "") {
                bootbox.alert("<?php echo lang('session_login_msg_empty_login');?>");
            } else {
                $.ajax({
                   type: "POST",
                   url: "<?php echo base_url(); ?>session/forgetpassword",
                   data: { login: $('#login').val() }
                 })
                 .done(function(msg) {
                   switch(msg) {
                       case "OK":
                           bootbox.alert("<?php echo lang('session_login_msg_password_sent');?>");
                           break;
                       case "UNKNOWN":
                           bootbox.alert("<?php echo lang('session_login_flash_bad_credentials');?>");
                           break;
                   }
                 });
            }
        });
        
        //Validate the form if the user press enter key in password field
        $('#password').keypress(function(e){
            if(e.keyCode==13)
            $('#send').click();
        });
    });
</script>

<textarea id="pubkey" style="visibility:hidden;"><?php echo $public_key; ?></textarea>