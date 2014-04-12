
<table class="table table-bordered table-hover">
<thead>
    <tr>
      <th>&nbsp;</th>
      <th>Start</th>
      <th>End</th>
      <th>Days</th>
      <th>Leave type</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($entitleddays as $days) { ?>
    <tr>
      <td><a href="<?php echo base_url();?>entitleddays/contractdelete/<?php echo $days['id'] ?>" title="delete"><i class="icon-remove"></i></a></td>
      <td><?php echo $days['startdate']; ?></td>
      <td><?php echo $days['enddate']; ?></td>
      <td><?php echo $days['days']; ?></td>
      <td><?php echo $days['type']; ?></td>
    </tr>
  <?php } ?>
  <?php if (count($entitleddays) == 0) { ?>
    <tr>
        <td colspan="5">No entitled days attached to the contract.</td>
    </tr>
  <?php } ?>
  </tbody>
</table>

<?php echo form_open('entitleddays/contract/' . $id); ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
    <label for="startdate" required>Start Date</label>
    <input type="input" name="startdate" id="startdate" />
    <label for="enddate" required>End Date</label>
    <input type="input" name="enddate" id="enddate" />
    <label for="type" required>Leave type</label>
    <select name="type">
    <?php foreach ($types as $types_item): ?>
        <option value="<?php echo $types_item['id'] ?>" <?php if ($types_item['id'] == 1) echo "selected" ?>><?php echo $types_item['name'] ?></option>
    <?php endforeach ?> 
    </select>    
    <label for="days" required>Days</label>
    <input type="input" name="days" id="days" />
    <button id="send" class="btn btn-primary">Add</button>
</form>

<link href="<?php echo base_url();?>assets/datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo base_url();?>assets/datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>

<!--Avoid datepicker to appear behind the modal form//-->
<style>
    .datepicker{z-index:1151 !important;}
</style>

<script type="text/javascript">
    $(function () {
        $('#startdate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
        $('#enddate').datepicker({format: 'yyyy-mm-dd', autoclose: true});
    });
</script>
