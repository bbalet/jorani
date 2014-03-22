<h2>Create a new user</h2>

<?php echo validation_errors(); ?>

<?php echo form_open('users/create') ?>

	<label for="firstname">Firstname</label>
	<input type="input" name="firstname" /><br />

	<label for="lastname">Lastname</label>
	<input type="input" name="lastname" /><br />

	<label for="login">Login</label>
	<input type="input" name="login" /><br />
	
	<label for="password">Password</label>
	<input type="password" name="password" /><br />
	
	<label for="role">Role</label>
	<select name="role">
		<option value="1" selected>user</option>
		<option value="2">administrator</option>
	</select>
	
	<label for="manager">Manager</label>
	<select name="manager">
		<option value="1" selected>a</option>
		<option value="2">b</option>
	</select>
	<br /><br />
	<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Create user</button>
	&nbsp;
	<a href="<?php echo base_url();?>index.php/users/" class="btn btn-primary"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>

</form>