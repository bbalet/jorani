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

<style>
.allplanned {
    background-color: #999;
    color: #ffffff;
}

.allrequested {
    background-color: #f89406;
    color: #ffffff;
}

.allaccepted {
    background-color: #468847;
    color: #ffffff;
}

.allrejected {
    background-color: #ff0000;
    color: #ffffff;
}

.working {
    background-color: #ffffff;
    color: #0;
}

.dayoff {
    background-color: #000000;
    color: #ffffff;
}

.amplanned {
background: #999; /* Old browsers */
background: -moz-linear-gradient(-45deg, #999 50%, #ffffff 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#999), color-stop(50%,#ffffff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #999 50%,#ffffff 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #999 50%,#ffffff 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #999 50%,#ffffff 50%); /* IE10+ */
background: linear-gradient(135deg, #999 50%,#ffffff 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#999', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.amrequested {
background: #f89406; /* Old browsers */
background: -moz-linear-gradient(-45deg, #f89406 50%, #ffffff 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#f89406), color-stop(50%,#ffffff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #f89406 50%,#ffffff 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #f89406 50%,#ffffff 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #f89406 50%,#ffffff 50%); /* IE10+ */
background: linear-gradient(135deg, #f89406 50%,#ffffff 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f89406', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.amaccepted {
background: #468847; /* Old browsers */
background: -moz-linear-gradient(-45deg, #468847 50%, #ffffff 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#468847), color-stop(50%,#ffffff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #468847 50%,#ffffff 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #468847 50%,#ffffff 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #468847 50%,#ffffff 50%); /* IE10+ */
background: linear-gradient(135deg, #468847 50%,#ffffff 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#468847', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.amrejected {
background: #ff0000; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ff0000 50%, #ffffff 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ff0000), color-stop(50%,#ffffff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ff0000 50%,#ffffff 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ff0000 50%,#ffffff 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ff0000 50%,#ffffff 50%); /* IE10+ */
background: linear-gradient(135deg, #ff0000 50%,#ffffff 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff0000', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.pmplanned {
background: #999; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ffffff 50%, #999 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ffffff), color-stop(50%,#999)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ffffff 50%,#999 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ffffff 50%,#999 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ffffff 50%,#999 50%); /* IE10+ */
background: linear-gradient(135deg, #ffffff 50%,#999 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#999',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.pmrequested {
background: #f89406; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ffffff 50%, #f89406 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ffffff), color-stop(50%,#f89406)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ffffff 50%,#f89406 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ffffff 50%,#f89406 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ffffff 50%,#f89406 50%); /* IE10+ */
background: linear-gradient(135deg, #ffffff 50%,#f89406 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f89406',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.pmaccepted {
background: #468847; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ffffff 50%, #468847 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ffffff), color-stop(50%,#468847)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ffffff 50%,#468847 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ffffff 50%,#468847 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ffffff 50%,#468847 50%); /* IE10+ */
background: linear-gradient(135deg, #ffffff 50%,#468847 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#468847',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.pmrejected {
background: #ff0000; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ffffff 50%, #ff0000 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ffffff), color-stop(50%,#ff0000)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ffffff 50%,#ff0000 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ffffff 50%,#ff0000 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ffffff 50%,#ff0000 50%); /* IE10+ */
background: linear-gradient(135deg, #ffffff 50%,#ff0000 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ff0000',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

/*Overlapping*/
.plannedrequested {
background: #999; /* Old browsers */
background: -moz-linear-gradient(-45deg, #999 50%, #f89406 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#999), color-stop(50%,#f89406)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #999 50%,#f89406 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #999 50%,#f89406 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #999 50%,#f89406 50%); /* IE10+ */
background: linear-gradient(135deg, #999 50%,#f89406 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#999', endColorstr='#f89406',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.plannedaccepted {
background: #999; /* Old browsers */
background: -moz-linear-gradient(-45deg, #999 50%, #468847 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#999), color-stop(50%,#468847)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #999 50%,#468847 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #999 50%,#468847 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #999 50%,#468847 50%); /* IE10+ */
background: linear-gradient(135deg, #999 50%,#468847 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#999', endColorstr='#468847',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.plannedrejected {
background: #999; /* Old browsers */
background: -moz-linear-gradient(-45deg, #999 50%, #ff0000 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#999), color-stop(50%,#ff0000)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #999 50%,#ff0000 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #999 50%,#ff0000 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #999 50%,#ff0000 50%); /* IE10+ */
background: linear-gradient(135deg, #999 50%,#ff0000 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#999', endColorstr='#ff0000',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.requestedplanned {
background: #f89406; /* Old browsers */
background: -moz-linear-gradient(-45deg, #f89406 50%, #999 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#f89406), color-stop(50%,#999)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #f89406 50%,#999 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #f89406 50%,#999 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #f89406 50%,#999 50%); /* IE10+ */
background: linear-gradient(135deg, #f89406 50%,#999 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f89406', endColorstr='#999',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.requestedaccepted {
background: #f89406; /* Old browsers */
background: -moz-linear-gradient(-45deg, #f89406 50%, #468847 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#f89406), color-stop(50%,#468847)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #f89406 50%,#468847 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #f89406 50%,#468847 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #f89406 50%,#468847 50%); /* IE10+ */
background: linear-gradient(135deg, #f89406 50%,#468847 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f89406', endColorstr='#468847',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.requestedrejected {
background: #f89406; /* Old browsers */
background: -moz-linear-gradient(-45deg, #f89406 50%, #ff0000 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#f89406), color-stop(50%,#ff0000)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #f89406 50%,#ff0000 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #f89406 50%,#ff0000 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #f89406 50%,#ff0000 50%); /* IE10+ */
background: linear-gradient(135deg, #f89406 50%,#ff0000 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f89406', endColorstr='#ff0000',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.acceptedplanned {
background: #468847; /* Old browsers */
background: -moz-linear-gradient(-45deg, #468847 50%, #999 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#468847), color-stop(50%,#999)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #468847 50%,#999 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #468847 50%,#999 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #468847 50%,#999 50%); /* IE10+ */
background: linear-gradient(135deg, #468847 50%,#999 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#468847', endColorstr='#999',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.acceptedrequested {
background: #468847; /* Old browsers */
background: -moz-linear-gradient(-45deg, #468847 50%, #f89406 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#468847), color-stop(50%,#f89406)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #468847 50%,#f89406 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #468847 50%,#f89406 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #468847 50%,#f89406 50%); /* IE10+ */
background: linear-gradient(135deg, #468847 50%,#f89406 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#468847', endColorstr='#f89406',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.acceptedrejected {
background: #468847; /* Old browsers */
background: -moz-linear-gradient(-45deg, #468847 50%, #ff0000 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#468847), color-stop(50%,#ff0000)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #468847 50%,#ff0000 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #468847 50%,#ff0000 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #468847 50%,#ff0000 50%); /* IE10+ */
background: linear-gradient(135deg, #468847 50%,#ff0000 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#468847', endColorstr='#ff0000',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.rejectedplanned {
background: #ff0000; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ff0000 50%, #999 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ff0000), color-stop(50%,#999)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ff0000 50%,#999 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ff0000 50%,#999 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ff0000 50%,#999 50%); /* IE10+ */
background: linear-gradient(135deg, #ff0000 50%,#999 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff0000', endColorstr='#999',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.rejectedrequested {
background: #ff0000; /* Old browsers */
background: -moz-linear-gradient(-45deg, #ff0000 50%, #f89406 50%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right bottom, color-stop(50%,#ff0000), color-stop(50%,#f89406)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(-45deg, #ff0000 50%,#f89406 50%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(-45deg, #ff0000 50%,#f89406 50%); /* Opera 11.10+ */
background: -ms-linear-gradient(-45deg, #ff0000 50%,#f89406 50%); /* IE10+ */
background: linear-gradient(135deg, #ff0000 50%,#f89406 50%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff0000', endColorstr='#f89406',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}

.rejectedaccepted {
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
    

<h1><?php echo lang('calendar_tabular_title');?></h1>

<div class="row-fluid">
    <div class="span4">
        <label for="txtEntity"><?php echo lang('calendar_organization_field_select_entity');?></label>
        <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" value="<?php echo $department;?>" readonly />
        <button id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('calendar_tabular_button_select_entity');?></button>
        
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
            <?php for ($ii=date('Y', strtotime('-6 year')); $ii<date('Y', strtotime('+2 year'));$ii++) {
                if ($ii == $year) {
                    echo "<option val='" . $ii ."' selected>" . $ii ."</option>";
                } else {
                    echo "<option val='" . $ii ."'>" . $ii ."</option>";
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
        <!--<button id="cmdExport" class="btn btn-primary"><?php echo lang('calendar_tabular_button_export');?></button>//-->
    </div>
</div>

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
    
    function select_entity() {
        entity = $('#organization').jstree('get_selected')[0];
        text = $('#organization').jstree().get_text(entity);
        $('#txtEntity').val(text);
        $("#frmSelectEntity").modal('hide');
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

        $('#cmdExecute').click(function() {
            if (entity != -1) {
                if ($('#chkIncludeChildren').prop('checked') == true) {
                    children = 'true';
                } else {
                    children = 'false';
                }
                month = $('#cboMonth').val();
                year = $('#cboYear').val();
                url = '<?php echo base_url();?>calendar/tabular/' + entity + '/' + month+ '/' + year+ '/' + children;
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
