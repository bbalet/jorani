<?php 
/**
 * This view displays a simplified login form for OAtuh2 authorization.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.6.0
 */
?>
<link href="<?php echo base_url();?>assets/css/jorani-0.5.1.css" rel="stylesheet">
<style>
<?php //Font mapping with languages needing a better font than the default font
$fonts = $this->config->item('fonts');
if (!is_null($fonts)) {
    if (array_key_exists($language_code, $fonts)) { ?>
    @font-face {
      font-family: '<?php echo $fonts[$language_code]['name'];?>';
      src: url('<?php echo base_url(), 'assets/fonts/', $fonts[$language_code]['asset'];?>') format('truetype');
    }
    body, button, input, select, .ui-datepicker, .selectize-input {
        font-family: '<?php echo $fonts[$language_code]['name'];?>' !important;
    }
<?php 
        }
    } ?>
</style>

<div class="container">
    <div class="row">
        <div class="span12">
            <img width="100" src="<?php echo base_url();?>assets/images/logo_simple.png">
        </div>
    </div>
    <div class="row">
        <div class="span12">
            
    <label for="login"><?php echo lang('session_login_field_login');?></label>
    <input type="text" class="input-medium" name="login" id="login" value="<?php echo set_value('login'); ?>" required />
    <input type="hidden" name="salt" id="salt" value="<?php echo $salt; ?>" />
    <label for="password"><?php echo lang('session_login_field_password');?></label>
    <input class="input-medium" type="password" name="password" id="password" /><br />
    <br />
    <button id="send" class="btn btn-primary"><i class="icon-user icon-white"></i>&nbsp;<?php echo lang('session_login_button_login');?></button>
    <br />
    <textarea id="pubkey" style="visibility:hidden;"><?php echo $public_key; ?></textarea>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/selectize.bootstrap2.css" />
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.pers-brow.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jsencrypt.min.js"></script>
<script type="text/javascript">
    
    //Encrypt the password using RSA and send the ciphered value into the form
    function submit_form() {
        var encrypt = new JSEncrypt();
        encrypt.setPublicKey($('#pubkey').val());
        //Encrypt the concatenation of the password and the salt
        var encrypted = encrypt.encrypt($('#password').val() + $('#salt').val());
        $.ajax({
            url: "<?php echo base_url();?>api/authorization/login",
            type: "POST",
            data: {
                login: $('#login').val(),
                CipheredValue: encrypted,
            },
            success: function(data){
                if (typeof JoraniOAuthLoginCallback == 'function') {
                    JoraniOAuthLoginCallback(data);
                } else {
                    alert("No OAuth2 callback function was defined");
                }
            }
        });
    }

    $(function () {
    
        $('#login').focus();
        
        $('#send').click(function() {
            submit_form();
        });
        
        //Validate the form if the user press enter key in password field
        $('#password').keypress(function(e){
            if(e.keyCode==13)
            submit_form();
        });
    });
</script>
