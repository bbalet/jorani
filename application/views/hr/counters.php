<?php
/**
 * This view allows a manager or HR admin to visualize the leave balance (taken/available/entitled) of an employee.
 * @copyright  Copyright (c) 2014-2015 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<div class="row-fluid">
    <div class="span12">
        <h2><?php echo lang('hr_summary_title');?>&nbsp;<?php echo $employee_id; ?>&nbsp;<span class="muted"> (<?php echo $employee_name; ?>)</span>&nbsp;<?php echo $help;?></h2>

        <p><?php echo lang('hr_summary_date_field');?>&nbsp;
            <input type="text" id="refdate" />
        </p>
        
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
              <td><?php echo round(((float) $value[1] - (float) $value[0]), 3, PHP_ROUND_HALF_DOWN); ?></td>
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
        <?php if ($source == 'employees') {?>
        <h3><a href="<?php echo base_url();?>entitleddays/contract/<?php echo $contract_id; ?>"><?php echo lang('entitleddays_counters_title_contract');?><?php echo $contract_id; ?></a>&nbsp;<span class="muted"> (<?php echo $contract_name; ?>)</span></h3>
        <?php } else { ?>
        <h3><?php echo lang('entitleddays_counters_title_contract') . ' ' . $contract_id; ?>&nbsp;<span class="muted"> (<?php echo $contract_name; ?>)</span></h3>
        <?php } ?>
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
              <td><?php echo $days['type_name']; ?></td>
              <td><?php echo $days['description']; ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php if ($source == 'employees') {?>
        <h3><a href="<?php echo base_url();?>entitleddays/user/<?php echo $employee_id; ?>"><?php echo lang('entitleddays_counters_title_employee');?></a></h3>
        <?php } else { ?>
        <h3><?php echo lang('entitleddays_counters_title_employee');?></h3>
        <?php } ?>
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
              <td><?php echo $days['type_name']; ?></td>
              <td><?php echo $days['description']; ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
    </div>
</div>

<div class="row-fluid">
    <div class="span3">
      <?php if ($source == 'employees') {?>
      <a href="<?php echo base_url();?>hr/employees" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('hr_summary_button_list');?></a>
      <?php } else { ?>
      <a href="<?php echo base_url();?>requests/collaborators" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('hr_summary_button_list');?></a>
      <?php } ?>
    </div>
    <div class="span9">&nbsp;</div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {
        //Init datepicker widget (it is complicated because we cannot based it on UTC)
        isDefault = <?php echo $isDefault;?>;
        moment.locale('<?php echo $language_code;?>', {longDateFormat : {L : '<?php echo lang('global_date_momentjs_format');?>'}});
        reportDate = '<?php $date = new DateTime($refDate); echo $date->format(lang('global_date_format'));?>';
        todayDate = moment().format('L');
        if (isDefault == 1) {
            $("#refdate").val(todayDate);
        } else {
            $("#refdate").val(reportDate);
        }
        $('#refdate').datepicker({
            onSelect: function(dateText, inst) {
                    tmpUnix = moment($("#refdate").datepicker("getDate")).unix();
                    url = "<?php echo base_url();?>hr/counters/<?php echo $source; ?>/<?php echo $employee_id; ?>/" + tmpUnix;
                    window.location = url;
            }
        });
        
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
