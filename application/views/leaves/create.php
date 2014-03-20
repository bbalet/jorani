<h2>Create a new user</h2>

<?php echo validation_errors(); ?>

<?php echo form_open('users/create') ?>

	<label for="firstname">Firstname</label>
	<input type="input" name="firstname" /><br />

	<label for="lastname">Lastname</label>
	<input type="input" name="lastname" /><br />

	<input type="submit" name="submit" value="Create user" />

</form>