<?php
/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jorani is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('calendar', $language);
$this->lang->load('global', $language);?>

<link href="<?php echo base_url();?>assets/css/tabular.css" rel="stylesheet">
<h1><?php echo lang('calendar_tabular_title');?> &nbsp;<?php echo $help;?></h1>
<div class="row-fluid">
    <div class="span4">
        <label for="txtEntity"><?php echo lang('calendar_organization_field_select_entity');?></label>
        <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" value="<?php echo $department;?>" readonly />
        <button id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('calendar_tabular_button_select_entity');?></button>
	<?php $listMonth = array(lang('January'), lang('February'), lang('March'), lang('April'), lang('May'), lang('June'), lang('July'), lang('August'), lang('September'), lang('October'), lang('November'), lang('December')); ?>        
        <label for="cboMonth"><?php echo lang('calendar_tabular_field_month');?></label>
        <select name="cboMonth" id="cboMonth">
            <?php for ($ii=1; $ii<13;$ii++) {
                if ($ii == $month) {
                    echo "<option value=" . $ii ." selected>" . $listMonth[$ii -1] . "</option>";
                } else {
                    echo "<option value=" . $ii .">" . $listMonth[$ii -1] . "</option>";
                }
            }?>
        </select>
        
        <label for="cboYear"><?php echo lang('calendar_tabular_field_year');?></label>
        <select name="cboYear" id="cboYear">
            <?php for ($ii=date('Y', strtotime('-6 year')); $ii<date('Y', strtotime('+2 year'));$ii++) {
                if ($ii == $year) {
                    echo "<option value='" . $ii ."' selected>" . $ii ."</option>";
                } else {
                    echo "<option value='" . $ii ."'>" . $ii ."</option>";
                }
            }?>
        </select>
        </div>
    </div>
    <div class="span3">
        <label class="checkbox">
            <input type="checkbox" value="" id="chkIncludeChildren"> <?php echo lang('calendar_tabular_check_include_subdept');?>
        </label>
    </div>
    <div class="span5">
        <button id="cmdExecute" class="btn btn-primary"><?php echo lang('calendar_tabular_button_execute');?></button>
	<br />
	<br />
	<?php
	foreach ($leavetypes as $type)
	{
	  echo '<div style="height: 100%; width: 50%; padding: 5px; background-color: ' . $type['color'] . ' !important; -webkit-print-color-adjust: exact"></div>' . $type['name'] . '';
	  echo '<br />';
	}
	?>
    </div>
