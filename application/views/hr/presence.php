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
$this->lang->load('hr', $language);
$this->lang->load('calendar', $language);
$this->lang->load('datatable', $language);
$this->lang->load('global', $language);?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('hr_presence_title');?><span class="muted">&nbsp;(<?php echo $employee_name;?>&nbsp; |&nbsp;<?php echo lang($month_name);?>&nbsp;<?php echo $year;?>)</span> &nbsp;<?php echo $help;?></h2>

<p><?php echo lang('hr_presence_description');?></p>

<div class="row-fluid">
     <div class="span6">
        <div id='calendar'></div>
    </div>
    <div class="span3">
        <br /><br />
        <p><?php echo lang('hr_presence_employee');?> : <b><?php echo $employee_name;?></b></p>
        <p><?php echo lang('hr_presence_month');?> : <b><?php echo lang($month_name);?>&nbsp;<?php echo $year;?></b></p>
        <p><?php echo lang('hr_presence_days');?> : <b><?php echo $total_days;?></b></p>
        <?php if ($contract_id != '') { ?>
        <p><?php echo lang('hr_presence_contract');?> : <b><a href="<?php echo base_url();?>contracts/<?php echo $contract_id; ?>"><?php echo $contract_name;?></a></b></p>
        <p><?php echo lang('hr_presence_working_days');?> : <b><?php echo $opened_days;?></b></p>
        <p><?php echo lang('hr_presence_non_working_days');?> : <b><a href="<?php echo base_url();?>contracts/<?php echo $contract_id; ?>/calendar"><?php echo $non_working_days;?></a></b></p>
        <?php } else { ?>
        <p><?php echo lang('hr_presence_contract');?> : <i class="icon-warning-sign"></i><?php echo lang('hr_presence_no_contract');?></p>
        <p><?php echo lang('hr_presence_working_days');?> : <i class="icon-warning-sign"></i><?php echo lang('hr_presence_no_contract');?></p>
        <p><?php echo lang('hr_presence_non_working_days');?> : <i class="icon-warning-sign"></i><?php echo lang('hr_presence_no_contract');?></p>
        <?php } ?>
        <p><?php echo lang('hr_presence_leave_duration');?> : <b><?php echo $leave_duration;?></b>&nbsp;<i class="icon-hand-left"></i><?php echo lang('hr_presence_please_check');?></p>
        <p><?php echo lang('hr_presence_work_duration');?> : <b><?php echo $work_duration;?></b>&nbsp;<i class="icon-hand-left"></i><?php echo lang('hr_presence_please_check');?></p>
    </div>
    <div class="span3">
        <br /><br />
	<?php $listMonth = array(lang('January'), lang('February'), lang('March'), lang('April'), lang('May'), lang('June'), lang('July'), lang('August'), lang('September'), lang('October'), lang('November'), lang('December')); ?>
        <label for="cboMonth"><?php echo lang('calendar_tabular_field_month');?></label>
        <select name="cboMonth" id="cboMonth">
            <?php for ($ii=1; $ii<13;$ii++) {
                if ($ii == $month) {
                    echo "<option value='" . $ii ."' selected>" . $ii ."</option>";
                } else {
                    echo "<option value='" . $ii ."'>" . $ii ."</option>";
                }
            }?>
        </select>
        <label for="cboYear"><?php echo lang('calendar_tabular_field_year');?></label>
        <select name="cboYear" id="cboYear">
            <?php for ($ii=date('Y', strtotime('-6 year')); $ii<= date('Y'); $ii++) {
                if ($ii == $year) {
                    echo "<option value='" . $ii ."' selected>" . $ii ."</option>";
                } else {
                    echo "<option value='" . $ii ."'>" . $ii ."</option>";
                }
            }?>
        </select><br />
        <button id="cmdExecute" class="btn btn-primary"><?php echo lang('hr_presence_button_execute');?></button>
     </div>
