<?php 
/**
 * This partial view is loaded into a modal form and allows the connected user to change its password.
 * @copyright  Copyright (c) 2014-2023 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<?php
$attributes = array('id' => 'target');
echo form_open('users/reset/' . $target_user_id, $attributes); ?>
    <input type="hidden" name="CipheredValue" id="CipheredValue" />
    <label for="password"><?php echo lang('users_reset_field_password');?></label>
    <input type="password" name="password" id="password" required /><br />
    <br />
    <button type="submit" class="btn btn-primary"><?php echo lang('users_reset_button_reset');?></button>
    </form>
