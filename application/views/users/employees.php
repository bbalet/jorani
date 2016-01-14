<?php 
/**
 * This partial view is loaded into a modal form and allows to pick an employee.
 * An example of use is selecting the line manager of another employee.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<div class="row-fluid">
    <div class="span12">
<table cellpadding="0" cellspacing="0" border="0" class="display" id="employees" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('users_employees_thead_id');?></th>
            <th><?php echo lang('users_employees_thead_firstname');?></th>
            <th><?php echo lang('users_employees_thead_lastname');?></th>
            <th><?php echo lang('users_employees_thead_email');?></th>
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
    </div>
</div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="<?php echo base_url();?>assets/datatable/select/css/select.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/select/js/dataTables.select.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    //Transform the HTML table in a fancy datatable
    $('#employees').dataTable({
        select: 'single',
        "pageLength": 5,
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
    //Hide pagination select box in order to save space
    $('.dataTables_length').css("display", "none");
});
</script>
