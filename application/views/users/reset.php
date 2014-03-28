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
$attributes = array('id' => 'target');
echo form_open('users/reset/' . $target_user_id, $attributes); ?>
    <input type="hidden" name="CipheredValue" id="CipheredValue" />
</form>
    <label for="password">Password</label>
    <input type="password" name="password" id="password" required /><br />
    <br />
    <button id="send" class="btn btn-primary">Reset</button>

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
