
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
    $("#flashbox").alert();
});
</script>
<?php } ?>
        
<h1>List of leaves for employee #<?php echo $user_id; ?></h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="leaves" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Status</th>
            <th>Start date</th>
            <th>End date</th>            
            <th>Duration</th>
            <th>Type</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($leaves as $leave): ?>
    <tr>
        <td>
            <?php echo $leave['id'] ?>
            <div class="pull-right">
                &nbsp;
                <a href="<?php echo base_url();?>requests/accept/<?php echo $leave['id']; ?>?source=hr%2Fleaves%2F<?php echo $user_id; ?>" title="accept request"><i class="icon-ok"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>requests/reject/<?php echo $leave['id']; ?>?source=hr%2Fleaves%2F<?php echo $user_id; ?>" title="reject request"><i class="icon-remove"></i></a>
                &nbsp;
                <a href="#" class="confirm-delete" data-id="<?php echo $leave['id'];?>" title="delete leave request"><i class="icon-trash"></i></a>
            </div>
        </td>
        <td><?php echo $leave['status']; ?></td>
        <td><?php echo $leave['startdate']; ?></td>
        <td><?php echo $leave['enddate']; ?></td>
        <td><?php echo $leave['duration']; ?></td>
        <td><?php echo $leave['type']; ?></td>
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
      <a href="<?php echo base_url();?>hr/leaves/export/<?php echo $user_id; ?>" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; Export this list</a>
    </div>
    <div class="span3">
      <a href="<?php echo base_url();?>hr/employees" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp; List of employees</a>
    </div>
    <div class="span7">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="frmDeleteLeaveRequest" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
         <h3>Delete leave request</h3>
    </div>
    <div class="modal-body">
        <p>You are about to delete one leave request, this procedure is irreversible.</p>
        <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeleteUser" class="btn danger">Yes</a>
        <a href="#" onclick="$('#frmDeleteLeaveRequest').modal('hide');" class="btn secondary">No</a>
    </div>
</div>

<script type="text/javascript">
$(function () {
    //Transform the HTML table in a fancy datatable
    var oTable = $('#leaves').dataTable();
    oTable.fnSort( [ [0,'desc'] ] );

    //On showing the confirmation pop-up, add the user id at the end of the delete url action
    $('#frmDeleteLeaveRequest').on('show', function() {
        var link = "<?php echo base_url();?>leaves/delete/" + $(this).data('id');
        link += "?source=hr%2Fleaves%2F<?php echo $user_id; ?>";
        $("#lnkDeleteUser").attr('href', link);
    })

    //Display a modal pop-up so as to confirm if a leave request has to be deleted or not
    //We build a complex selector because datatable does horrible things on DOM...
    //a simplier selector doesn't work when the delete is on page >1 
    $("#leaves tbody").on('click', '.confirm-delete',  function(){
        var id = $(this).data('id');
        $('#frmDeleteLeaveRequest').data('id', id).modal('show');
    });
    
    $('#frmDeleteLeaveRequest').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>

