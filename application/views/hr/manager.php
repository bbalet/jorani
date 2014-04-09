<?php echo form_open('hr/manager/' . $id); ?>

    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
    
    <label for="manager">Manager</label>
    <select name="manager">
    <?php foreach ($users as $users_item): ?>
        <option value="<?php echo $users_item['id'] ?>" <?php if ($users_item['manager'] == $users_item['id']) echo "selected"; ?>><?php echo $users_item['firstname'] . ' ' . $users_item['lastname']; ?></option>
    <?php endforeach ?>
    </select><br />
    If a user has no manager (itself), its leave requests are automatically validated.
    <br />
    <br />
    <button id="send" class="btn btn-primary">Set manager</button>
</form>