</div>

<hr />

<h3><?php echo lang('hr_presence_leaves_list_title');?></h3>

<div class="row-fluid">
    <div class="span12">
<?php
	$maxDay = 45;
	$headerDay = "";
	$headerDayNum = "";
	$headerMonth = "";
	$lastMonth = "";
	$now = date("Y-m-d");
	$date = $year . '-' . $month . '-' . '1';
	$DateStart = new DateTime($date);
	$DateEnd = new DateTime($date);
	$DateEnd->modify('+ ' . $maxDay . ' day');													                	        while ($DateStart <= $DateEnd){																	                $dayNum = $DateStart->format('w');
	  $ii = $DateStart->format('d');
	  switch ($dayNum)																				   {
	  	 case 1: $headerDay .= "<td><b>" . lang('calendar_monday_short') . "</b></td>"; break;
		 case 2: $headerDay .= "<td><b>" . lang('calendar_tuesday_short') . "</b></td>"; break;
		 case 3: $headerDay .= "<td><b>" . lang('calendar_wednesday_short') . "</b></td>"; break;
		 case 4: $headerDay .= "<td><b>" . lang('calendar_thursday_short') . "</b></td>"; break;
		 case 5: $headerDay .= "<td><b>" . lang('calendar_friday_short') . "</b></td>"; break;
		 case 6: $headerDay .= "<td><b>" . lang('calendar_saturday_short') . "</b></td>"; break;
		 case 0: $headerDay .= "<td><b>" . lang('calendar_sunday_short') . "</b></td>"; break;
	   }
	$test = $DateStart->format('Y-m-d');
	if (in_array($test, $dayoffs))
	   {
		$headerDayNum .= "<td bgcolor = '#5A5B78'><b>" . $ii . "</b></td>";
	   }
	else
	   {
		$headerDayNum .= "<td><b>" . $ii . "</b></td>";
	   }
	   if ($lastMonth != $DateStart->format('m'))
	      {
		$lastMonth = $DateStart->format('m');
		$tmpLastDay = date("t", strtotime($DateStart->format('Y-m-d'))) - $DateStart->format('d') + 1;    //last day of selected month
		if ($tmpLastDay > $maxDay)
		   $tmpLastDay = $maxDay;
		$headerMonth .= "<td colspan=" . $tmpLastDay . "><b>" . $listMonth[$lastMonth - 1] . "</b></td>";
	      }
	$DateStart->modify('+1 day'); //Next day
	$maxDay = $maxDay - 1;
	}
	?>
        <table class="table table-bordered">
    <thead>
	<tr>
		<?php
		echo $headerMonth
		?>
	</tr>
        <tr>
            <?php
		echo $headerDay;    
            ?>
        </tr>
        <tr>
            <?php
		echo $headerDayNum;    
            ?>
        </tr>
    </thead>
  <tbody>
    <tr>
      <?php foreach ($linear->days as $day) {
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
                          case 3: $day->color =  ";background: -webkit-linear-gradient(-45deg, $day->color 50%,#ffffff 50%)"; break;  // Accepted
                          case 4: $class = "amrejected"; break;  // Rejected
                      }
                    break;
                case '3':
                    switch ($day->status)
                      {
                          case 1: $class = "pmplanned"; break;  // Planned
                          case 2: $class = "pmrequested"; break;  // Requested
                          case 3: $day->color = ";background: -webkit-linear-gradient(-45deg, #ffffff 50%,$day->color 50%)"; break;  // Accepted
                          case 4: $class = "pmrejected"; break;  // Rejected
                      }
                    break;
            }
          }
          //Detect overlapping cases
          switch ($class) {
                    case "plannedplanned":
                    case "requestedrequested":
                    case "acceptedaccepted":
                    case "rejectedrejected":
                        $overlapping = TRUE;
              break;
          }
            if ($overlapping) {
                echo '<td title="' . $day->type . '" class="' . $class . '"><img src="' . base_url() . 'assets/images/date_error.png"></td>';
            } else {
                echo '<td title="' . $day->type . '" style="background-color: ' . $day->color . '">&nbsp;</td>';
            }
     } ?>
          </tr>
  </tbody>
