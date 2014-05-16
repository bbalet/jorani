
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
        
<h1>List of positions</h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="positions" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($positions as $position): ?>
    <tr>
        <td>
            <a href="#" class="confirm-delete" data-id="<?php echo $position['id'];?>" title="delete"><i class="icon-trash"></i></a>&nbsp; 
            <a href="<?php echo base_url();?>positions/edit/<?php echo $position['id']; ?>" title="edit"><i class="icon-pencil"></i></a>&nbsp; 
            <?php echo $position['id'];?>
        </td>
        <td><?php echo $position['name']; ?></td>
        <td><?php echo $position['description']; ?></td>
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
      <a href="<?php echo base_url();?>positions/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; Export this list</a>
    </div>
    <div class="span3">
      <a href="<?php echo base_url();?>positions/create" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp; Create a new position</a>
    </div>
    <div class="span7">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<div id="frmDeletePosition" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmDeletePosition').modal('hide')" class="close">&times;</a>
         <h3>Delete Position</h3>
    </div>
    <div class="modal-body">
        <p>You are about to delete one position, this procedure is irreversible.</p>
        <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeletePosition" class="btn danger">Yes</a>
        <a href="javascript:$('#frmDeletePosition').modal('hide')" class="btn secondary">No</a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#positions').dataTable();
    $("#frmDeletePosition").alert();
    
    //On showing the confirmation pop-up, add the contract id at the end of the delete url action
    $('#frmDeletePosition').on('show', function() {
        var link = "<?php echo base_url();?>positions/delete/" + $(this).data('id');
        $("#lnkDeletePosition").attr('href', link);
    });

    //Display a modal pop-up so as to confirm if a contract has to be deleted or not
    $('.confirm-delete').on('click', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('#frmDeletePosition').data('id', id).modal('show');
    });
});
</script>
