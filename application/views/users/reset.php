<?php 
/**
 * This partial view is loaded into a modal form and allows the connected user to change its password.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<?php
$attributes = array('id' => 'target');
echo form_open('users/reset/' . $target_user_id, $attributes); ?>
    <input type="hidden" name="CipheredValue" id="CipheredValue" />
    <h2><?php echo lang('users_reset_change_password'); ?></h2><BR />
	<?php if (isset($mdp)) { echo $mdp; } ?>
	<?php echo validation_errors(); ?>
	
	<label for="lastpassword"><?php echo lang('users_reset_last_password'); ?> :</label>
	<input type="password" name="lastpassword" id="lastpassword" required />
	<br />
    <label for="password"><?php echo lang('users_reset_new_password'); ?> :</label>
    <input type="password" name="password" id="password" required />
	<BR >
	<label for="passwordbis"><?php echo lang('users_reset_rewamp_password'); ?> :</label>
	<input type="password" name="passwordbis" id="passwordbis" required />
	<br />
    <br />
</form>
    <button id="send" class="btn btn-primary"><?php echo lang('users_reset_button_reset');?></button>

<script type="text/javascript" src="<?php echo base_url();?>assets/js/jsencrypt.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('#send').click(function() {
			if (passwordbis.value != password.value) {
				alert('Ce ne sont pas les m\352mes mots de passe!');
				passwordbis.value = "";
				password.value = "";
				password.focus();
				return false;
			}
			
            var encrypt = new JSEncrypt();
            encrypt.setPublicKey($('#pubkey').val());
            var encrypted = encrypt.encrypt($('#password').val());
            $('#CipheredValue').val(encrypted);
            $('#target').submit();
        });
        
        //Validate the form if the user press enter key in password field
        $('#password').keypress(function(e){
            if(e.keyCode==13)
            $('#send').click();
        });
    });
</script>

<textarea id="pubkey" style="visibility:hidden;"><?php echo $public_key; ?></textarea>
