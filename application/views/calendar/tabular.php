<?php
/**
 * This view builds a monthly tabular calendar for a group of employees.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.3.0
 */
?>

<h2><?php echo lang('calendar_tabular_title');?> &nbsp;<?php echo $help;?></h2>

<div class="row-fluid">
    <div class="span4">
        <label for="txtEntity"><?php echo lang('calendar_organization_field_select_entity');?></label>
        <div class="input-append">
            <input type="text" id="txtEntity" name="txtEntity" value="<?php echo $department;?>" readonly />
            <button id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('calendar_tabular_button_select_entity');?></button>
        </div>
        
        <label for="cboMonth"><?php echo lang('calendar_tabular_field_month');?></label>
        <select name="cboMonth" id="cboMonth">
            <?php for ($ii=1; $ii<13;$ii++) {
                if ($ii == $month) {
                    echo "<option val='" . $ii ."' selected>" . $ii ."</option>";
                } else {
                    echo "<option val='" . $ii ."'>" . $ii ."</option>";
                }
            }?>
        </select>
        
        <label for="cboYear"><?php echo lang('calendar_tabular_field_year');?></label>
        <select name="cboYear" id="cboYear">
            <?php 
            $len = date('Y', strtotime('+2 year'));
            for ($ii=date('Y', strtotime('-6 year')); $ii<$len;$ii++) {
                if ($ii == $year) {
                    echo "<option val='" . $ii ."' selected>" . $ii ."</option>";
                } else {
                    echo "<option val='" . $ii ."'>" . $ii ."</option>";
                }
            }?>
        </select>
        
    </div>
    <div class="span3">
        <label for="chkIncludeChildren">
            <input type="checkbox" value="" id="chkIncludeChildren" name="chkIncludeChildren"> <?php echo lang('calendar_tabular_check_include_subdept');?>
        </label>
    </div>
    <div class="span5">
        <div class="row-fluid">
            <div class="span12">
                <button id="cmdPrevious" class="btn btn-primary"><i class="icon-chevron-left icon-white"></i></button>
                <button id="cmdExecute" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp;<?php echo lang('calendar_tabular_button_execute');?></button>
                <button id="cmdNext" class="btn btn-primary"><i class="icon-chevron-right icon-white"></i></button>
            </div>
        </div>
        <div class="row-fluid"><div class="span12">&nbsp;</div></div>
        <div class="row-fluid">
            <div class="span12">
                <button id="cmdExport" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp;<?php echo lang('calendar_tabular_button_export');?></button>
            </div>
        </div>

    </div>
</div>

<div class="row-fluid">
    <div class="span3"><span class="label"><?php echo lang('Planned');?></span></div>
    <div class="span3"><span class="label label-success"><?php echo lang('Accepted');?></span></div>
    <div class="span3"><span class="label label-warning"><?php echo lang('Requested');?></span></div>
    <div class="span3">&nbsp;</div>
</div>

