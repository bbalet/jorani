<h2>Details of user #<?php echo $users_item['id']; ?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('users/update') ?>
    <input type="hidden" name="id" value="<?php echo $users_item['id']; ?>" required /><br />

    <label for="firstname">Firstname</label>
    <input type="input" name="firstname" value="<?php echo $users_item['firstname']; ?>" required /><br />

    <label for="lastname">Lastname</label>
    <input type="input" name="lastname" value="<?php echo $users_item['lastname']; ?>" required /><br />

    <label for="login">Login</label>
    <input type="input" name="login" value="<?php echo $users_item['lastname']; ?>" required /><br />
	
    <label for="email">E-mail</label>
    <input type="email" id="email" name="email" value="<?php echo $users_item['email']; ?>" required /><br />
		
    <label for="role[]">Role</label>
    <select name="role[]" multiple="multiple" size="6">
    <?php foreach ($roles as $roles_item): ?>
        <option value="<?php echo $roles_item['id'] ?>" <?php if ((((int)$roles_item['id']) & ((int) $users_item['role']))) echo "selected" ?>><?php echo $roles_item['name'] ?></option>
    <?php endforeach ?>
    </select>

    <label for="manager">Manager</label>
    <select name="manager" required>
    <?php foreach ($users as $users_item): ?>
        <option value="<?php echo $users_item['id'] ?>"><?php echo $users_item['firstname'] . ' ' . $users_item['lastname']; ?></option>
    <?php endforeach ?>
    </select> If a user has no manager (itself), it can validate its leave requests.
    <br /><br />
    
    <br /><br />
    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Update user</button>
    &nbsp;
    <a href="<?php echo base_url();?>users/" class="btn btn-primary"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>
</form>