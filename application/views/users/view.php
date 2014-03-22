<h2>Details of user #<?php echo $users_item['id']; ?></h2>

	<label for="firstname">Firstname</label>
	<input type="input" name="firstname" value="<?php echo $users_item['firstname']; ?>" readonly /><br />

	<label for="lastname">Lastname</label>
	<input type="input" name="lastname" value="<?php echo $users_item['lastname']; ?>" readonly /><br />

	<label for="login">Login</label>
	<input type="input" name="login" value="<?php echo $users_item['lastname']; ?>" readonly /><br />
	
	<label for="role">Role</label>
	<select name="role" readonly>
		<option value="1" selected>user</option>
	</select>
	
	<label for="manager">Manager</label>
	<select name="manager" readonly>
		<option value="1" selected>a</option>
	</select>
	<br /><br />
	<a href="<?php echo base_url();?>index.php/users/edit/<?php echo $users_item['id'] ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>
	&nbsp;
	<a href="<?php echo base_url();?>index.php/users/" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;Back to list</a>

</form>