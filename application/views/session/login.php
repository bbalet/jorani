<h2>Create a new user</h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'target');
echo form_open('session/login', $attributes); ?>

    <label for="firstname">Login</label>
    <input type="input" name="login" id="firstname" autofocus required /><br />
    <input type="hidden" name="CipheredValue" id="CipheredValue" />
</form>
    <label for="lastname">Password</label>
    <input type="password" name="password" id="password" required /><br />
    <br />
    <button id="send" class="btn btn-primary">Login</button>

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
    });
</script>

<textarea id="pubkey" style="visibility:hidden;"><?php echo $public_key; ?></textarea>