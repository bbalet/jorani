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
        <td><?php echo $position['id'] ?></td>
        <td><?php echo $position['name'] ?></td>
        <td><?php echo $position['description'] ?></td>
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
    $('#positions').dataTable({
        "pageLength": 5
    });
    //Hide pagination select box in order to save space
    $('.dataTables_length').css("display", "none");
    //Display selected row
    $("#positions tbody tr").on('click',function(event) {
            $("#positions tbody tr").removeClass('row_selected');		
            $(this).addClass('row_selected');
    });
    $("#positions tbody tr:first").addClass('row_selected');
});
</script>
