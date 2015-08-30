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
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */
?>

<?php
$attributes = array('id' => 'target');
echo form_open('users/reset/' . $target_user_id, $attributes); ?>
    <input type="hidden" name="CipheredValue" id="CipheredValue" />
</form>
    <label for="password"><?php echo lang('users_reset_field_password');?></label>
    <input type="password" name="password" id="password" required /><br />
    <br />
    <button id="send" class="btn btn-primary"><?php echo lang('users_reset_button_reset');?></button>

<script type="text/javascript" src="<?php echo base_url();?>assets/js/jsencrypt.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('#send').click(function() {
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
