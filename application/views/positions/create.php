<h2>Create a new position</h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'target');
echo form_open('positions/create', $attributes); ?>

    <label for="name">Name</label>
    <input type="input" name="name" id="name" required /><br />

    <label for="description">Description</label>
    <textarea type="input" name="description" id="description" /></textarea>
    <br />
    <button id="send" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Create position</button>
    &nbsp;
    <a href="<?php echo base_url(); ?>positions" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>
</form>
