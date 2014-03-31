
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

<h1>Requests submitted to me</h1>

<p>This screen lists the leave requests submitted to you. If you are not a manager, this list will always be empty.</p>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="leaves" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Full name</th>
            <th>Start Date</th>
            <th>End Date</th>            
            <th>Duration</th>
            <th>Type</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($requests as $requests_item): ?>
    <tr>
        <td>
            <a href="<?php echo base_url();?>leaves/<?php echo $requests_item['id']; ?>" title="View request"><?php echo $requests_item['id']; ?></a>
            &nbsp;
            <a href="<?php echo base_url();?>leaves/<?php echo $requests_item['id']; ?>" title="view request details"><i class="icon-eye-open"></i></a>
            &nbsp;
            <a href="<?php echo base_url();?>requests/accept/<?php echo $requests_item['id']; ?>" title="accept request"><i class="icon-ok"></i></a>
            &nbsp;
            <a href="<?php echo base_url();?>requests/reject/<?php echo $requests_item['id']; ?>" title="reject request"><i class="icon-remove"></i></a>
        </td>
        <td><?php echo $requests_item['firstname'] . ' ' . $requests_item['lastname']; ?></td>
        <td><?php echo $requests_item['startdate'] . ' / ' . $requests_item['startdatetype']; ?></td>
        <td><?php echo $requests_item['enddate'] . ' / ' . $requests_item['enddatetype']; ?></td>
        <td><?php echo $requests_item['duration']; ?></td>
        <td><?php echo $requests_item['type_label']; ?></td>
        
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
      <a href="<?php echo base_url();?>requests/export/<?php echo $filter; ?>" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; Export this list</a>
    </div>
     <div class="span2">
      <a href="<?php echo base_url();?>requests/all" class="btn btn-primary"><i class="icon-filter icon-white"></i>&nbsp; All requests</a>
    </div>
    <div class="span2">
      <a href="<?php echo base_url();?>requests/requested" class="btn btn-primary"><i class="icon-filter icon-white"></i>&nbsp; Pending requests</a>
    </div>
    <div class="span8">&nbsp;</div>
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
	

});
</script>
