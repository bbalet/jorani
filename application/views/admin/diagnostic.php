<?php
/**
 * This view displays the diagnotic of confiuration and what was entered by employees (requests, etc.)
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.6
 */
?>

<div class="row-fluid">
    <div class="span12">
        <h2><?php echo $title;?><?php echo $help;?></h2>
        
        <p><?php echo lang('admin_diagnostic_description');?></p>

<?php
$daysOffYears_badge = ($daysOffYears_count == 0)?'':'<span class="badge badge-info">' . $daysOffYears_count . '</span>&nbsp;';
$duplicatedLeaves_badge = ($duplicatedLeaves_count == 0)?'':'<span class="badge badge-info">' . $duplicatedLeaves_count . '</span>&nbsp;';
$wrongDateType_badge = ($wrongDateType_count == 0)?'':'<span class="badge badge-info">' . $wrongDateType_count . '</span>&nbsp;';
$entitlmentOverflow_badge = ($entitlmentOverflow_count == 0)?'':'<span class="badge badge-info">' . $entitlmentOverflow_count . '</span>&nbsp;';
$negativeOvertime_badge = ($negativeOvertime_count == 0)?'':'<span class="badge badge-info">' . $negativeOvertime_count . '</span>&nbsp;';
$unusedContracts_badge = ($unusedContracts_count == 0)?'':'<span class="badge badge-info">' . $unusedContracts_count . '</span>&nbsp;';
$leaveBalance_badge = ($leaveBalance_count == 0)?'':'<span class="badge badge-info">' . $leaveBalance_count . '</span>&nbsp;';

?>
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#daysoff"><?php echo $daysOffYears_badge . lang('admin_diagnostic_daysoff_tab');?></a></li>
    <li><a data-toggle="tab" href="#requests"><?php echo $duplicatedLeaves_badge . lang('admin_diagnostic_requests_tab');?></a></li>
    <li><a data-toggle="tab" href="#datetypes"><?php echo $wrongDateType_badge . lang('admin_diagnostic_datetype_tab');?></a></li>
    <li><a data-toggle="tab" href="#entitlements"><?php echo $entitlmentOverflow_badge . lang('admin_diagnostic_entitlements_tab');?></a></li>
    <li><a data-toggle="tab" href="#overtime"><?php echo $negativeOvertime_badge . lang('admin_diagnostic_overtime_tab');?></a></li>
    <li><a data-toggle="tab" href="#contracts"><?php echo $unusedContracts_badge . lang('admin_diagnostic_contract_tab');?></a></li>
    <li><a data-toggle="tab" href="#balance"><?php echo $leaveBalance_badge . lang('admin_diagnostic_balance_tab');?></a></li>
</ul>