<?php if (count($tabular) > 0) {?>
<table class="table table-bordered">
    <thead>
        <tr>
            <td>&nbsp;</td>
            <?php
                $start = $year . '-' . $month . '-' . '1';    //first date of selected month
                $lastDay = date("t", strtotime($start));    //last day of selected month
                for ($ii = 1; $ii <=$lastDay; $ii++) {
                    $dayNum = date("N", strtotime($year . '-' . $month . '-' . $ii));
                    switch ($dayNum)
                    {
                        case 1: echo '<td><b>' . lang('calendar_monday_short') . '</b></td>'; break;
                        case 2: echo '<td><b>' . lang('calendar_tuesday_short') . '</b></td>'; break;
                        case 3: echo '<td><b>' . lang('calendar_wednesday_short') . '</b></td>'; break;
                        case 4: echo '<td><b>' . lang('calendar_thursday_short') . '</b></td>'; break;
                        case 5: echo '<td><b>' . lang('calendar_friday_short') . '</b></td>'; break;
                        case 6: echo '<td><b>' . lang('calendar_saturday_short') . '</b></td>'; break;
                        case 7: echo '<td><b>' . lang('calendar_sunday_short') . '</b></td>'; break;
                    }
                }?>
        </tr>
        <tr>
            <td><b><?php echo lang('calendar_tabular_thead_employee');?></b></td>
            <?php
                $start = $year . '-' . $month . '-' . '1';    //first date of selected month
                $lastDay = date("t", strtotime($start));    //last day of selected month
                for ($ii = 1; $ii <=$lastDay; $ii++) {
                    echo '<td><b>' . $ii . '</b></td>';
                }?>
        </tr>
    </thead>
  <tbody>
  <?php
/*
 * This partial view builds a "linear" calendar (which is technically a line into an HTML table).
 * A linear calendar displays the leaves of an employee during a month. Each cell is a day.
 * This partial view is included into the monthly presence report and the tabular calendar.
 */
  $repeater = 0;
  foreach ($tabular as $employee) {?>
    <tr>
      <td><?php echo $employee->name; ?></td>
      <?php foreach ($employee->days as $day) {
          $overlapping = FALSE;
          if (strstr($day->display, ';')) {
              $periods = explode(";", $day->display);
              $statuses = explode(";", $day->status);
                switch (intval($statuses[1]))
                {
                    case 1: $class = "planned"; break;  // Planned
                    case 2: $class = "requested"; break;  // Requested
                    case 3: $class = "accepted"; break;  // Accepted
                    case 4: $class = "rejected"; break;  // Rejected
                    case 5: $class="dayoff"; break;
                    case 6: $class="dayoff"; break;
                }
                switch (intval($statuses[0]))
                {
                    case 1: $class .= "planned"; break;  // Planned
                    case 2: $class .= "requested"; break;  // Requested
                    case 3: $class .= "accepted"; break;  // Accepted
                    case 4: $class .= "rejected"; break;  // Rejected
                    case 5: $class .="dayoff"; break;
                    case 6: $class .="dayoff"; break;
                }
                //If we have two requests the same day (morning/afternoon)
                if (($statuses[0] == $statuses[1]) && ($periods[0] != $periods[1])){
                    switch (intval($statuses[0]))
                    {
                        case 1: $class = "allplanned"; break;  // Planned
                        case 2: $class = "allrequested"; break;  // Requested
                        case 3: $class = "allaccepted"; break;  // Accepted
                        case 4: $class = "allrejected"; break;  // Rejected
                        //The 2 cases below would be weird...
                        case 5: $class ="dayoff"; break;
                        case 6: $class ="dayoff"; break;
                    }
                }
          } else {
            switch ($day->display) {
                case '9': $class="error"; break;
                case '0': $class="working"; break;
                case '4': $class="dayoff"; break;
                case '5': $class="amdayoff"; break;
                case '6': $class="pmdayoff"; break;
                case '1':
                      switch ($day->status)
                      {
                          case 1: $class = "allplanned"; break;  // Planned
                          case 2: $class = "allrequested"; break;  // Requested
                          case 3: $class = "allaccepted"; break;  // Accepted
                          case 4: $class = "allrejected"; break;  // Rejected
                      }
                      break;
                case '2':
                    switch ($day->status)
                      {
                          case 1: $class = "amplanned"; break;  // Planned
                          case 2: $class = "amrequested"; break;  // Requested
                          case 3: $class = "amaccepted"; break;  // Accepted
                          case 4: $class = "amrejected"; break;  // Rejected
                      }
                    break;
                case '3':
                    switch ($day->status)
                      {
                          case 1: $class = "pmplanned"; break;  // Planned
                          case 2: $class = "pmrequested"; break;  // Requested
                          case 3: $class = "pmaccepted"; break;  // Accepted
                          case 4: $class = "pmrejected"; break;  // Rejected
                      }
                    break;
            }
          }
          
          //Detect overlapping cases
          if (substr_count($day->display, ";") > 1) $overlapping = TRUE;
          switch ($class) {
                    case "plannedplanned":
                    case "requestedrequested":
                    case "acceptedaccepted":
                    case "rejectedrejected":
                        $overlapping = TRUE;
              break;
          }
            if ($class == "error"){
                echo '<td><img src="'.  base_url() .'assets/images/date_error.png"></td>';
            } else {
                if ($overlapping) {
                    echo '<td title="' . $day->type . '" class="' . $class . '"><img src="' . base_url() . 'assets/images/date_error.png"></td>';
                } else {
                    echo '<td title="' . $day->type . '" class="' . $class . '">&nbsp;</td>';
                }
            }
            ?>
    <?php } ?>
          </tr>
    <?php      
    if (++$repeater>=10) {
        $repeater = 0;?>
        <tr>
            <td>&nbsp;</td>
            <?php
                $start = $year . '-' . $month . '-' . '1';    //first date of selected month
                $lastDay = date("t", strtotime($start));    //last day of selected month
                for ($ii = 1; $ii <=$lastDay; $ii++) {
                    $dayNum = date("N", strtotime($year . '-' . $month . '-' . $ii));
                    switch ($dayNum)
                    {
                        case 1: echo '<td><b>' . lang('calendar_monday_short') . '</b></td>'; break;
                        case 2: echo '<td><b>' . lang('calendar_tuesday_short') . '</b></td>'; break;
                        case 3: echo '<td><b>' . lang('calendar_wednesday_short') . '</b></td>'; break;
                        case 4: echo '<td><b>' . lang('calendar_thursday_short') . '</b></td>'; break;
                        case 5: echo '<td><b>' . lang('calendar_friday_short') . '</b></td>'; break;
                        case 6: echo '<td><b>' . lang('calendar_saturday_short') . '</b></td>'; break;
                        case 7: echo '<td><b>' . lang('calendar_sunday_short') . '</b></td>'; break;
                    }
                }?>
        </tr>
    <tr>
        <td><b><?php echo lang('calendar_tabular_thead_employee');?></b></td>
        <?php for ($ii = 1; $ii <=$lastDay; $ii++) echo '<td><b>' . $ii . '</b></td>';?>
    </tr>
    <?php }
    }?>
  </tbody>
</table>
<?php } ?>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('calendar_tabular_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn"><?php echo lang('calendar_tabular_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn"><?php echo lang('calendar_tabular_popup_entity_button_cancel');?></a>
    </div>
</div>

<script src="<?php echo base_url();?>assets/js/modernizr.min.js"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript">
    var entity = -1; //Id of the selected entity
    var text; //Label of the selected entity
    var entity = <?php echo $entity;?>;
    var month = <?php echo $month;?>;
    var year = <?php echo $year;?>;
    var children = '<?php echo $children;?>';
    
    function select_entity() {
        entity = $('#organization').jstree('get_selected')[0];
        text = $('#organization').jstree().get_text(entity);
        $('#txtEntity').val(text);
        $("#frmSelectEntity").modal('hide');
    }
    
    function includeChildren() {
        if ($('#chkIncludeChildren').prop('checked') == true) {
            return 'true';
        } else {
            return 'false';
        }
    }
    
    //Execute the report
    //Target : execution in the page or export to Excel
    function executeReport(month, year, children, target) {
        if (entity != -1) {
            url = '<?php echo base_url();?>calendar/' + target + '/' + entity + '/' + month+ '/' + year+ '/' + children;
            document.location.href = url;
        }
    }
    
    $(document).ready(function() {
        //Select radio button depending on URL
        if (children == '1') {
            $("#chkIncludeChildren").prop("checked", true);
        } else {
            $("#chkIncludeChildren").prop("checked", false);
        }
        
        //Popup select entity
        $("#cmdSelectEntity").click(function() {
            $("#frmSelectEntity").modal('show');
            $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
        });

        //Execute the report
        $('#cmdExecute').click(function() {
            month = $('#cboMonth').val();
            year = $('#cboYear').val();
            children = includeChildren();
            executeReport(month, year, children, 'tabular');
        });

        //Export the report into Excel
        $("#cmdExport").click(function() {
            month = $('#cboMonth').val();
            year = $('#cboYear').val();
            children = includeChildren();
            executeReport(month, year, children, 'tabular/export');
        });

<?php $datePrev = date_create($year . '-' . $month . '-01');
$dateNext = clone $datePrev;
date_add($dateNext, date_interval_create_from_date_string('1 month'));
date_sub($datePrev, date_interval_create_from_date_string('1 month'));?>
        //Previous/Next
        $('#cmdPrevious').click(function() {
            month = <?php echo $datePrev->format('m'); ?>;
            year = <?php echo $datePrev->format('Y'); ?>;
            children = includeChildren();
            url = '<?php echo base_url();?>calendar/tabular/' + entity + '/' + month+ '/' + year+ '/' + children;
            document.location.href = url;
        });
        $('#cmdNext').click(function() {
            month = <?php echo $dateNext->format('m'); ?>;
            year = <?php echo $dateNext->format('Y'); ?>;
            children = includeChildren();
            url = '<?php echo base_url();?>calendar/tabular/' + entity + '/' + month+ '/' + year+ '/' + children;
            document.location.href = url;
        });
        
        //Load alert forms
        $("#frmSelectEntity").alert();
        //Prevent to load always the same content (refreshed each time)
        $('#frmSelectEntity').on('hidden', function() {
            $(this).removeData('modal');
        });
    });
</script>
