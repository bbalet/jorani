<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('session', $language);?>

<h2><?php echo lang('session_login_title');?> &nbsp;
<a href="http://www.leave-management-system.org/page-login-to-the-application.html" title="Link to documentation" target="_blank"><i class="icon-question-sign"></i></a></h2>

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
echo form_open('session/login', $attributes); ?>

    <input type="hidden" name="last_page" value="session/login" />
    <label for="login"><?php echo lang('session_login_field_language');?></label>
    <select name="language" id="language">
        <option value="en" <?php if ($language_code == 'en') echo 'selected'; ?>>English</option>
        <option value="fr" <?php if ($language_code == 'fr') echo 'selected'; ?>>Français</option>
    </select>
    <label for="login"><?php echo lang('session_login_field_login');?></label>
    <input type="input" name="login" id="login" value="<?php echo set_value('login'); ?>" autofocus required /><br />
    <input type="hidden" name="CipheredValue" id="CipheredValue" />
</form>
    <input type="hidden" name="salt" id="salt" value="<?php echo $salt; ?>" />
    <label for="password"><?php echo lang('session_login_field_password');?></label>
    <input type="password" name="password" id="password" required /><br />
    <br />
    <button id="send" class="btn btn-primary"><?php echo lang('session_login_button_login');?></button>

<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jsencrypt.min.js"></script>
<script type="text/javascript">
    $(function () {
        //Memorize the last selected language with a cookie
        if($.cookie('language') != null) {
            $('#language option[value="' + $.cookie('language') + '"]').attr('selected', 'selected');
            if ($.cookie('language') != "<?php echo $language_code; ?>") {
                $('#loginFrom').prop('action', '<?php echo base_url();?>session/language');
                $('#loginFrom').submit();
            }
        }
        $('#language').change(function() {
            $.cookie('language', $('#language option:selected').val(), { expires: 90, path: '/'});
        });
        
        $('#send').click(function() {
            var encrypt = new JSEncrypt();
            encrypt.setPublicKey($('#pubkey').val());
            //Encrypt the concatenation of the password and the salt
            var encrypted = encrypt.encrypt($('#password').val() + $('#salt').val());
            $('#CipheredValue').val(encrypted);
            $('#loginFrom').submit();
        });
        
        //Validate the form if the user press enter key in password field
        $('#password').keypress(function(e){
            if(e.keyCode==13)
            $('#send').click();
        });
        
        //Refresh page language
        $('#language').change(function(){
            $('#loginFrom').prop('action', '<?php echo base_url();?>session/language');
            $('#loginFrom').submit();
        });
    });
</script>

<textarea id="pubkey" style="visibility:hidden;"><?php echo $public_key; ?></textarea>