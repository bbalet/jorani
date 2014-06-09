<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('users', $language);
CI_Controller::get_instance()->load->library('language');
?>

<h2><?php echo lang('users_view_title');?><?php echo $user['id']; ?></h2>

    <label for="firstname"><?php echo lang('users_view_field_firstname');?></label>
    <input type="input" name="firstname" value="<?php echo $user['firstname']; ?>" readonly /><br />

    <label for="lastname"><?php echo lang('users_view_field_lastname');?></label>
    <input type="input" name="lastname" value="<?php echo $user['lastname']; ?>" readonly /><br />

    <label for="login"><?php echo lang('users_view_field_login');?></label>
    <input type="input" name="login" value="<?php echo $user['login']; ?>" readonly /><br />
	
    <label for="email"><?php echo lang('users_view_field_email');?></label>
    <input type="email" name="email" value="<?php echo $user['email']; ?>" readonly /><br />
	
    <label for="role"><?php echo lang('users_view_field_role');?></label>
    <select name="role" multiple="multiple" size="2" readonly>
    <?php foreach ($roles as $roles_item): ?>
        <option value="<?php echo $roles_item['id']; ?>" <?php if ((((int)$roles_item['id']) & ((int) $user['role']))) echo "selected"; ?>><?php echo $roles_item['name']; ?></option>
    <?php endforeach ?>
    </select>

    <label for="manager"><?php echo lang('users_view_field_manager');?></label>
    <input type="text" name="manager" value="<?php echo $manager_label; ?>" readonly /><br />
    
    <label for="contract"><?php echo lang('users_view_field_contract');?></label>
    <input type="text" name="contract" value="<?php echo $contract_label; ?>" readonly /><br />
    
    <label for="position"><?php echo lang('users_view_field_position');?></label>
    <input type="text" name="position" value="<?php echo $position_label; ?>" readonly /><br />
    
    <label for="entity"><?php echo lang('users_view_field_entity');?></label>
    <input type="text" name="entity" value="<?php echo $organization_label; ?>" readonly /><br />
    
    <label for="datehired"><?php echo lang('users_view_field_hired');?></label>
    <input type="text" name="datehired" value="<?php echo $user['datehired']; ?>" readonly /><br />
    
    <label for="identifier"><?php echo lang('users_view_field_identifier');?></label>
    <input type="text" name="identifier" value="<?php echo $user['identifier']; ?>" readonly /><br />
    
    <label for="language"><?php echo lang('users_create_field_language');?></label>
    <select name="language" readonly>
        <option><?php echo $this->language->code2nativelanguage($user['language']); ?></option>
    </select>
    
    <br /><br />
    <a href="<?php echo base_url();?>users/edit/<?php echo $user['id'] ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i>&nbsp;<?php echo lang('users_view_button_edit');?></a>
    &nbsp;
    <a href="<?php echo base_url();?>users" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('users_view_button_back');?></a>
