<h2>Create a new user</h2>

<?php echo validation_errors(); ?>

<?php echo form_open('users/create') ?>

    <label for="firstname">Firstname</label>
    <input type="input" name="firstname" id="firstname" required /><br />

    <label for="lastname">Lastname</label>
    <input type="input" name="lastname" id="lastname" required /><br />

    <label for="login">Login</label>
    <input type="input" name="login" id="login" required /><br />

    <label for="email">E-mail</label>
    <input type="email" id="email" name="email" required /><br />
    
    <label for="password">Password</label>
    <input type="input" name="password" id="password" required />&nbsp;
    <a class="btn" id="cmdGeneratePassword">
        <i class="icon-refresh"></i>&nbsp;Generate password
    </a>
    <br />

    <label for="role">Role</label>
    <select name="role">
    <?php foreach ($roles as $roles_item): ?>
        <option value="<?php echo $roles_item['id'] ?>" <?php if ($roles_item['id'] == 2) echo "selected" ?>><?php echo $roles_item['name'] ?></option>
    <?php endforeach ?>
    </select>
    
    <label for="manager">Manager</label>
    <select name="manager">
    <?php foreach ($users as $users_item): ?>
        <option value="<?php echo $users_item['id'] ?>" <?php if ($users_item['id'] == 2) echo "selected" ?>><?php echo $users_item['name'] ?></option>
    <?php endforeach ?>
    </select> If a user has no manager, his leave requests are automatically validated.
    <br /><br />
    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Create user</button>
    &nbsp;
    <a href="<?php echo base_url(); ?>index.php/users/" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>
</form>

<script type="text/javascript" src="<?php echo base_url();?>assets/js/jqBootstrapValidation.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/lms.password.js"></script>
<script type="text/javascript">
    $(function () {
        $("input").not("[type=submit]").jqBootstrapValidation(); 
        
        $("#cmdGeneratePassword").click(function() {
            $("#password").val(password_generator(<?php echo $this->config->item('password_length');?>));
        });
        
        //On any change on firstname or lastname fields, automatically build the
        //login identifier with first character of firstname and lastname
        $("#firstname").change(function() {
            $("#login").val($("#firstname").val().charAt(0) + $("#lastname").val());            
        });
        $("#lastname").change(function() {
            $("#login").val($("#firstname").val().charAt(0) + $("#lastname").val());            
        });
    });

</script>
