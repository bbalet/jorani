
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
        
<h1>List of overtime requests for employee #<?php echo $user_id; ?></h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="extras" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Status</th>
            <th>Date</th>
            <th>Duration</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($extras as $extra): ?>
    <tr>
        <td>
            <a href="<?php echo base_url();?>extra/edit/<?php echo $extra['id']; ?>?source=hr%2Fovertime%2F<?php echo $user_id; ?>" title="edit request"><?php echo $extra['id'] ?></a>
            <div class="pull-right">
                &nbsp;
                <a href="<?php echo base_url();?>overtime/accept/<?php echo $extra['id']; ?>?source=hr%2Fovertime%2F<?php echo $user_id; ?>" title="accept request"><i class="icon-ok"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>overtime/reject/<?php echo $extra['id']; ?>?source=hr%2Fovertime%2F<?php echo $user_id; ?>" title="reject request"><i class="icon-remove"></i></a>
                &nbsp;
                <a href="#" class="confirm-delete" data-id="<?php echo $extra['id'];?>" title="delete overtime request"><i class="icon-trash"></i></a>
            </div>
        </td>
        <td><?php echo $extra['status']; ?></td>
        <td><?php echo $extra['date']; ?></td>
        <td><?php echo $extra['duration']; ?></td>
        <td><?php echo $extra['cause']; ?></td>
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
      <a href="<?php echo base_url();?>hr/overtime/export/<?php echo $user_id; ?>" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; Export this list</a>
    </div>
    <div class="span3">
      <a href="<?php echo base_url();?>hr/employees" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp; List of employees</a>
    </div>
    <div class="span7">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="frmDeleteExtraRequest" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
         <h3>Delete overtime request</h3>
    </div>
    <div class="modal-body">
        <p>You are about to delete one overtime request, this procedure is irreversible.</p>
        <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeleteExtra" class="btn danger">Yes</a>
        <a href="#" onclick="$('#frmDeleteExtraRequest').modal('hide');" class="btn secondary">No</a>
    </div>
</div>

<script type="text/javascript">
$(function () {
    //Transform the HTML table in a fancy datatable
    var oTable = $('#extras').dataTable();
    oTable.fnSort( [ [0,'desc'] ] );

    //On showing the confirmation pop-up, add the user id at the end of the delete url action
    $('#frmDeleteExtraRequest').on('show', function() {
        var link = "<?php echo base_url();?>extra/delete/" + $(this).data('id');
        link += "?source=hr%2Fovertime%2F<?php echo $user_id; ?>";
        $("#lnkDeleteExtra").attr('href', link);
    })

    //Display a modal pop-up so as to confirm if a leave request has to be deleted or not
    //We build a complex selector because datatable does horrible things on DOM...
    //a simplier selector doesn't work when the delete is on page >1 
    $("#extras tbody").on('click', '.confirm-delete',  function(){
        var id = $(this).data('id');
        $('#frmDeleteExtraRequest').data('id', id).modal('show');
    });
    
    $('#frmDeleteExtraRequest').on('hidden', function() {
        $(this).removeData('modal');
    });
});
</script>

