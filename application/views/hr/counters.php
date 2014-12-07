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
$this->lang->load('hr', $language);
$this->lang->load('entitleddays', $language);
$this->lang->load('global', $language);?>

<div class="row-fluid">
    <div class="span12">
        <h2><?php echo lang('hr_summary_title');?>&nbsp;<?php echo $employee_id; ?>&nbsp;<span class="muted"> (<?php echo $employee_name; ?>)</span> &nbsp; 
        <a href="<?php echo lang('global_link_doc_page_my_summary');?>" title="<?php echo lang('global_link_tooltip_documentation');?>" target="_blank" rel="nofollow"><i class="icon-question-sign"></i></a></h2>

        <table class="table table-bordered table-hover">
        <thead>
            <tr>
              <th><?php echo lang('hr_summary_thead_type');?></th>
              <th><?php echo lang('hr_summary_thead_available');?></th>
              <th><?php echo lang('hr_summary_thead_taken');?></th>
              <th><?php echo lang('hr_summary_thead_entitled');?></th>
              <th><?php echo lang('hr_summary_thead_description');?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($summary as $key => $value) { ?>
            <tr>
              <td><?php echo $key; ?></td>
              <td><?php echo ((float) $value[1] - (float) $value[0]); ?></td>
              <td><?php if ($value[2] == '') { echo ((float) $value[0]); } else { echo '-'; } ?></td>
              <td><?php if ($value[2] == '') { echo ((float) $value[1]); } else { echo '-'; } ?></td>
              <td><?php echo $value[2]; ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <h3><a href="<?php echo base_url();?>entitleddays/contract/<?php echo $contract_id; ?>"><?php echo lang('entitleddays_counters_title_contract');?><?php echo $contract_id; ?></a>&nbsp;<span class="muted"> (<?php echo $contract_name; ?>)</span></h3>
        <p><?php echo lang('entitleddays_counters_description_contract');?><?php echo $contract_start; ?> - <?php echo $contract_end; ?></p>

        <table id="entitleddayscontract">
        <thead>
            <tr>
              <th><?php echo lang('entitleddays_contract_index_thead_start');?></th>
              <th><?php echo lang('entitleddays_contract_index_thead_end');?></th>
              <th><?php echo lang('entitleddays_contract_index_thead_days');?></th>
              <th><?php echo lang('entitleddays_contract_index_thead_type');?></th>
              <th><?php echo lang('entitleddays_contract_index_thead_description');?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($entitleddayscontract as $days) { ?>
            <tr>    
        <?php $startDate = new DateTime($days['startdate']);
        $endDate = new DateTime($days['enddate']);?>
              <td data-order="<?php echo $startDate->getTimestamp(); ?>"><?php echo $startDate->format(lang('global_date_format'));?></td>
              <td data-order="<?php echo $endDate->getTimestamp(); ?>"><?php echo $endDate->format(lang('global_date_format'));?></td>
              <td><?php echo $days['days']; ?></td>
              <td><?php echo $days['type']; ?></td>
              <td><?php echo $days['description']; ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <h3><a href="<?php echo base_url();?>entitleddays/user/<?php echo $employee_id; ?>"><?php echo lang('entitleddays_counters_title_employee');?></a></h3>
        <table id="entitleddaysemployee">
        <thead>
            <tr>
              <th><?php echo lang('entitleddays_user_index_thead_start');?></th>
              <th><?php echo lang('entitleddays_user_index_thead_end');?></th>
              <th><?php echo lang('entitleddays_user_index_thead_days');?></th>
              <th><?php echo lang('entitleddays_user_index_thead_type');?></th>
              <th><?php echo lang('entitleddays_user_index_thead_description');?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($entitleddaysemployee as $days) { ?>
            <tr>
              <td><?php 
        $date = new DateTime($days['startdate']);
        echo $date->format(lang('global_date_format'));
        ?></td>
              <td><?php 
        $date = new DateTime($days['enddate']);
        echo $date->format(lang('global_date_format'));
        ?></td>
              <td><?php echo $days['days']; ?></td>
              <td><?php echo $days['type']; ?></td>
              <td><?php echo $days['description']; ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
    </div>
</div>

<div class="row-fluid">
    <div class="span3">
      <a href="<?php echo base_url();?>hr/employees" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('hr_summary_button_list');?></a>
    </div>
    <div class="span9">&nbsp;</div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(function () {       
        //Transform the HTML table in a fancy datatable
        $('#entitleddayscontract').dataTable({
                    "order": [[ 0, "desc" ]],
                    "bFilter": false,
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
            
        $('#entitleddaysemployee').dataTable({
                    "order": [[ 0, "desc" ]],
                    "bFilter": false,
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
