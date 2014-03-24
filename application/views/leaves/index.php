
<div class="row-fluid">
    <div class="span12">

<?php if($this->session->flashdata('msg')){ ?>
<div class="alert fade in" id="flashbox">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php echo $this->session->flashdata('msg'); ?>
 
</div>
 
<script type="text/javascript">
//Flash message
$(document).ready(function() {
                $(".alert").alert();
});
</script>
<?php } ?>

<h1>My leave requests</h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="leaves" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Cause</th>
            <th>Duration</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($leaves as $leaves_item): ?>
    <tr>
        <td>
            <a href="<?php echo base_url();?>leaves/<?php echo $leaves_item['id']; ?>" title="View request"><?php echo $leaves_item['id']; ?></a>
            &nbsp;
            <a href="<?php echo base_url();?>leaves/<?php echo $leaves_item['id']; ?>" title="view request details"><i class="icon-eye-open"></i></a>
            &nbsp;
            <?php if ($leaves_item['status'] == 1) { ?>
            <a href="<?php echo base_url();?>leaves/edit/<?php echo $leaves_item['id']; ?>" title="edit request details"><i class="icon-pencil"></i></a>
            &nbsp;
            <a href="#" class="confirm-delete" data-id="<?php echo $leaves_item['id'];?>" title="delete request"><i class="icon-trash"></i></a>
            <?php } ?>
        </td>
        <td><?php echo $leaves_item['startdate'] . ' / ' . $leaves_item['startdatetype']; ?></td>
        <td><?php echo $leaves_item['enddate'] . ' / ' . $leaves_item['enddatetype']; ?></td>
        <td><?php echo $leaves_item['cause']; ?></td>
        <td><?php echo $leaves_item['duration']; ?></td>
        <td><?php echo $leaves_item['status']; ?></td>
    </tr>
<?php endforeach ?>
	</tbody>
</table>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span2">
      <a href="<?php echo base_url();?>leaves/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; Export this list</a>
    </div>
    <div class="span2">
      <a href="<?php echo base_url();?>leaves/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp; New request</a>
    </div>
    <div class="span2">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="modal-from-dom" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
         <h3>Delete leave request</h3>
    </div>
    <div class="modal-body">
        <p>You are about to delete one leave request, this procedure is irreversible.</p>
        <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
        <a href="<?php echo base_url();?>leaves/delete/" class="btn danger">Yes</a>
        <a href="javascript:$('#modal-from-dom').modal('hide')" class="btn secondary">No</a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#leaves').dataTable();
	
    //On showing the confirmation pop-up, add the user id at the end of the delete url action
    $('#modal-from-dom').on('show', function() {
        var id = $(this).data('id'),
        removeBtn = $(this).find('.danger');
        removeBtn.attr('href', removeBtn.attr('href') + id);
    })

    //Display a modal pop-up so as to confirm if a user has to be deleted or not
    $('.confirm-delete').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#modal-from-dom').data('id', id).modal('show');
    });
});
</script>
