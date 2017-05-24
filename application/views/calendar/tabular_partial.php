<?php
/**
 * This partial view builds a "linear" calendar (which is technically a line into an HTML table).
 * A linear calendar displays the leaves of an employee during a month. Each cell is a day.
 * It can be loaded asynchronously.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.6.0
 */
?>

<?php if (count($tabular) > 0) {?>
<table class="table table-bordered">
    <thead>
        <tr>
            <td>&nbsp;</td>
            <?php
                $start = $year . '-' . $month . '-' . '1';    //first date of selected month
                $lastDay = date("t", strtotime($start));    //last day of selected month
                $isCurrentMonth = date('Y-n') === $year . '-' . (int)$month;
                $currentDay = (int)date('d');
                for ($ii = 1; $ii <=$lastDay; $ii++) {
                    $class = '';
                    if($isCurrentMonth && $ii === $currentDay){
                        $class .= ' currentday-bg';
                    }
                    $dayNum = date("N", strtotime($year . '-' . $month . '-' . $ii));
                    switch ($dayNum)
                    {
                        case 1: echo '<td'.($class?' class="'.$class.'"':'').'><b>' . lang('calendar_monday_short') . '</b></td>'; break;
                        case 2: echo '<td'.($class?' class="'.$class.'"':'').'><b>' . lang('calendar_tuesday_short') . '</b></td>'; break;
                        case 3: echo '<td'.($class?' class="'.$class.'"':'').'><b>' . lang('calendar_wednesday_short') . '</b></td>'; break;
                        case 4: echo '<td'.($class?' class="'.$class.'"':'').'><b>' . lang('calendar_thursday_short') . '</b></td>'; break;
                        case 5: echo '<td'.($class?' class="'.$class.'"':'').'><b>' . lang('calendar_friday_short') . '</b></td>'; break;
                        case 6: echo '<td'.($class?' class="'.$class.'"':'').'><b>' . lang('calendar_saturday_short') . '</b></td>'; break;
                        case 7: echo '<td'.($class?' class="'.$class.'"':'').'><b>' . lang('calendar_sunday_short') . '</b></td>'; break;
                    }
                }?>
        </tr>
        <tr>
            <td><b><?php echo lang('calendar_tabular_thead_employee');?></b></td>
            <?php
                $start = $year . '-' . $month . '-' . '1';    //first date of selected month
                $lastDay = date("t", strtotime($start));    //last day of selected month
                for ($ii = 1; $ii <=$lastDay; $ii++) {
                    $class = '';
                    if($isCurrentMonth && $ii === $currentDay){
                        $class .= ' currentday-bg';
                    }
                    echo '<td'.($class?' class="'.$class.'"':'').'><b>' . $ii . '</b></td>';
                }?>
        </tr>
    </thead>
  <tbody>
  <?php
  $repeater = 0;
  
  foreach ($tabular as $employee) {
      $dayIterator = 0;
      ?>
    <tr>
      <td><?php echo $employee->name; ?></td>
      <?php foreach ($employee->days as $day) {
          $dayIterator++;
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
                    case 5: $class = "dayoff"; break;
                    case 6: $class = "dayoff"; break;
                }
                switch (intval($statuses[0]))
                {
                    case 1: $class .= "planned"; break;  // Planned
                    case 2: $class .= "requested"; break;  // Requested
                    case 3: $class .= "accepted"; break;  // Accepted
                    case 4: $class .= "rejected"; break;  // Rejected
                    case 5: $class .= "dayoff"; break;
                    case 6: $class .= "dayoff"; break;
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
                        case 5: $class = "dayoff"; break;
                        case 6: $class = "dayoff"; break;
                    }
                }
          } else {
            switch ($day->display) {
                case '9': $class = "error"; break;
                case '0': $class = "working"; break;
                case '4': $class = "dayoff"; break;
                case '5': $class = "amdayoff"; break;
                case '6': $class = "pmdayoff"; break;
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
          
          // Current day class
          if($isCurrentMonth && $dayIterator === $currentDay){
              $class .= ' currentday-border';
          }
            if ($class == "error"){
                echo '<td><img src="'.  base_url() .'assets/images/date_error.png"></td>';
            } else {
                $dayType = "";
                if ($mode == 'public') {    //In public access, nobody is connected
                    $dayType = "";
                } else {
                    //Hide leave type to users who are not part of HR/Admin
                    if (($is_hr == TRUE) || 
                            ($is_admin == TRUE) || 
                            ($employee->manager == $user_id) || 
                            ($employee->id == $user_id)) {
                        $dayType = $day->type;
                    }
                }
                if ($overlapping) {
                    echo '<td title="' . $dayType . '" class="' . $class . '"><img src="' . base_url() . 'assets/images/date_error.png"></td>';
                } else {
                    echo '<td title="' . $dayType . '" class="' . $class . '">&nbsp;</td>';
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
