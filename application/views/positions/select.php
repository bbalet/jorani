<table cellpadding="0" cellspacing="0" border="0" class="display" id="employees" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>E-mail</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($employees as $employee): ?>
    <tr>
        <td><?php echo $employee['id'] ?></td>
        <td><?php echo $employee['firstname'] ?></td>
        <td><?php echo $employee['lastname'] ?></td>
        <td><?php echo $employee['email'] ?></td>
    </tr>
<?php endforeach ?>
	</tbody>
</table>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<style>
    tr.row_selected td{background-color:#b0bed9 !important;}
</style>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#employees').dataTable({
        "pageLength": 5
    });
    //Hide pagination select box in order to save space
    $('.dataTables_length').css("display", "none");
    //Display selected row
    $("#employees tbody tr").on('click',function(event) {
            $("#employees tbody tr").removeClass('row_selected');		
            $(this).addClass('row_selected');
    });
    $("#employees tbody tr:first").addClass('row_selected');
});
</script>
