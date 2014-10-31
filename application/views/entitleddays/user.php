<?php
/*
 * This file is part of lms.
 *
 * lms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('entitleddays', $language);
$this->lang->load('global', $language);?>

<h2><?php echo lang('entitleddays_user_index_title');?> <span class="muted"><?php echo $name; ?></span></h2>

<table class="table table-bordered table-hover" id="entitleddaysuser">
<thead>
    <tr>
      <th>&nbsp;</th>
      <th><?php echo lang('entitleddays_user_index_thead_start');?></th>
      <th><?php echo lang('entitleddays_user_index_thead_end');?></th>
      <th><?php echo lang('entitleddays_user_index_thead_days');?></th>
      <th><?php echo lang('entitleddays_user_index_thead_type');?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($entitleddays as $days) { ?>
    <tr data-id="<?php echo $days['id'] ?>">
      <td><a href="#" onclick="delete_entitleddays(<?php echo $days['id'] ?>);" title="<?php echo lang('entitleddays_user_index_thead_tip_delete');?>"><i class="icon-remove"></i></a></td>
      <td><?php 
$date = new DateTime($days['startdate']);
echo $date->format(lang('global_date_format'));
?></td>
      <td><?php 
$date = new DateTime($days['enddate']);
echo $date->format(lang('global_date_format'));
?></td>
      <td><span id="days<?php echo $days['id'] ?>"><?php echo $days['days']; ?></span> &nbsp; <a href="#" onclick="Javascript:incdec(<?php echo $days['id'] ?>, 'decrease');"><i class="icon-minus"></i></a>
             &nbsp; <a href="#" onclick="Javascript:incdec(<?php echo $days['id'] ?>, 'increase');"><i class="icon-plus"></i></a></td>
      <td><?php echo $days['type']; ?></td>
    </tr>
  <?php } ?>
  <?php if (count($entitleddays) == 0) { ?>
    <tr id="noentitleddays">
        <td colspan="5"><?php echo lang('entitleddays_user_index_no_data');?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>

<label for="viz_startdate"><?php echo lang('entitleddays_user_index_field_start');?></label>
<input type="text" id="viz_startdate" name="viz_startdate" required /><br />
<input type="hidden" name="startdate" id="startdate" />
<label for="viz_enddate"><?php echo lang('entitleddays_user_index_field_end');?></label>
<input type="text" id="viz_enddate" name="viz_enddate" required /><br />
<input type="hidden" name="enddate" id="enddate" />
<label for="type"><?php echo lang('entitleddays_user_index_field_type');?></label>
<select name="type" id="type" required>
<?php foreach ($types as $types_item): ?>
    <option value="<?php echo $types_item['id'] ?>" <?php if ($types_item['id'] == 1) echo "selected" ?>><?php echo $types_item['name'] ?></option>
<?php endforeach ?> 
</select>    
<label for="days" required><?php echo lang('entitleddays_user_index_field_days');?></label>
<input type="input" name="days" id="days" />
<button id="cmdAddEntitledDays" class="btn btn-primary" onclick="add_entitleddays();"><?php echo lang('entitleddays_user_index_button_add');?></button>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui-1.10.4.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript">
    
    function validate_form() {
        result = false;
        var fieldname = "";
        if ($('#startdate').val() == "") fieldname = "<?php echo lang('entitleddays_user_index_field_start');?>";
        if ($('#enddate').val() == "") fieldname = "<?php echo lang('entitleddays_user_index_field_end');?>";
        if ($('#type').val() == "") fieldname = "<?php echo lang('entitleddays_user_index_field_type');?>";
        if ($('#days').val() == "") fieldname = "<?php echo lang('entitleddays_user_index_field_days');?>";
        if (fieldname == "") {
            return true;
        } else {
            bootbox.alert(<?php echo lang('entitleddays_user_mandatory_js_msg');?>);
            return false;
        }
    }
    
    function delete_entitleddays(id) {
        $.ajax({
            url: "<?php echo base_url();?>entitleddays/userdelete/" + id
          }).done(function() {
              $('tr[data-id="' + id + '"]').remove();
              var rowCount = $('#entitleddaysuser tbody tr').length;
              if (rowCount == 0) {
                  $('#entitleddaysuser > tbody:last').append('<tr id="noentitleddays"><td colspan="5"><?php echo lang('entitleddays_user_index_no_data');?></td></tr>');
              }
          });
    }
    
    //"increase" or "decrease" the number of entitled days of a given row
    function incdec(id, operation) {
        $.ajax({
            url: "<?php echo base_url();?>entitleddays/ajax/incdec",
                            type: "POST",
                data: { id: id,
                        operation: operation
                    }
          }).done(function() {
              var days = parseInt($('#days' + id).text());
              switch(operation) {
                  case "increase": days++; $('#days' + id).text(days.toFixed(2)); break;
                  case "decrease": days--; $('#days' + id).text(days.toFixed(2)); break;
              }
          });
    }
    
    function add_entitleddays() {
        if (validate_form()) {
            $.ajax({
                url: "<?php echo base_url();?>entitleddays/ajax/user",
                type: "POST",
                data: { user_id: <?php echo $id; ?>,
                        startdate: $('#startdate').val(),
                        enddate: $('#enddate').val(),
                        days: $('#days').val(),
                        type: $('#type').val()
                    }
              }).done(function( msg ) {
                  id = parseInt(msg);
                  days = parseFloat($('#days').val());
                  $('#noentitleddays').remove();
                  myRow = '<tr data-id="' + id + '">' +
                            '<td><a href="#" onclick="delete_entitleddays(' + id + ');" title="<?php echo lang('entitleddays_user_index_thead_tip_delete');?>"><i class="icon-remove"></i></a></td>' +
                            '<td>' + $('#viz_startdate').val() + '</td>' +
                            '<td>' + $('#viz_enddate').val() + '</td>' +
                            '<td><span id="days' + id + '">' + days + '</span> &nbsp; ' +
                            '<a href="#" onclick="Javascript:incdec(' + id + ', \'decrease\');"><i class="icon-minus"></i></a>' +
                            '&nbsp; <a href="#" onclick="Javascript:incdec(' + id + ', \'increase\');"><i class="icon-plus"></i></a></td>' +
                            '<td>' + $('#type option:selected').text() + '</td>' +
                        '</tr>';
                  $('#entitleddaysuser > tbody:last').append(myRow);
            });
        }
    }
    
    $(function () {
        $("#viz_startdate").datepicker({
            changeMonth: true,
            changeYear: true,
            altFormat: "yy-mm-dd",
            altField: "#startdate",
            numberOfMonths: 3,
                  onClose: function( selectedDate ) {
                    $( "#viz_enddate" ).datepicker( "option", "minDate", selectedDate );
                  }
        }, $.datepicker.regional['<?php echo $language_code;?>']);
        $("#viz_enddate").datepicker({
            changeMonth: true,
            changeYear: true,
            altFormat: "yy-mm-dd",
            altField: "#enddate",
            numberOfMonths: 3,
                  onClose: function( selectedDate ) {
                    $( "#viz_startdate" ).datepicker( "option", "maxDate", selectedDate );
                  }
        }, $.datepicker.regional['<?php echo $language_code;?>']);
        
        //Force decimal separator whatever the locale is
        $( "#days" ).keyup(function() {
            var value = $("#days").val();
            value = value.replace(",", ".");
            $("#days").val(value);
        });
    });
</script>