<div class="tab-content">

  <div class="tab-pane active" id="daysoff">
    
    <p><?php echo lang('admin_diagnostic_daysoff_description');?></p>
    
    <table class="table table-bordered table-hover table-condensed">
      <thead>
        <tr>
            <th><?php echo lang('admin_diagnostic_daysoff_thead_id');?></th>
            <th><?php echo lang('admin_diagnostic_daysoff_thead_name');?></th>
            <th><?php echo lang('admin_diagnostic_daysoff_thead_ym1');?></th>
            <th><?php echo lang('admin_diagnostic_daysoff_thead_y');?></th>
            <th><?php echo lang('admin_diagnostic_daysoff_thead_yp1');?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($daysOffYears as $yearDef): ?>
        <tr>
            <td><a target="_blank" href="<?php echo base_url();?>contracts/<?php echo $yearDef['contract'];?>/calendar"><?php echo $yearDef['contract'];?></a></td>
            <td><?php echo $yearDef['name'];?></td>
            <td><?php echo $yearDef['ym1'];?></td>
            <?php if ($yearDef['y'] == 0) {?>
            <td style="background:#f5811e;color:white;"><b><?php echo $yearDef['y'];?></b></td>
            <?php } else {?>
            <td><?php echo $yearDef['y'];?></td>
            <?php } ?>
            <td><?php echo $yearDef['yp1'];?></td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
    
  <div class="tab-pane" id="requests">
      
        <p><?php echo lang('admin_diagnostic_requests_description');?></p>
      
        <?php if ($duplicatedLeaves_count==0) {?>
        <p><b><?php echo lang('admin_diagnostic_no_error');?></b></p>
        <?php } else {?>
        <table class="table table-bordered table-hover table-condensed">
          <thead>
            <tr>
                <th><?php echo lang('admin_diagnostic_requests_thead_id');?></th>
                <th><?php echo lang('admin_diagnostic_requests_thead_employee');?></th>
                <th><?php echo lang('admin_diagnostic_requests_thead_start_date');?></th>
                <th><?php echo lang('admin_diagnostic_requests_thead_type');?></th>
            </tr>
          </thead>
          <tbody>
        <?php foreach ($duplicatedLeaves as $leave): 
            $date = new DateTime($leave['startdate']);
            $startdate = $date->format(lang('global_date_format'));?>
            <tr>
                <td><a target="_blank" href="<?php echo base_url();?>leaves/edit/<?php echo $leave['id'];?>"><?php echo $leave['id'];?></a></td>
                <td><?php echo $leave['user_label'];?></td>
                <td><?php echo $startdate;?></td>
                <td><?php echo $leave['type_label'];?></td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
        <?php }?>
  </div>
    
  <div class="tab-pane" id="datetypes">
      
        <p><?php echo lang('admin_diagnostic_datetype_description');?></p>
      
        <?php if ($wrongDateType_count==0) {?>
        <p><b><?php echo lang('admin_diagnostic_no_error');?></b></p>
        <?php } else {?>
        <table class="table table-bordered table-hover table-condensed">
          <thead>
            <tr>
                <th><?php echo lang('admin_diagnostic_datetype_thead_id');?></th>
                <th><?php echo lang('admin_diagnostic_datetype_thead_employee');?></th>
                <th><?php echo lang('admin_diagnostic_datetype_thead_start_date');?></th>
                <th><?php echo lang('admin_diagnostic_datetype_thead_start_type');?></th>
                <th><?php echo lang('admin_diagnostic_datetype_thead_end_type');?></th>
                <th><?php echo lang('admin_diagnostic_datetype_thead_status');?></th>
            </tr>
          </thead>
          <tbody>
        <?php foreach ($wrongDateType as $dateType): 
            $date = new DateTime($dateType['startdate']);
            $startdate = $date->format(lang('global_date_format'));?>
            <tr>
                <td><a target="_blank" href="<?php echo base_url();?>leaves/edit/<?php echo $dateType['id'];?>"><?php echo $dateType['id'];?></a></td>
                <td><?php echo $dateType['user_label'];?></td>
                <td><?php echo $startdate;?></td>
                <td><?php echo lang($dateType['startdatetype']);?></td>
                <td><?php echo lang($dateType['enddatetype']);?></td>
                <td><?php echo lang($dateType['status_label']);?></td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
        <?php }?>
  </div>

  <div class="tab-pane" id="entitlements">
      
      <p><?php echo lang('admin_diagnostic_entitlements_description');?></p>
      
        <?php if ($entitlmentOverflow_count==0) {?>
        <p><b><?php echo lang('admin_diagnostic_no_error');?></b></p>
        <?php } else {?>
        <table class="table table-bordered table-hover table-condensed">
          <thead>
            <tr>
                <th><?php echo lang('admin_diagnostic_entitlements_thead_id');?></th>
                <th><?php echo lang('admin_diagnostic_entitlements_thead_type');?></th>
                <th><?php echo lang('admin_diagnostic_entitlements_thead_name');?></th>
                <th><?php echo lang('admin_diagnostic_entitlements_thead_start_date');?></th>
                <th><?php echo lang('admin_diagnostic_entitlements_thead_end_date');?></th>
            </tr>
          </thead>
          <tbody>
        <?php foreach ($entitlmentOverflow as $entitlment): 
            $date = new DateTime($entitlment['startdate']);
            $startdate = $date->format(lang('global_date_format'));
            $date = new DateTime($entitlment['enddate']);
            $enddate = $date->format(lang('global_date_format'));
            if (!is_null($entitlment['contract'])) {?>
            <tr>
                <td><a target="_blank" href="<?php echo base_url();?>entitleddays/contract/<?php echo $entitlment['contract'];?>"><?php echo $entitlment['id'];?></a></td>
                <td><?php echo lang('admin_diagnostic_entitlements_type_contract');?></td>
                <td><?php echo (is_null($entitlment['contract_label'])?lang('admin_diagnostic_entitlements_deletion_problem'):$entitlment['contract_label']);?></td>
                <td><?php echo $startdate;?></td>
                <td><?php echo $enddate;?></td>
            </tr>
            <?php } else {?>
            <tr>
                <td><a target="_blank" href="<?php echo base_url();?>entitleddays/user/<?php echo $entitlment['employee'];?>"><?php echo $entitlment['id'];?></a></td>
                <td><?php echo lang('admin_diagnostic_entitlements_type_employee');?></td>
                <td><?php echo (is_null($entitlment['user_label'])?lang('admin_diagnostic_entitlements_deletion_problem'):$entitlment['user_label']);?></td>
                <td><?php echo $startdate;?></td>
                <td><?php echo $enddate;?></td>
            </tr>
            <?php 
                }
                endforeach ?>
          </tbody>
        </table>
        <?php }?>
  </div>
    
  <div class="tab-pane" id="overtime">
      
        <p><?php echo lang('admin_diagnostic_overtime_description');?></p>

        <?php if ($negativeOvertime_count==0) {?>
        <p><b><?php echo lang('admin_diagnostic_no_error');?></b></p>
        <?php } else {?>
        <table class="table table-bordered table-hover table-condensed">
          <thead>
            <tr>
                <th><?php echo lang('admin_diagnostic_daysoff_thead_id');?></th>
                <th><?php echo lang('admin_diagnostic_daysoff_thead_employee');?></th>
                <th><?php echo lang('admin_diagnostic_daysoff_thead_date');?></th>
                <th><?php echo lang('admin_diagnostic_daysoff_thead_duration');?></th>
                <th><?php echo lang('admin_diagnostic_daysoff_thead_status');?></th>
            </tr>
          </thead>
          <tbody>
        <?php foreach ($negativeOvertime as $overtime): 
            $date = new DateTime($overtime['date']);
            $date = $date->format(lang('global_date_format'));?>
            <tr>
                <td><a target="_blank" href="<?php echo base_url();?>extra/edit/<?php echo $overtime['id'];?>"><?php echo $overtime['id'];?></a></td>
                <td><?php echo $overtime['user_label'];?></td>
                <td><?php echo $date;?></td>
                <td><?php echo $overtime['duration'];?></td>
                <td><?php echo lang($overtime['status_label']);?></td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
        <?php }?>
  </div>
    
  <div class="tab-pane" id="contracts">
    <p><a target="_blank" href="<?php echo base_url();?>contracts"><?php echo lang('admin_diagnostic_contract_description');?></a></p>

    <?php if ($unusedContracts_count==0) {?>
    <p><b><?php echo lang('admin_diagnostic_no_error');?></b></p>
    <?php } else {?>
    <table class="table table-bordered table-hover table-condensed">
      <thead>
        <tr>
            <th><?php echo lang('admin_diagnostic_contract_thead_id');?></th>
            <th><?php echo lang('admin_diagnostic_contract_thead_name');?></th>
        </tr>
      </thead>
      <tbody>
    <?php foreach ($unusedContracts as $contract):?>
        <tr>
            <td><?php echo $contract['id'];?></td>
            <td><?php echo $contract['name'];?></td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
    <?php }?>
  </div><!-- Unused contracts //-->
    
  <div class="tab-pane" id="balance">
    <p><?php echo lang('admin_diagnostic_balance_description');?></a></p>

    <?php if ($leaveBalance_count==0) {?>
    <p><b><?php echo lang('admin_diagnostic_no_error');?></b></p>
    <?php } else {?>
    <table class="table table-bordered table-hover table-condensed">
      <thead>
        <tr>
            <th><?php echo lang('admin_diagnostic_contract_thead_id');?></th>
            <th><?php echo lang('admin_diagnostic_balance_thead_employee');?></th>
            <th><?php echo lang('admin_diagnostic_balance_thead_contract');?></th>
            <th><?php echo lang('admin_diagnostic_balance_thead_start_date');?></th>
            <th><?php echo lang('admin_diagnostic_balance_thead_status');?></th>
        </tr>
      </thead>
      <tbody>
    <?php foreach ($leaveBalance as $balance): 
        $date = new DateTime($balance['startdate']);
        $startdate = $date->format(lang('global_date_format'))?>
        <tr>
            <td><a target="_blank" href="<?php echo base_url();?>leaves/edit/<?php echo $balance['id'];?>"><?php echo $balance['id'];?></a></td>
            <td>
                <a target="_blank" href="<?php echo base_url();?>hr/counters/employees/<?php echo $balance['employee'];?>"><i class="icon-info-sign"></i></a>
                <a target="_blank" href="<?php echo base_url();?>entitleddays/user/<?php echo $balance['employee'];?>"><i class="icon-edit"></i></a>
                <?php echo $balance['user_label'];?>
            </td>
            <td>
                <a target="_blank" href="<?php echo base_url();?>entitleddays/contract/<?php echo $balance['employee'];?>"><i class="icon-edit"></i></a>
                <?php echo $balance['contract_label'];?>
            </td>
            <td><?php echo $startdate;?></td>
            <td><?php echo $balance['status_label'];?></td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
    <?php }?>
  </div><!-- Leave Balance //-->
    
</div>
    </div>
</div>

<script type="text/javascript">
$(function () {
    //open a tab if a hash was passed by URL
    var url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    }
    
    //Change hash for page-reload
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    })
});
</script>
