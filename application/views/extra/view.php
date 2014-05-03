<h2>Details of leave request #<?php echo $leave['id']; ?></h2>

    <label for="startdate" required>Start Date</label>
    <input type="input" name="startdate"  value="<?php echo $leave['startdate']; ?>" readonly />
    
    <select name="startdatetype" readonly>
        <option selected><?php echo $leave['startdatetype']; ?></option>
    </select><br />
    
    <label for="enddate" required>End Date</label>
    <input type="input" name="enddate"  value="<?php echo $leave['enddate']; ?>" readonly />
    
    <select name="enddatetype" readonly>
        <option selected><?php echo $leave['enddatetype']; ?></option>
    </select><br />
    
    <label for="duration" required>Duration</label>
    <input type="input" name="duration"  value="<?php echo $leave['duration']; ?>" readonly />
    
    <label for="type" required>Leave type</label>
    <select name="type" readonly>
        <option selected><?php echo $leave['type_label']; ?></option>
    </select><br />
    
    <label for="cause">Cause</label>
    <textarea name="cause" readonly><?php echo $leave['cause']; ?></textarea>
    
    <label for="status" readonly>Status</label>
    <select name="status">
        <option selected><?php echo $leave['status_label']; ?></option>
    </select><br />

    <br /><br />
    <?php if ($leave['status'] == 1) { ?>
    <a href="<?php echo base_url();?>leaves/edit/<?php echo $leave['id'] ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>
    &nbsp;
    <?php } ?>
    <a href="<?php echo base_url();?>leaves" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;Back to list</a>
