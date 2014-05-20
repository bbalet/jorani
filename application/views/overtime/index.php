<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('overtime', $language);
$this->lang->load('datatable', $language);
?>

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

<h1><?php echo lang('overtime_index_title');?></h1>

<p><?php echo lang('overtime_index_description');?></p>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="overtime" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('overtime_index_thead_id');?></th>
            <th><?php echo lang('overtime_index_thead_fullname');?></th>
            <th><?php echo lang('overtime_index_thead_date');?></th>
            <th><?php echo lang('overtime_index_thead_duration');?></th>
            <th><?php echo lang('overtime_index_thead_status');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($requests as $requests_item): ?>
    <tr>
        <td>
            <a href="<?php echo base_url();?>extra/<?php echo $requests_item['id']; ?>" title="View request"><?php echo $requests_item['id']; ?></a>
            &nbsp;
            <a href="<?php echo base_url();?>extra/<?php echo $requests_item['id']; ?>" title="view request details"><i class="icon-eye-open"></i></a>
            &nbsp;
            <a href="<?php echo base_url();?>overtime/accept/<?php echo $requests_item['id']; ?>" title="accept request"><i class="icon-ok"></i></a>
            &nbsp;
            <a href="<?php echo base_url();?>overtime/reject/<?php echo $requests_item['id']; ?>" title="reject request"><i class="icon-remove"></i></a>
        </td>
        <td><?php echo $requests_item['firstname'] . ' ' . $requests_item['lastname']; ?></td>
        <td><?php echo $requests_item['date']; ?></td>
        <td><?php echo $requests_item['duration']; ?></td>
        <td><?php echo $requests_item['status_label']; ?></td>
        
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
      <a href="<?php echo base_url();?>overtime/export/<?php echo $filter; ?>" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; <?php echo lang('overtime_index_button_export');?></a>
    </div>
     <div class="span2">
      <a href="<?php echo base_url();?>overtime/all" class="btn btn-primary"><i class="icon-filter icon-white"></i>&nbsp; <?php echo lang('overtime_index_button_show_all');?></a>
    </div>
    <div class="span2">
      <a href="<?php echo base_url();?>overtime/requested" class="btn btn-primary"><i class="icon-filter icon-white"></i>&nbsp; <?php echo lang('overtime_index_button_show_pending');?></a>
    </div>
    <div class="span8">&nbsp;</div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#overtime').dataTable({
		"oLanguage": {
                    "sEmptyTable":     "<?php echo lang('datatable_sEmptyTable');?>",
                    "sInfo":           "<?php echo lang('datatable_sInfo');?>",
                    "sInfoEmpty":      "<?php echo lang('datatable_sInfoEmpty');?>",
                    "sInfoFiltered":   "<?php echo lang('datatable_sInfoFiltered');?>",
                    "sInfoPostFix":    "<?php echo lang('datatable_sInfoPostFix');?>",
                    "sInfoThousands":  "<?php echo lang('datatable_sInfoThousands');?>",
                    "sLengthMenu":     "<?php echo lang('datatable_sLengthMenu');?>",
                    "sLoadingRecords": "<?php echo lang('datatable_sLoadingRecords');?>",
                    "sProcessing":     "<?php echo lang('datatable_sProcessing');?>",
                    "sSearch":         "<?php echo lang('datatable_sSearch');?>",
                    "sZeroRecords":    "<?php echo lang('datatable_sZeroRecords');?>",
                    "oPaginate": {
                        "sFirst":    "<?php echo lang('datatable_sFirst');?>",
                        "sLast":     "<?php echo lang('datatable_sLast');?>",
                        "sNext":     "<?php echo lang('datatable_sNext');?>",
                        "sPrevious": "<?php echo lang('datatable_sPrevious');?>"
                    },
                    "oAria": {
                        "sSortAscending":  "<?php echo lang('datatable_sSortAscending');?>",
                        "sSortDescending": "<?php echo lang('datatable_sSortDescending');?>"
                    }
                }
            });
});
</script>