</div>
<button id="cmdPrev" class="btn btn-primary"><</button>
<button id="cmdToday" class="btn btn-primary"><?php echo lang('calendar_component_buttonText_today');?></button>
<button id="cmdNext" class="btn btn-primary">></button>
<button id="cmdPrint" class="btn btn-primary">Print calendar</button>
<br />
<br />
<?php if (count($tabular) == 0) {
      echo lang('leaves_summary_tbody_empty');
} else {
?>
<?php
		$maxDay = 45; //day displayed in the tabular
		$headerDay = "";
		$headerDayNum = "";
		$headerMonth = "";
		$lastMonth = "";
		$now = date("Y-m-d");
		$date = $year . '-' . $month . '-' . '1';
		if (date('m')-0 == $month && $firstDay == "true")
		{
		   $DateStart = new DateTime($now);
		   $DateEnd = new DateTime($now);
		}
		else
		{
		   $DateStart = new DateTime($date);
		   $DateEnd = new DateTime($date);
		}
		$DateEnd->modify('+ ' . $maxDay . ' day');
		while ($DateStart <= $DateEnd){
                    $dayNum = $DateStart->format('w');
		    $ii = $DateStart->format('d');
                    switch ($dayNum)
                    {
                        case 1: $headerDay .= "<td><b>" . lang('calendar_monday_short') . "</b></td>"; break;
                        case 2: $headerDay .= "<td><b>" . lang('calendar_tuesday_short') . "</b></td>"; break;
                        case 3: $headerDay .= "<td><b>" . lang('calendar_wednesday_short') . "</b></td>"; break;
                        case 4: $headerDay .= "<td><b>" . lang('calendar_thursday_short') . "</b></td>"; break;
                        case 5: $headerDay .= "<td><b>" . lang('calendar_friday_short') . "</b></td>"; break;
                        case 6: $headerDay .= "<td><b>" . lang('calendar_saturday_short') . "</b></td>"; break;
                        case 0: $headerDay .= "<td><b>" . lang('calendar_sunday_short') . "</b></td>"; break;
                    }
		    $test = $DateStart->format('Y-m-d');
		    if (in_array($test, $dayoffs)) //$dayoffs are dates in DayInfo Table, it can be scolare holiday
		    {
			if ($test == $now)
			$headerDayNum .= "<td style='border: solid; border-color: red; background-color: #5A5B78 !important; -webkit-print-color-adjust: exact'><b>" . $ii . "</b></td>";
			else
			$headerDayNum .= "<td  style='background-color: #5A5B78 !important; -webkit-print-color-adjust: exact'><b>" . $ii . "</b></td>";
		    }
		    else
		    {
			if ($test == $now)
			   $headerDayNum .= "<td style='border: solid; border-color: red'><b>" . $ii . "</b></td>";
			else
			   $headerDayNum .= "<td><b>" . $ii . "</b></td>";
		    }
		    if ($lastMonth != $DateStart->format('m')) //display month
		    {
			$lastMonth = $DateStart->format('m');
			$tmpLastDay = date("t", strtotime($DateStart->format('Y-m-d'))) - $DateStart->format('d') + 1;    //last day of selected month
			if ($tmpLastDay > $maxDay)
			   $tmpLastDay = $maxDay + 1;
			$headerMonth .= "<td colspan=" . $tmpLastDay . "><b>" . $listMonth[$lastMonth - 1] . "</b></td>";
		    }
		  $DateStart->modify('+1 day'); //Next day
		  $maxDay = $maxDay - 1;
		}
		?>
<table class="table table-bordered" style="border: solid" id="printTable" border="1" cellpadding="3">
    <thead>
	<tr>
	 <td>&nbsp;</td>
	 <?php echo $headerMonth; ?>
	</tr>
        <tr>
            <td>&nbsp;</td>
            <?php echo $headerDay; ?>
        </tr>
        <tr>
            <td><b><?php echo lang('calendar_tabular_thead_employee');?></b></td>
            <?php echo $headerDayNum; ?>
        </tr>
    </thead>
  <tbody>
  <?php
  $repeater = 0;
  $organisation = "";
  $display = "";
  foreach ($tabular as $employee) {?>
  	  <?php
	  if ($employee->org != $organisation) // Delimite services
    	     echo '<tr style="border-top: solid">';
	  else
	     echo '<tr>';
	  ?>
	<td><?php echo $employee->name; ?></td>
      <?php foreach ($employee->days as $day) {
          $overlapping = FALSE;
          if (strstr($day->display, ';')) {
              $periods = explode(";", $day->display);
              $statuses = explode(";", $day->status);
                switch (intval($statuses[0]))
                {
                    case 1: $class = "planned"; break;  // Planned
                    case 2: $class = "requested"; break;  // Requested
                    case 3: $class = "accepted"; break;  // Accepted
                    case 4: $class = "rejected"; break;  // Rejected
                    case 5: $class="dayoff"; break;
                    case 6: $class="dayoff"; break;
                }
                switch (intval($statuses[1]))
                {
                    case 1: $class .= "planned"; break;  // Planned
                    case 2: $class .= "requested"; break;  // Requested
                    case 3: $class .= "accepted"; break;  // Accepted
                    case 4: $class .= "rejected"; break;  // Rejected
                    case 5: $class .="dayoff"; break;
                    case 6: $class .="dayoff"; break;
                }
          } else {
            switch ($day->display) {
                case '0': $display = "<td title='$day->type' style='background-color: $day->color !important; -webkit-print-color-adjust: exact'>&nbsp;</td>"; break;
                case '4': $display = "<td title='$day->type' style='background-color: $day->color !important; -webkit-print-color-adjust: exact'>&nbsp;</td>"; break;
                case '5': $class="amdayoff"; break;
                case '6': $class="pmdayoff"; break;
                case '1': 
		     switch ($day->status) //toute la journée
                      {
                          case 1: $class = "amplanned"; break;  // Planned
			  case 2: $display = "<td title='$day->type' style='background: repeating-linear-gradient(45deg,#000000,#000000 5px,$day->color 5px,$day->color 10px)!important; -webkit-print-color-adjust: exact'>&nbsp;</td>"; break; // Requested
                          case 3: $display = "<td title='$day->type' style='background-color: $day->color !important; -webkit-print-color-adjust: exact'>&nbsp;</td>"; break;  // Accepted
                          case 4: $class = "amrejected"; break;  // Rejected
                      }
                    break;
                case '2':
                    switch ($day->status) //matin seulement
                      {
			  case 2: $display = "<td title='$day->type' style='background: repeating-linear-gradient(45deg,#000000,#000000 5px,$day->color 5px,$day->color 10px) !important;  border: 1px solid #ccc; padding: 0; width: 40px; height: 30px; -webkit-print-color-adjust: exact'><div style='background: -webkit-linear-gradient(-45deg, transparent 50%, #ffffff 50%) !important; height: 100%; width: 100%; margin: 0; padding: 0; -webkit-print-color-adjust: exact'></div></td>"; break; // Requested
			  case 3: $display = "<td title='$day->type' style='background: -webkit-linear-gradient(-45deg, $day->color 50%,#ffffff 50%) !important; -webkit-print-color-adjust: exact'>&nbsp;</td>"; break; // Accepted
                          case 4: $class = "amrejected"; break;  // Rejected
                      }
                    break;
                case '3':
                    switch ($day->status) //apres-midi seulement
                      {
                          case 1: $class = "pmplanned"; break;  // Planned
                          case 2: $display = "<td title='$day->type' style='background: repeating-linear-gradient(45deg,#000000,#000000 5px,$day->color 5px,$day->color 10px) !important; border: 1px solid #ccc; padding: 0; width: 80px; height: 35px; -webkit-print-color-adjust: exact'><div style='background: -webkit-linear-gradient(-45deg, #ffffff 50%, transparent 50%) !important; height: 100%; width: 100%; margin: 0; padding: 0; -webkit-print-color-adjust: exact'></div></td>"; break; // Requested
                          case 3: $display = "<td title='$day->type' style='background: -webkit-linear-gradient(-45deg, #ffffff 50%,$day->color 50%) !important; -webkit-print-color-adjust: exact'>&nbsp;</td>"; break; // Accepted
			  case 4: $class = "pmrejected"; break;  // Rejected
                      }
                    break;
            }
          }
          //Detect overlapping cases
          //switch ($class) {
            //        case "plannedplanned":
            //        case "requestedrequested":
            //        case "acceptedaccepted":
            //        case "rejectedrejected":
            //            $overlapping = TRUE;
            //  break;
         // }
	    echo $display;
	    $organisation = $employee->org;
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
        <a href="#" onclick="select_entity();" class="btn secondary"><?php echo lang('calendar_tabular_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn secondary"><?php echo lang('calendar_tabular_popup_entity_button_cancel');?></a>
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
    var firstDay = false;

    function select_entity() {
        entity = $('#organization').jstree('get_selected')[0];
        text = $('#organization').jstree().get_text(entity);
        $('#txtEntity').val(text);
        $("#frmSelectEntity").modal('hide');
    }

    $('#cmdPrint').click(function() {
     var printContents = document.getElementById("printTable").outerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;
     window.print();
     document.body.innerHTML = originalContents;
     window.location.reload();
        });

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

        $('#cmdNext').click(function() {
            if (entity != -1) {
                if ($('#chkIncludeChildren').prop('checked') == true) {
                    children = 'true';
                } else {
                    children = 'false';
                }
                month = $('#cboMonth').val();
                year = $('#cboYear').val(); 
		var tmpMonth = parseInt(month);
		var tmpYear = parseInt(year);
		if (tmpMonth == 12)
		{
		   tmpMonth = 1;
		   tmpYear++;
		}
		else
		{
		  tmpMonth++;
		}
		firstDay = false;
                url = '<?php echo base_url();?>calendar/tabular/' + entity + '/' + tmpMonth + '/' + tmpYear + '/' + children + '/' + firstDay;
                document.location.href = url;
            }
        });

	$('#cmdPrev').click(function() {
            if (entity != -1) {
                if ($('#chkIncludeChildren').prop('checked') == true) {
                    children = 'true';
                } else {
                    children = 'false';
                }
                month = $('#cboMonth').val();
                year = $('#cboYear').val(); 
		var tmpMonth = parseInt(month);
		var tmpYear = parseInt(year);
		if (tmpMonth == 1)
		{
		   tmpMonth = 12;
		   tmpYear--;
		}
		else
		{
		  tmpMonth--;
		}
		firstDay = false;
                url = '<?php echo base_url();?>calendar/tabular/' + entity + '/' + tmpMonth + '/' + tmpYear + '/' + children + '/' + firstDay;
                document.location.href = url;
            }
        });

	$('#cmdToday').click(function() {
            if (entity != -1) {
                if ($('#chkIncludeChildren').prop('checked') == true) {
                    children = 'true';
                } else {
                    children = 'false';
                }
		firstDay = false;
		var d = new Date();
		var m = d.getMonth() + 1;
		var y = d.getFullYear();
                url = '<?php echo base_url();?>calendar/tabular/' + entity + '/' + m+ '/' + y+ '/' + children + '/' + firstDay;
                document.location.href = url;
            }
        });

	$('#cmdExecute').click(function() {
            if (entity != -1) {
                if ($('#chkIncludeChildren').prop('checked') == true) {
                    children = 'true';
                } else {
                    children = 'false';
                }
                month = $('#cboMonth').val();
                year = $('#cboYear').val();
		firstDay = false;
                url = '<?php echo base_url();?>calendar/tabular/' + entity + '/' + month+ '/' + year+ '/' + children + '/' + firstDay;
                document.location.href = url;
            }
        });

        //Export the report into Excel
        $("#cmdExport").click(function() {
            if (entity != -1) {
                if ($('#chkIncludeChildren').prop('checked') == true) {
                    children = 'true';
                } else {
                    children = 'false';
                }
                month = $('#cboMonth').val();
                year = $('#cboYear').val();
            url = '<?php echo base_url(); ?>calendar/tabular/export/' + entity + '/'+ month + '/'+ year + '/'+ children;
            document.location.href  = url;
            }
        });

        //Load alert forms
        $("#frmSelectEntity").alert();
        //Prevent to load always the same content (refreshed each time)
        $('#frmSelectEntity').on('hidden', function() {
            $(this).removeData('modal');
        });
    });
</script>