</table>
    </div>
</div>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="leaves" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('hr_presence_thead_id');?></th>
            <th><?php echo lang('hr_presence_thead_start');?></th>
            <th><?php echo lang('hr_presence_thead_end');?></th>            
            <th><?php echo lang('hr_presence_thead_duration');?></th>
            <th><?php echo lang('hr_presence_thead_type');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($leaves as $leave):
    $date = new DateTime($leave['startdate']);
    $tmpStartDate = $date->getTimestamp();
    $startdate = $date->format(lang('global_date_format'));
    $date = new DateTime($leave['enddate']);
    $tmpEndDate = $date->getTimestamp();
    $enddate = $date->format(lang('global_date_format'));?>
    <tr>
        <td data-order="<?php echo $leave['id']; ?>">
            <a href="<?php echo base_url();?>leaves/edit/<?php echo $leave['id']; ?>?source=hr%2Fpresence%2F<?php echo $user_id; ?>" title="<?php echo lang('hr_presence_thead_tip_edit');?>"><?php echo $leave['id'] ?></a>
        </td>
        <td data-order="<?php echo $tmpStartDate; ?>"><?php echo $startdate . ' (' . lang($leave['startdatetype']). ')'; ?></td>
        <td data-order="<?php echo $tmpEndDate; ?>"><?php echo $enddate . ' (' . lang($leave['enddatetype']) . ')'; ?></td>
        <td><?php echo $leave['duration']; ?></td>
        <td><?php echo $leave['type']; ?></td>
    </tr>
<?php endforeach ?>
	</tbody>
</table>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div class="row-fluid">
    <div class="span12">
      <a href="<?php echo base_url();?>hr/employees" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('hr_presence_button_list');?></a>
    </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

    </div>
</div>

<link href="<?php echo base_url();?>assets/fullcalendar/fullcalendar.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/lib/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/fullcalendar/lang/<?php echo $language_code;?>.js"></script>
<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
var employee = <?php echo $user_id;?>;
            
function refresh_calendar() {
    source = '<?php echo base_url();?>leaves/individual/' + employee;
    $('#calendar').fullCalendar('removeEvents');
    $('#calendar').fullCalendar('addEventSource', source);
    $('#calendar').fullCalendar('removeEventSource', source);
    source = '<?php echo base_url();?>contracts/calendar/userdayoffs/' + employee;
    $('#calendar').fullCalendar('removeEventSource', source);
    $('#calendar').fullCalendar('addEventSource', source);
    $('#calendar').fullCalendar('rerenderEvents');
    $('#calendar').fullCalendar('removeEventSource', source);
}
    
$(function () {
    
    //Transform the HTML table in a fancy datatable
    var oTable = $('#leaves').dataTable({
                    "order": [[ 1, "asc" ]],
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
        
        //Load a tiny calendar
        $('#calendar').fullCalendar({
            height: 300,
            defaultDate: moment('<?php echo $default_date;?>'),
            header: {
                left: "",
                center: "",
                right: ""
            },
            loading: function(isLoading) {
                if (isLoading) { //Display/Hide a pop-up showing an animated icon during the Ajax query.
                    $('#frmModalAjaxWait').modal('show');
                } else {
                    $('#frmModalAjaxWait').modal('hide');
                }    
            }
        });
        
        //Execute the report for another date
        $('#cmdExecute').click(function() {
            var month = $('#cboMonth').val();
            var year = $('#cboYear').val();
            var url = '<?php echo base_url();?>hr/presence/' + employee + '/' + month+ '/' + year;
            document.location.href = url;
        });
        
        refresh_calendar();
});
</script>

