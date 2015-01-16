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
$this->lang->load('status', $language);
$this->lang->load('global', $language);?>

<style>
table tbody td.allplanned {
    background-color: #999;
    color: #ffffff;
}

table tbody td.allrequested {
    background-color: #f89406;
    color: #ffffff;
}

table tbody td.allaccepted {
    background-color: #468847;
    color: #ffffff;
}

table tbody td.allrejected {
    background-color: #ff0000;
    color: #ffffff;
}

table tbody td.working {
    background-color: #ffffff;
    color: #0;
}

table tbody td.dayoff {
    background-color: #000000;
    color: #ffffff;
}

table tbody td.amplanned {
background: #999; /* Old browsers */
background: -moz-linear-gradient(-45deg, #999 50%, #ffffff 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#999), color-stop(50%,#ffffff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #999 50%,#ffffff 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #999 50%,#ffffff 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #999 50%,#ffffff 50%); /* IE10+ */
background: linear-gradient(135deg, #999 50%,#ffffff 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#999', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.amrequested {
background: #f89406; /* Old browsers */
background: -moz-linear-gradient(-45deg, #f89406 50%, #ffffff 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#f89406), color-stop(50%,#ffffff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #f89406 50%,#ffffff 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #f89406 50%,#ffffff 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #f89406 50%,#ffffff 50%); /* IE10+ */
background: linear-gradient(135deg, #f89406 50%,#ffffff 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f89406', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.amaccepted {
background: #468847; /* Old browsers */
background: -moz-linear-gradient(-45deg, #468847 50%, #ffffff 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#468847), color-stop(50%,#ffffff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #468847 50%,#ffffff 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #468847 50%,#ffffff 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #468847 50%,#ffffff 50%); /* IE10+ */
background: linear-gradient(135deg, #468847 50%,#ffffff 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#468847', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.amrejected {
background: #ff0000; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ff0000 50%, #ffffff 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ff0000), color-stop(50%,#ffffff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ff0000 50%,#ffffff 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ff0000 50%,#ffffff 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ff0000 50%,#ffffff 50%); /* IE10+ */
background: linear-gradient(135deg, #ff0000 50%,#ffffff 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff0000', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.pmplanned {
background: #999; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ffffff 50%, #999 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ffffff), color-stop(50%,#999)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ffffff 50%,#999 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ffffff 50%,#999 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ffffff 50%,#999 50%); /* IE10+ */
background: linear-gradient(135deg, #ffffff 50%,#999 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#999',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.pmrequested {
background: #f89406; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ffffff 50%, #f89406 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ffffff), color-stop(50%,#f89406)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ffffff 50%,#f89406 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ffffff 50%,#f89406 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ffffff 50%,#f89406 50%); /* IE10+ */
background: linear-gradient(135deg, #ffffff 50%,#f89406 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f89406',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.pmaccepted {
background: #468847; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ffffff 50%, #468847 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ffffff), color-stop(50%,#468847)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ffffff 50%,#468847 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ffffff 50%,#468847 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ffffff 50%,#468847 50%); /* IE10+ */
background: linear-gradient(135deg, #ffffff 50%,#468847 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#468847',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.pmrejected {
background: #ff0000; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ffffff 50%, #ff0000 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ffffff), color-stop(50%,#ff0000)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ffffff 50%,#ff0000 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ffffff 50%,#ff0000 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ffffff 50%,#ff0000 50%); /* IE10+ */
background: linear-gradient(135deg, #ffffff 50%,#ff0000 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ff0000',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

//-----------------------------------------------------------------
//Overlapping
table tbody td.plannedrequested {
background: #999; /* Old browsers */
background: -moz-linear-gradient(-45deg, #999 50%, #f89406 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#999), color-stop(50%,#f89406)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #999 50%,#f89406 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #999 50%,#f89406 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #999 50%,#f89406 50%); /* IE10+ */
background: linear-gradient(135deg, #999 50%,#f89406 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#999', endColorstr='#f89406',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.plannedaccepted {
background: #999; /* Old browsers */
background: -moz-linear-gradient(-45deg, #999 50%, #468847 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#999), color-stop(50%,#468847)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #999 50%,#468847 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #999 50%,#468847 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #999 50%,#468847 50%); /* IE10+ */
background: linear-gradient(135deg, #999 50%,#468847 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#999', endColorstr='#468847',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.plannedrejected {
background: #999; /* Old browsers */
background: -moz-linear-gradient(-45deg, #999 50%, #ff0000 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#999), color-stop(50%,#ff0000)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #999 50%,#ff0000 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #999 50%,#ff0000 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #999 50%,#ff0000 50%); /* IE10+ */
background: linear-gradient(135deg, #999 50%,#ff0000 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#999', endColorstr='#ff0000',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.requestedplanned {
background: #f89406; /* Old browsers */
background: -moz-linear-gradient(-45deg, #f89406 50%, #999 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#f89406), color-stop(50%,#999)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #f89406 50%,#999 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #f89406 50%,#999 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #f89406 50%,#999 50%); /* IE10+ */
background: linear-gradient(135deg, #f89406 50%,#999 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f89406', endColorstr='#999',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.requestedaccepted {
background: #f89406; /* Old browsers */
background: -moz-linear-gradient(-45deg, #f89406 50%, #468847 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#f89406), color-stop(50%,#468847)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #f89406 50%,#468847 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #f89406 50%,#468847 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #f89406 50%,#468847 50%); /* IE10+ */
background: linear-gradient(135deg, #f89406 50%,#468847 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f89406', endColorstr='#468847',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.requestedrejected {
background: #f89406; /* Old browsers */
background: -moz-linear-gradient(-45deg, #f89406 50%, #ff0000 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#f89406), color-stop(50%,#ff0000)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #f89406 50%,#ff0000 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #f89406 50%,#ff0000 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #f89406 50%,#ff0000 50%); /* IE10+ */
background: linear-gradient(135deg, #f89406 50%,#ff0000 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f89406', endColorstr='#ff0000',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

//Accepted - Planned
//Accepted - Requested 
//Accepted - Rejected 
table tbody td.acceptedplanned {
background: #468847; /* Old browsers */
background: -moz-linear-gradient(-45deg, #468847 50%, #999 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#468847), color-stop(50%,#999)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #468847 50%,#999 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #468847 50%,#999 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #468847 50%,#999 50%); /* IE10+ */
background: linear-gradient(135deg, #468847 50%,#999 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#468847', endColorstr='#999',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.acceptedrequested {
background: #468847; /* Old browsers */
background: -moz-linear-gradient(-45deg, #468847 50%, #f89406 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#468847), color-stop(50%,#f89406)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #468847 50%,#f89406 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #468847 50%,#f89406 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #468847 50%,#f89406 50%); /* IE10+ */
background: linear-gradient(135deg, #468847 50%,#f89406 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#468847', endColorstr='#f89406',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.acceptedrejected {
background: #468847; /* Old browsers */
background: -moz-linear-gradient(-45deg, #468847 50%, #ff0000 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#468847), color-stop(50%,#ff0000)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #468847 50%,#ff0000 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #468847 50%,#ff0000 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #468847 50%,#ff0000 50%); /* IE10+ */
background: linear-gradient(135deg, #468847 50%,#ff0000 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#468847', endColorstr='#ff0000',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

//Rejected - Planned
//Rejected - Requested 
//Rejected - Accepted 
table tbody td.rejectedplanned {
background: #ff0000; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ff0000 50%, #999 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ff0000), color-stop(50%,#999)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ff0000 50%,#999 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ff0000 50%,#999 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ff0000 50%,#999 50%); /* IE10+ */
background: linear-gradient(135deg, #ff0000 50%,#999 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff0000', endColorstr='#999',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.rejectedrequested {
background: #ff0000; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ff0000 50%, #f89406 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ff0000), color-stop(50%,#f89406)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ff0000 50%,#f89406 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ff0000 50%,#f89406 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ff0000 50%,#f89406 50%); /* IE10+ */
background: linear-gradient(135deg, #ff0000 50%,#f89406 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff0000', endColorstr='#f89406',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

table tbody td.rejectedaccepted {
background: #ff0000; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ff0000 50%, #468847 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ff0000), color-stop(50%,#468847)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ff0000 50%,#468847 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ff0000 50%,#468847 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ff0000 50%,#468847 50%); /* IE10+ */
background: linear-gradient(135deg, #ff0000 50%,#468847 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff0000', endColorstr='#468847',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

</style>
    

<h1>Test</h1>

<div class="row-fluid">
    <div class="span3"><span class="label"><?php echo lang('Planned');?></span></div>
    <div class="span3"><span class="label label-success"><?php echo lang('Accepted');?></span></div>
    <div class="span3"><span class="label label-warning"><?php echo lang('Requested');?></span></div>
    <div class="span3">&nbsp;</div>
</div>

<?php if (count($tabular) == 0) {
     echo lang('leaves_summary_tbody_empty');
} else {
?>
<table class="table table-bordered">
    <thead>
        <tr>
            <td><b>Employee</b></td>
            <?php
                $start = $year . '-' . $month . '1';    //first date of selected month
                $lastDay = date("t", strtotime($start));    //last day of selected month
                for ($ii = 1; $ii <=$lastDay; $ii++) {
                    echo '<td><b>' . $ii . '</b></td>';
                }?>
        </tr>
    </thead>
  <tbody>
  <?php
  $repeater = 0;
  foreach ($tabular as $employee) {?>
    <tr>
      <td><?php echo $employee->name; ?></td>
      <?php foreach ($employee->days as $day) {
          //Overlapping cases
          if (strstr($day->display, ';')) {
              $periods = explode(";", $day->display);
              $statuses = explode(";", $day->status);
                switch ($statuses[0])
                {
                    case 1: $class = "planned"; break;  // Planned
                    case 2: $class = "requested"; break;  // Requested
                    case 3: $class = "accepted"; break;  // Accepted
                    case 4: $class = "rejected"; break;  // Rejected
                }
                switch ($statuses[1])
                {
                    case 1: $class .= "planned"; break;  // Planned
                    case 2: $class .= "requested"; break;  // Requested
                    case 3: $class .= "accepted"; break;  // Accepted
                    case 4: $class .= "rejected"; break;  // Rejected
                }
          } else {
            switch ($day->display) {
                case '0': $class="working"; break;
                case '4': $class="dayoff"; break;
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
          
            echo '<td title="' . $day->type . '" class="' . $class . '">'  . '</td>';?>
    <?php } ?>
          </tr>
    <?php      
    if ($repeater++>10) {
        $repeater = 0;?>
    <tr>
        <td><b>Employee</b></td>
        <?php for ($ii = 1; $ii <=$lastDay + 1; $ii++) echo '<td><b>' . $ii . '</b></td>';?>
    </tr>
    <?php }
    }?>
  </tbody>
</table>
<?php } ?>

<button id="cmdExport" class="btn btn-primary"><?php echo lang('calendar_organization_button_select_entity');?></button>

<script type="text/javascript">
    $(document).ready(function() {
        entity = <?php echo $entity;?>;
        month = <?php echo $month;?>;
        year = <?php echo $year;?>;
        children = '<?php echo $children;?>';
        
        //Export the report into Excel
        $("#cmdExport").click(function() {
            url = '<?php echo base_url(); ?>calendar/tabular/export/' + entity + '/'+ month + '/'+ year + '/'+ children;
            location.href = url;
        });
</script>
