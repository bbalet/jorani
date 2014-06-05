<h2><?php echo lang('leaves_view_title');?><?php echo $leave['id']; ?></h2>

    <label for="startdate"><?php echo lang('leaves_view_field_start');?></label>
    <input type="input" name="startdate" value="<?php echo $leave['startdate']; ?>" readonly />
    <select name="startdatetype" readonly>
        <option selected><?php echo $leave['startdatetype']; ?></option>
    </select><br />
    
    <label for="enddate"><?php echo lang('leaves_view_field_end');?></label>
    <input type="input" name="enddate"  value="<?php echo $leave['enddate']; ?>" readonly />
    <select name="enddatetype" readonly>
        <option selected><?php echo $leave['enddatetype']; ?></option>
    </select><br />
    
    <label for="duration"><?php echo lang('leaves_view_field_duration');?></label>
    <input type="input" name="duration"  value="<?php echo $leave['duration']; ?>" readonly />
    
    <label for="type"><?php echo lang('leaves_view_field_type');?></label>
    <select name="type" readonly>
        <option selected><?php echo $leave['type_label']; ?></option>
    </select><br />
    
    <label for="cause"><?php echo lang('leaves_view_field_cause');?></label>
    <textarea name="cause" readonly><?php echo $leave['cause']; ?></textarea>
    
    <label for="status"><?php echo lang('leaves_view_field_status');?></label>
    <select name="status" readonly>
        <option selected><?php echo $leave['status_label']; ?></option>
    </select><br />

    <br /><br />
    <?php if ($leave['status'] == 1) { ?>
    <a href="<?php echo base_url();?>leaves/edit/<?php echo $leave['id'] ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i>&nbsp;<?php echo lang('leaves_view_button_edit');?></a>
    &nbsp;
    <?php } ?>
    <a href="<?php echo base_url();?>leaves" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('leaves_view_button_back_list');?></a>

