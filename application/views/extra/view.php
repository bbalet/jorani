<h2>Details of overtime request #<?php echo $leave['id']; ?></h2>

    <label for="date" required>Date</label>
    <input type="input" name="date"  value="<?php echo $leave['date']; ?>" readonly />
    
    <label for="duration" required>Duration</label>
    <input type="input" name="duration"  value="<?php echo $leave['duration']; ?>" readonly />
    
    <label for="cause">Cause</label>
    <textarea name="cause" readonly><?php echo $leave['cause']; ?></textarea>
    
    <label for="status">Status</label>
    <select name="status" readonly>
        <option selected><?php echo $leave['status_label']; ?></option>
    </select><br />

    <br /><br />
    <?php if ($leave['status'] == 1) { ?>
    <a href="<?php echo base_url();?>extra/edit/<?php echo $leave['id'] ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>
    &nbsp;
    <?php } ?>
    <a href="<?php echo base_url();?>extra" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;Back to list</a>
