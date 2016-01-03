<?php 
/**
 * This view displays the list of leave requests submitted to a manager.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('requests_index_title');?><?php echo $help;?></h2>

<?php echo $flash_partial_view;?>

<p><?php echo lang('requests_index_description');?></p>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="leaves" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('requests_index_thead_id');?></th>
            <th><?php echo lang('requests_index_thead_fullname');?></th>
            <th><?php echo lang('requests_index_thead_startdate');?></th>
            <th><?php echo lang('requests_index_thead_enddate');?></th>            
            <th><?php echo lang('requests_index_thead_duration');?></th>
            <th><?php echo lang('requests_index_thead_type');?></th>
            <th><?php echo lang('requests_index_thead_status');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($requests as $requests_item):
    $date = new DateTime($requests_item['startdate']);
    $tmpStartDate = $date->getTimestamp();
    $startdate = $date->format(lang('global_date_format'));
    $date = new DateTime($requests_item['enddate']);
    $tmpEndDate = $date->getTimestamp();
    $enddate = $date->format(lang('global_date_format'));?>
    <tr>
        <td data-order="<?php echo $requests_item['id']; ?>">
            <a href="<?php echo base_url();?>leaves/requests/<?php echo $requests_item['id']; ?>" title="<?php echo lang('requests_index_thead_tip_view');?>"><?php echo $requests_item['id']; ?></a>
            &nbsp;
            <div class="pull-right">
                <a href="<?php echo base_url();?>leaves/requests/<?php echo $requests_item['id']; ?>" title="<?php echo lang('requests_index_thead_tip_view');?>"><i class="icon-eye-open"></i></a>
                &nbsp;
                <a href="#" class="lnkAccept" data-id="<?php echo $requests_item['id']; ?>" title="<?php echo lang('requests_index_thead_tip_accept');?>"><i class="icon-ok"></i></a>
                &nbsp;
                <a href="#" class="lnkReject" data-id="<?php echo $requests_item['id']; ?>" title="<?php echo lang('requests_index_thead_tip_reject');?>"><i class="icon-remove"></i></a>
            </div>
        </td>
        <td><?php echo $requests_item['firstname'] . ' ' . $requests_item['lastname']; ?></td>
        <td data-order="<?php echo $tmpStartDate; ?>"><?php echo $startdate . ' (' . lang($requests_item['startdatetype']). ')'; ?></td>
        <td data-order="<?php echo$tmpEndDate; ?>"><?php echo $enddate . ' (' . lang($requests_item['enddatetype']) . ')'; ?></td>
        <td><?php echo $requests_item['duration']; ?></td>
        <td><?php echo $requests_item['type_name']; ?></td>
        <td><?php echo lang($requests_item['status_name']); ?></td>
    </tr>
<?php endforeach ?>
	</tbody>
</table>
	</div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div class="row-fluid">
    <div class="span12">
        <a href="<?php echo base_url();?>requests/export/<?php echo $filter; ?>" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp; <?php echo lang('requests_index_button_export');?></a>
        &nbsp;&nbsp;
        <a href="<?php echo base_url();?>requests/all" class="btn btn-primary"><i class="icon-filter icon-white"></i>&nbsp; <?php echo lang('requests_index_button_show_all');?></a>
        &nbsp;&nbsp;
        <a href="<?php echo base_url();?>requests/requested" class="btn btn-primary"><i class="icon-filter icon-white"></i>&nbsp; <?php echo lang('requests_index_button_show_pending');?></a>
    </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
var clicked = false;
    
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#leaves').dataTable({
                "order": [[ 2, "desc" ]],
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

     //Prevent double click on accept and reject buttons
     $(".lnkAccept").on('click', function (event) {
        event.preventDefault();
        if (!clicked) {
            clicked = true;
            window.location.href = "<?php echo base_url();?>requests/accept/" + $(this).data("id");
        }
     });
     $(".lnkReject").on('click', function (event) {
        event.preventDefault();
        if (!clicked) {
            clicked = true;
            window.location.href = "<?php echo base_url();?>requests/reject/" + $(this).data("id");
        }
     });
});
</script>
