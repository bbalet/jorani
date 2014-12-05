<?php 
/*
 * This file is part of lms.
 *
 * lms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with lms.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('requests', $language);
$this->lang->load('datatable', $language);
$this->lang->load('global', $language);?>

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

<div class="row-fluid">
    <div class="span12">
        
<h1><?php echo lang('requests_collaborators_title');?> &nbsp;</h1>

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
            <?php echo $collaborator['id'] ?>
            <div class="pull-right">
                <a href="<?php echo base_url();?>requests/counters/<?php echo $collaborator['id'] ?>" title="<?php echo lang('requests_collaborators_thead_link_balance');?>"><i class="icon-info-sign"></i></a>
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
