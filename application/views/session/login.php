<?php 
/**
 * This view displays the login form. Its layout differs from other pages of the application.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.1.0
 */
?>

<?php if ($this->config->item('oauth2_enabled') == TRUE) { ?>
<script type="text/javascript" src="https://apis.google.com/js/client:platform.js?onload=start" async defer></script>
<script type="text/javascript">
    function start() {
      gapi.load('auth2', function() {
        auth2 = gapi.auth2.init({
          client_id: '<?php echo $this->config->item('oauth2_client_id');?>',
        });
      });
    }
</script>
<?php }?>

<style>
    body {
        background-image:url('<?php echo base_url();?>assets/images/login-background.jpg');
        background-size: 100% 100%;
        background-repeat: no-repeat;
    }
    
    .vertical-center {
        min-height: 90%;  /* Fallback for browsers not supporting vh unit */
        min-height: 90vh;
        display: flex;
        align-items: center;
    }
      
    .form-box {
        padding: 20px;
        border: 1px #e4e4e4 solid;
        border-radius: 4px;
        box-shadow: 0 0 6px #ccc;
        background-color: #fff;
    }
</style>

    <div class="row vertical-center">
        <div class="span3">&nbsp;</div>
            <div class="span6 form-box">
                <div class="row-fluid">
                    <div class="span6">
<h2><?php echo lang('session_login_title');?><?php echo $help;?></h2>

<?php echo $flash_partial_view;?>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'loginFrom');
echo form_open('session/login', $attributes);
$languages = $this->polyglot->nativelanguages($this->config->item('languages'));?>

    <input type="hidden" name="last_page" value="session/login" />
    <?php if (count($languages) == 1) { ?>
    <input type="hidden" name="language" value="<?php echo $language_code; ?>" />
    <?php } else { ?>
    <label for="language"><?php echo lang('session_login_field_language');?></label>
    <select class="input-medium" name="language" id="language">
        <?php foreach ($languages as $lang_code => $lang_name) { ?>
        <option value="<?php echo $lang_code; ?>" <?php if ($language_code == $lang_code) echo 'selected'; ?>><?php echo $lang_name; ?></option>
        <?php }?>
    </select>
    <?php } ?>
    <label for="login"><?php echo lang('session_login_field_login');?></label>
    <input type="text" class="input-medium" name="login" id="login" value="<?php echo (ENVIRONMENT=='demo')?'bbalet':set_value('login'); ?>" required />
    <input type="hidden" name="CipheredValue" id="CipheredValue" />
</form>
    <input type="hidden" name="salt" id="salt" value="<?php echo $salt; ?>" />
    <label for="password"><?php echo lang('session_login_field_password');?></label>
    <input class="input-medium" type="password" name="password" id="password" value="<?php echo (ENVIRONMENT=='demo')?'bbalet':''; ?>" /><br />
    <br />
    <button id="send" class="btn btn-primary"><i class="icon-user icon-white"></i>&nbsp;<?php echo lang('session_login_button_login');?></button>
    <?php if ($this->config->item('oauth2_enabled') == TRUE) { ?>
         <?php if ($this->config->item('oauth2_provider') == 'google') { ?>
    <button id="cmdGoogleSignIn" class="btn btn-primary"><i class="fa fa-google"></i>&nbsp;<?php echo lang('session_login_button_login');?></button>
        <?php } ?>
    <?php } ?>
    <br /><br />
    <?php if (($this->config->item('ldap_enabled') == FALSE) && (ENVIRONMENT!='demo')) { ?>
    <button id="cmdForgetPassword" class="btn btn-danger"><i class="icon-envelope icon-white"></i>&nbsp;<?php echo lang('session_login_button_forget_password');?></button>
    <?php } ?>
    
    <textarea id="pubkey" style="visibility:hidden;"><?php echo $public_key; ?></textarea>
                </div>
                <div class="span6" style="height:100%;">
                    <div class="row-fluid">
                        <div class="span12">
                            <img src="<?php echo base_url();?>assets/images/logo_simple.png">
                        </div>
                    </div>
                    <div class="row-fluid"><div class="span12">&nbsp;</div></div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span style="font-size: 250%; font-weight: bold; line-height: 100%;"><center><?php echo lang('Leave Management System');?></center></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="span3">&nbsp;</div>
    </div>

<div class="modal hide" id="frmModalAjaxWait" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <h1><?php echo lang('global_msg_wait');?></h1>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif"  align="middle">
    </div>
</div>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/selectize.bootstrap2.css" />
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.pers-brow.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jsencrypt.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/selectize.min.js"></script>
<script type="text/javascript">
    
    //Encrypt the password using RSA and send the ciphered value into the form
    function submit_form() {
        var encrypt = new JSEncrypt();
        encrypt.setPublicKey($('#pubkey').val());
        //Encrypt the concatenation of the password and the salt
        var encrypted = encrypt.encrypt($('#password').val() + $('#salt').val());
        $('#CipheredValue').val(encrypted);
        $('#loginFrom').submit();
    }
    
    //Attempt to authenticate the user using OAuth2 protocol
    function signInCallback(authResult) {
        if (authResult['code']) {
          $.ajax({
            url: '<?php echo base_url();?>session/oauth2',
            type: 'POST',
            data: { 
                      auth_code: authResult.code
                      },
            success: function(result) {
                if (result == "OK") {
                    var target = '<?php echo $last_page;?>';
                    if (target == '') {
                        window.location = "<?php echo base_url();?>";
                    } else {
                        window.location = target;
                    }
                } else {
                    bootbox.alert(result);
                }
            }
          });
        } else {
          // There was an error.
          bootbox.alert("Unknown OAuth2 error");
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
        
        //Refresh page language
        $('#language').selectize({
            onChange: function (value) {
                if (value != '') {
                    $.cookie('language', $('#language option:selected').val(), { expires: 90, path: '/'});
                    $('#loginFrom').prop('action', '<?php echo base_url();?>session/language');
                    $('#loginFrom').submit();
                }
            }
        });
        
        $('#login').focus();
        
        $('#send').click(function() {
            submit_form();
        });
        
        //If the user has forgotten his password, send an e-mail
        $('#cmdForgetPassword').click(function() {
            if ($('#login').val() == "") {
                bootbox.alert("<?php echo lang('session_login_msg_empty_login');?>");
            } else {
                bootbox.confirm("<?php echo lang('session_login_msg_forget_password');?>",
                    "<?php echo lang('Cancel');?>",
                    "<?php echo lang('OK');?>", function(result) {
                    if (result) {
                        $('#frmModalAjaxWait').modal('show');
                        $.ajax({
                           type: "POST",
                           url: "<?php echo base_url(); ?>session/forgetpassword",
                           data: { login: $('#login').val() }
                         })
                         .done(function(msg) {
                            $('#frmModalAjaxWait').modal('hide');
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
            }
        });
        
        //Validate the form if the user press enter key in password field
        $('#password').keypress(function(e){
            if(e.keyCode==13)
            submit_form();
        });
        
        //Alternative authentication methods
<?php if ($this->config->item('oauth2_enabled') == TRUE) { ?>
     <?php if ($this->config->item('oauth2_provider') == 'google') { ?>
        $('#cmdGoogleSignIn').click(function() {
            auth2.grantOfflineAccess({'redirect_uri': 'postmessage'}).then(signInCallback);
        });
    <?php } ?>
<?php } ?>
        
    });
</script>
