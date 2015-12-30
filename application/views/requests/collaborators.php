<?php 
/**
 * This view displays the list of collaborators of the connected employee.
 * e.g. users having the connected user as their line manager.
 * @copyright  Copyright (c) 2014-2015 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.0
 */
?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('requests_collaborators_title');?>  &nbsp;<?php echo $help;?></h2>

<?php echo $flash_partial_view;?>

<p><?php echo lang('requests_collaborators_description');?></p>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="collaborators" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('requests_collaborators_thead_id');?></th>
            <th><?php echo lang('requests_collaborators_thead_firstname');?></th>
            <th><?php echo lang('requests_collaborators_thead_lastname');?></th>
            <th><?php echo lang('requests_collaborators_thead_email');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($collaborators as $collaborator): ?>
    <tr>
        <td data-order="<?php echo $collaborator['id']; ?>">
            <?php echo $collaborator['id']; ?>
            <div class="pull-right">
                <?php if ($this->config->item('requests_by_manager') == TRUE) { ?>
                <a href="<?php echo base_url();?>requests/createleave/<?php echo $collaborator['id'] ?>" title="<?php echo lang('requests_collaborators_thead_link_create_leave');?>"><i class="icon-plus"></i></a>
                <?php } ?>
                <a href="<?php echo base_url();?>hr/counters/collaborators/<?php echo $collaborator['id'] ?>" title="<?php echo lang('requests_collaborators_thead_link_balance');?>"><i class="icon-info-sign"></i></a>
                &nbsp;<a href="<?php echo base_url();?>hr/presence/collaborators/<?php echo $collaborator['id'] ?>" title="<?php echo lang('requests_collaborators_thead_link_presence');?>"><i class="fa fa-pie-chart" style="color:black;"></i></a>
                &nbsp;<a href="<?php echo base_url();?>calendar/year/<?php echo $collaborator['id'] ?>" title="<?php echo lang('requests_collaborators_thead_link_year');?>"><i class="icon-calendar"></i></a>
            </div>
        </td>
        <td><?php echo $collaborator['firstname'] ?></td>
        <td><?php echo $collaborator['lastname'] ?></td>
        <td><a href="mailto:<?php echo $collaborator['email']; ?>"><?php echo $collaborator['email']; ?></a></td>
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
    $('#collaborators').dataTable({
                "order": [[ 3, "asc" ], [ 2, "asc" ]],
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
