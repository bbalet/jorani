<?php echo form_open('leavetypes/edit/' . $id); ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <label for="name">Name</label>
    <input type="text" name="name" value="<?php echo $type_name; ?>" />
    <br />
    <button id="send" class="btn btn-primary">Update</button>
</form>
