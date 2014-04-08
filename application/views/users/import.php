
We are sorry, this function will be implemented in the next release.

<!--
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
echo form_open('users/import', $attributes); ?>

    <label for="file">Password</label>
    <input type="file" name="file" id="file" required /><br />

</form>
    <br />
    <button id="send" class="btn btn-primary">Import</button>

<script type="text/javascript" src="<?php echo base_url();?>assets/js/jsencrypt.min.js"></script>
<script type="text/javascript">
    $(function () {
        
        //Validate the form if the user press enter key in password field
        $('#password').keypress(function(e){
            if(e.keyCode==13)
            $('#send').click();
        });
    });
</script>
//-->