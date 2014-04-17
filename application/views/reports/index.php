<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('reports', $language);?>

<div class="row-fluid">
    <div class="span12">
        
<h1><?php echo lang('reports_index_title');?></h1>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="reports" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('reports_index_thead_report');?></th>
            <th><?php echo lang('reports_index_thead_description');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($reports as $name => $report): ?>
    <tr>
        <td><a href="<?php echo base_url() . 'reports/' . $report[0] . '/index.php'; ?>"><?php echo $name; ?></a></td>
        <td><?php echo $report[1]; ?></td>
    </tr>
<?php endforeach ?>
	</tbody>
</table>
	</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#reports').dataTable();
});
</script>
