<h2>Details of position #<?php echo $position['id']; ?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('positions/edit/' . $position['id']) ?>

    <label for="name">Name</label>
    <input type="input" name="name" id="name" value="<?php echo $position['name']; ?>" required /><br />

    <label for="description">Description</label>
    <textarea type="input" name="description" id="description" /><?php echo $position['description']; ?></textarea>
		
    <br /><br />
    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Update position</button>
    &nbsp;
    <a href="<?php echo base_url();?>positions" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>
</form>