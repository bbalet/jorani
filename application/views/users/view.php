<h2>Details of user #<?php echo $user['id']; ?></h2>

    <label for="firstname">Firstname</label>
    <input type="input" name="firstname" value="<?php echo $user['firstname']; ?>" readonly /><br />

    <label for="lastname">Lastname</label>
    <input type="input" name="lastname" value="<?php echo $user['lastname']; ?>" readonly /><br />

    <label for="login">Login</label>
    <input type="input" name="login" value="<?php echo $user['lastname']; ?>" readonly /><br />
	
    <label for="email">E-mail</label>
    <input type="email" name="email" value="<?php echo $user['email']; ?>" readonly /><br />
	
    <label for="role">Role</label>

    <select name="role" multiple="multiple" size="6" readonly>
    <?php foreach ($roles as $roles_item): ?>
        <option value="<?php echo $roles_item['id'] ?>" <?php if ((((int)$roles_item['id']) & ((int) $user['role']))) echo "selected" ?>><?php echo $roles_item['name'] ?></option>
    <?php endforeach ?>
    </select>

    <label for="manager">Manager</label>
    <select name="manager" readonly>
    <?php foreach ($users as $users_item): ?>
        <option value="<?php echo $users_item['id'] ?>"><?php echo $users_item['firstname'] . ' ' . $users_item['lastname']; ?></option>
    <?php endforeach ?>
    </select>
		
    <br /><br />
    <a href="<?php echo base_url();?>users/edit/<?php echo $user['id'] ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>
    &nbsp;
    <a href="<?php echo base_url();?>users/" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;Back to list</a>