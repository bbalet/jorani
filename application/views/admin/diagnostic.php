<?php
/**
 * This view displays the diagnotic of confiuration and what was entered by employees (requests, etc.)
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.6
 */
?>

<div class="row-fluid">
    <div class="span12">
        <h2><?php echo $title;?><?php echo $help;?></h2>
        
        <p><?php echo lang('admin_diagnostic_description');?></p>

<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#daysoff"><?php echo lang('admin_diagnostic_daysoff_tab');?></a></li>
  <li><a data-toggle="tab" href="#requests"><?php echo lang('admin_diagnostic_requests_tab');?></a></li>
  <li><a data-toggle="tab" href="#datetypes"><?php echo lang('admin_diagnostic_datetype_tab');?></a></li>
  <li><a data-toggle="tab" href="#entitlements"><?php echo lang('admin_diagnostic_entitlements_tab');?></a></li>
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
      
        <?php if (count($duplicatedLeaves)==0) {?>
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
                <td><?php echo $leave['types_label'];?></td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
        <?php }?>
  </div>
    
  <div class="tab-pane" id="datetypes">
      
        <p><?php echo lang('admin_diagnostic_datetype_description');?></p>
      
        <?php if (count($wrongDateType)==0) {?>
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
                <td><?php echo $dateType['status_label'];?></td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
        <?php }?>
  </div>

  <div class="tab-pane" id="entitlements">
      
      <p><?php echo lang('admin_diagnostic_entitlements_description');?></p>
      
        <?php if (count($entitlmentOverflow)==0) {?>
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
</div>

    </div>
</div>
