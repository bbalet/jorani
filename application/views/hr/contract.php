<?php echo form_open('hr/contract/' . $id); ?>

    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
    
    <label for="contract">Contract</label>
    <select name="contract">
    <?php foreach ($contracts as $contract): ?>
        <option value="<?php echo $contract['id'] ?>"><?php echo $contract['name']; ?></option>
    <?php endforeach ?>
    </select>
    <br />
    <button id="send" class="btn btn-primary">Set contract</button>
</form>
