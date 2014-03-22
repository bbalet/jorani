<h2>Details of user #<?php echo $users_item['id']; ?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('users/update') ?>
	<input type="hidden" name="id" value="<?php echo $users_item['id']; ?>" /><br />

	<label for="firstname">Firstname</label>
	<input type="input" name="firstname" value="<?php echo $users_item['firstname']; ?>" /><br />

	<label for="lastname">Lastname</label>
	<input type="input" name="lastname" value="<?php echo $users_item['lastname']; ?>" /><br />

	<label for="login">Login</label>
	<input type="input" name="login" value="<?php echo $users_item['lastname']; ?>" /><br />
		
        <label for="role">Role</label>
        <select name="role">
        <?php foreach ($roles as $roles_item): ?>
            <option value="<?php echo $roles_item['id'] ?>" <?php if ($roles_item['id'] == 2) echo "selected" ?>><?php echo $roles_item['name'] ?></option>
        <?php endforeach ?>
        </select>

        <label for="manager">Manager</label>
        <select name="manager">
        <?php foreach ($users as $users_item): ?>
            <option value="<?php echo $users_item['id'] ?>"><?php echo $users_item['firstname'] . ' ' . $users_item['lastname']; ?></option>
        <?php endforeach ?>
        </select> If a user has no manager (itself), its leave requests are automatically validated.
        <br /><br />
    
	<br /><br />
	<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Update user</button>
	&nbsp;
	<a href="<?php echo base_url();?>index.php/users/" class="btn btn-primary"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>
</form>