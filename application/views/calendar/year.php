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
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */
?>
<link href="<?php echo base_url();?>assets/css/tabular.css" rel="stylesheet">
<h1><?php echo lang('calendar_year_title');?>&nbsp;<span class="muted">(<?php echo $employee_name;?>)</span>&nbsp;<?php echo $help;?></h1>

<div class="row-fluid">
    <div class="span4">
        <span class="label"><?php echo lang('Planned');?></span>&nbsp;
        <span class="label label-success"><?php echo lang('Accepted');?></span>&nbsp;
        <span class="label label-warning"><?php echo lang('Requested');?></span>&nbsp;
        <span class="label label-important" style="background-color: #ff0000;"><?php echo lang('Rejected');?></span>
    </div>
    <div class="span4">
        <a href="<?php echo base_url();?>calendar/year/export/<?php echo $user_id;?>/<?php echo ($year);?>" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp;<?php echo lang('calendar_year_button_export');?></a>
    </div>
    <div class="span4">
        <div class="pull-right">
            <a href="<?php echo base_url();?>calendar/year/<?php echo $user_id;?>/<?php echo ($year - 1);?>" class="btn btn-primary"><i class="icon-chevron-left icon-white"></i></a>
            <?php echo $year;?>
            <a href="<?php echo base_url();?>calendar/year/<?php echo $user_id;?>/<?php echo ($year + 1);?>" class="btn btn-primary"><i class="icon-chevron-right icon-white"></i></a>
        </div>
    </div>
</div>

<div class="row-fluid">

</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div class="row-fluid">
    <div class="span12">
<table class="table table-bordered table-condensed">
    <thead>
        <tr>
            <td>&nbsp;</td>
            <?php for ($ii = 1; $ii <=31; $ii++) {
                    echo '<td>' . $ii . '</td>';
                }?>
        </tr>
    </thead>
  <tbody>
  <?php foreach ($months as $month_name => $month) { ?>
    <tr>
      <td rowspan="2"><?php echo $month_name; ?></td>
        <?php //Iterate so as to display all mornings
        $pad_day = 1;
        foreach ($month->days as $day) {
            if (strstr($day->display, ';')) {//Two statuses in the cell
                $periods = explode(";", $day->display);
                $statuses = explode(";", $day->status);
                $types = explode(";", $day->type);
                $display = $periods[0];
                $status = $statuses[0];
                $type = $types[0];
            } else {
                $display = $day->display;
                $status = $day->status;
                $type = $day->type;
            }
                //0 - Working day  _
                //1 - All day           []
                //2 - Morning        |\
                //3 - Afternoon      /|
                //4 - All Day Off       []
                //5 - Morning Day Off   |\
                //6 - Afternoon Day Off /|
            if ($display == 0) echo '<td>&nbsp;</td>';
            if ($display == 3 || $display == 6) echo '<td>&nbsp;</td>';
            if ($display == 4 || $display == 5) echo '<td title="' . $type .'" class="dayoff">&nbsp;</td>';
            if ($display == 1 || $display == 2) {
                switch ($status)
                {
                  case 1: echo '<td title="' . $type .'" class="allplanned">&nbsp;</td>'; break;  // Planned
                  case 2: echo '<td title="' . $type .'" class="allrequested">&nbsp;</td>'; break;  // Requested
                  case 3: echo '<td title="' . $type .'" class="allaccepted">&nbsp;</td>'; break;  // Accepted
                  case 4: echo '<td title="' . $type .'" class="allrejected">&nbsp;</td>'; break;  // Rejected
                }
            }
        $pad_day++;
        } ?>
      <?php //Fill 
      if ($pad_day <= 31) echo '<td colspan="' . (32 - $pad_day) . '" rowspan="2" style="background-color:#00FFFF;">&nbsp;</td>';
        ?>
    </tr>
    <tr>
        <?php //Iterate so as to display all afternoons
        foreach ($month->days as $day) {
            if (strstr($day->display, ';')) {//Two statuses in the cell
                $periods = explode(";", $day->display);
                $statuses = explode(";", $day->status);
                $types = explode(";", $day->type);
                $display = $periods[1];
                $status = $statuses[1];
                $type = $types[1];
            } else {
                $display = $day->display;
                $status = $day->status;
                $type = $day->type;
            }
            if ($display == 0) echo '<td>&nbsp;</td>';
            if ($display == 2 || $display == 5) echo '<td>&nbsp;</td>';
            if ($display == 4 || $display == 6) echo '<td title="' . $type .'" class="dayoff">&nbsp;</td>';
            if ($display == 1 || $display == 3) {
                switch ($status)
                {
                  case 1: echo '<td title="' . $type .'" class="allplanned">&nbsp;</td>'; break;  // Planned
                  case 2: echo '<td title="' . $type .'" class="allrequested">&nbsp;</td>'; break;  // Requested
                  case 3: echo '<td title="' . $type .'" class="allaccepted">&nbsp;</td>'; break;  // Accepted
                  case 4: echo '<td title="' . $type .'" class="allrejected">&nbsp;</td>'; break;  // Rejected
                }
            }
      } ?>
    </tr>
  <?php } ?>
  </tbody>
</table>
        
    </div>
</div>
