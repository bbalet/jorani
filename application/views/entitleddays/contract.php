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
$this->lang->load('entitleddays', $language);
$this->lang->load('datatable', $language);
$this->lang->load('global', $language);?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('entitleddays_contract_index_title');?> <span class="muted"><?php echo $contract_name; ?></span>&nbsp;<?php echo $help;?></h2>

<table id="entitleddayscontract">
<thead>
    <tr>
      <th>&nbsp;</th>
      <th><?php echo lang('entitleddays_contract_index_thead_start');?></th>
      <th><?php echo lang('entitleddays_contract_index_thead_end');?></th>
      <th><?php echo lang('entitleddays_contract_index_thead_days');?></th>
      <th><?php echo lang('entitleddays_contract_index_thead_type');?></th>
      <th><?php echo lang('entitleddays_contract_index_thead_description');?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($entitleddays as $days) { ?>
    <tr data-id="<?php echo $days['id']; ?>">
      <td><a href="#" onclick="delete_entitleddays(<?php echo $days['id'] ?>);" title="<?php echo lang('entitleddays_contract_index_thead_tip_delete');?>"><i class="icon-remove"></i></a></td>
<?php $startDate = new DateTime($days['startdate']);
$endDate = new DateTime($days['enddate']);?>
      <td data-order="<?php echo $startDate->getTimestamp(); ?>"><?php echo $startDate->format(lang('global_date_format'));?></td>
      <td data-order="<?php echo $endDate->getTimestamp(); ?>"><?php echo $endDate->format(lang('global_date_format'));?></td>
      <td data-order="<?php echo $days['days']; ?>"><span id="days<?php echo $days['id']; ?>" class="credit"><?php echo $days['days']; ?></span> &nbsp; <a href="#" onclick="Javascript:incdec(<?php echo $days['id']; ?>, 'decrease');"><i class="icon-minus"></i></a>
                     &nbsp; <a href="#" onclick="Javascript:incdec(<?php echo $days['id']; ?>, 'increase');"><i class="icon-plus"></i></a></td>
      <td><?php echo $days['type']; ?></td>
      <td><?php echo $days['description']; ?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>

<div id="frmAddEntitledDays" class="modal hide fade">
        <div class="modal-header">
            <a href="#" class="close" onclick="$('#frmAddEntitledDays').modal('hide');">&times;</a>
            <h3><?php echo lang('entitleddays_contract_popup_title');?></h3>
        </div>
        <div class="modal-body">
            <label for="viz_startdate"><?php echo lang('entitleddays_contract_index_field_start');?></label>
            <div class="input-append">
                <input type="text" id="viz_startdate" name="viz_startdate" required />
                <button class="btn" onclick="set_current_period();"><?php echo lang('entitleddays_contract_index_button_current');?></button>
            </div><br />
            <input type="hidden" name="startdate" id="startdate" />
            <label for="viz_enddate"><?php echo lang('entitleddays_contract_index_field_end');?></label>
            <input type="text" id="viz_enddate" name="viz_enddate" required /><br />
            <input type="hidden" name="enddate" id="enddate" />
            <label for="type"><?php echo lang('entitleddays_contract_index_field_type');?></label>
            <select name="type" id="type" required>
            <?php foreach ($types as $types_item): ?>
                <option value="<?php echo $types_item['id'] ?>" <?php if ($types_item['id'] == 1) echo "selected" ?>><?php echo $types_item['name'] ?></option>
            <?php endforeach ?> 
            </select>    
            <label for="days" required><?php echo lang('entitleddays_contract_index_field_days');?></label>
            <input type="text" class="input-mini" name="days" id="days" />
            <label for="description"><?php echo lang('entitleddays_contract_index_field_description');?></label>
            <input type="text" class="input-xlarge" name="description" id="description" />
        </div>
        <div class="modal-footer">
            <button id="cmdAddEntitledDays" class="btn btn-primary" onclick="add_entitleddays();"><?php echo lang('entitleddays_contract_index_button_add');?></button>
            <button id="cmdAddEntitledDays" class="btn btn-danger" onclick="$('#frmAddEntitledDays').modal('hide');"><?php echo lang('entitleddays_contract_index_button_cancel');?></button>
        </div>
 </div>
    
<div class="row-fluid"><div class="span12">&nbsp;</div></div>
<a href="<?php echo base_url();?>contracts" class="btn btn-danger"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('entitleddays_contract_index_button_back');?></a>
<button id="cmdAddEntitledDays" class="btn btn-primary" onclick="$('#frmAddEntitledDays').modal('show');"><i class="icon-plus-sign icon-white"></i>&nbsp;<?php echo lang('entitleddays_contract_index_button_add');?></button>
<div class="row-fluid"><div class="span12">&nbsp;</div></div>

    </div>
</div>


<div class="modal hide" id="frmModalAjaxWait" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <h1><?php echo lang('global_msg_wait');?></h1>
        </div>
        <div class="modal-body">
            <img src="<?php echo base_url();?>assets/images/loading.gif"  align="middle">
        </div>
 </div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js"></script>

<script type="text/javascript">
    //Current cell transformed in input box
    var current_input = null;
    var credit = 0;
    var startMonth = <?php echo $contract_start_month;?>;
    var startDay = <?php echo $contract_start_day;?>;
    var endMonth = <?php echo $contract_end_month;?>;
    var endDay = <?php echo $contract_end_day;?>;
    var locale = '<?php echo $language_code;?>';
    var oTable;     //datatable
    
    function set_current_period() {
        var now = moment();
        var startEntDate = moment();//now
        var endEntDate = moment();//now

        //Compute boundaries
        startEntDate.month(startMonth - 1);
        startEntDate.date(startDay);
        endEntDate.month(endMonth - 1);
        endEntDate.date(endDay);
        if (startMonth != 1 ) {
                if (now.month() < 5) {//zero-based => june
                        startEntDate.subtract(1, 'years');
                } else {
                        endEntDate.add(1, 'years');
                }
        }

        //Presentation for DB and Human
        startEntDate.locale(locale);
        endEntDate.locale(locale);
        $("#startdate").val(startEntDate.format("YYYY-MM-DD"));
        $("#enddate").val(endEntDate.format("YYYY-MM-DD"));
        $("#viz_startdate").val(startEntDate.format("L"));
        $("#viz_enddate").val(endEntDate.format("L"));
        }
    
    function validate_form() {
        result = false;
        var fieldname = "";
        if ($('#startdate').val() == "") fieldname = "<?php echo lang('entitleddays_contract_index_field_start');?>";
        if ($('#enddate').val() == "") fieldname = "<?php echo lang('entitleddays_contract_index_field_end');?>";
        if ($('#type').val() == "") fieldname = "<?php echo lang('entitleddays_contract_index_field_type');?>";
        if ($('#days').val() == "") fieldname = "<?php echo lang('entitleddays_contract_index_field_days');?>";
        if (fieldname == "") {
            return true;
        } else {
            bootbox.alert(<?php echo lang('entitleddays_contract_mandatory_js_msg');?>);
            return false;
        }
    }

    function delete_entitleddays(id) {
        bootbox.confirm("<?php echo lang('entitleddays_contract_confirm_delete_message');?>",
            "<?php echo lang('entitleddays_contract_confirm_delete_cancel');?>",
            "<?php echo lang('entitleddays_contract_confirm_delete_yes');?>", function(result) {
            if (result) {
                $('#frmModalAjaxWait').modal('show');
                $.ajax({
                    url: "<?php echo base_url();?>entitleddays/contractdelete/" + id
                  }).done(function() {
                      oTable.rows('tr[data-id="' + id + '"]').remove().draw();
                      $('#frmModalAjaxWait').modal('hide');
                  });
                }
        });
    }

    //"increase" or "decrease" the number of entitled days of a given row
    function incdec(id, operation) {
        $('#frmModalAjaxWait').modal('show');
        text2td();
        $.ajax({
            url: "<?php echo base_url();?>entitleddays/ajax/incdec",
                            type: "POST",
                data: { id: id,
                        operation: operation
                    }
          }).done(function() {
              var days = parseFloat($('#days' + id).text());
              switch(operation) {
                  case "increase": days++; $('#days' + id).text(days.toFixed(2)); break;
                  case "decrease": days--; $('#days' + id).text(days.toFixed(2)); break;
              }
              $('#frmModalAjaxWait').modal('hide');
          });
    }

    function add_entitleddays() {
        $('#frmAddEntitledDays').modal('hide');
        $('#frmModalAjaxWait').modal('show');
        if (validate_form()) {
            $.ajax({
                url: "<?php echo base_url();?>entitleddays/ajax/contract",
                type: "POST",
                data: { contract_id: <?php echo $id; ?>,
                        startdate: $('#startdate').val(),
                        enddate: $('#enddate').val(),
                        days: $('#days').val(),
                        type: $('#type').val(),
                        description: $("#description").val()
                    }
              }).done(function( msg ) {
                  id = parseInt(msg);
                  days = parseFloat($('#days').val());
                  htmlRow = '<tr data-id="' + id + '">' +
                            '<td><a href="#" onclick="delete_entitleddays(' + id + ');" title="<?php echo lang('entitleddays_contract_index_thead_tip_delete');?>"><i class="icon-remove"></i></a></td>' +
                            '<td data-order="' + moment.utc($('#startdate').val(), "YYYY-MM-DD") + '">' + $('#viz_startdate').val() + '</td>' +
                            '<td data-order="' + moment.utc($('#enddate').val(), "YYYY-MM-DD") + '">' + $('#viz_enddate').val() + '</td>' +
                            '<td data-order="' + days.toFixed(2) + '"><span id="days' + id + '" class="credit">' + days.toFixed(2) + '</span> &nbsp; ' +
                            '<a href="#" onclick="Javascript:incdec(' + id + ', \'decrease\');"><i class="icon-minus"></i></a>' +
                            '&nbsp; <a href="#" onclick="Javascript:incdec(' + id + ', \'increase\');"><i class="icon-plus"></i></a></td>' +
                            '<td>' + $('#type option:selected').text() + '</td>' +
                            '<td>' + $('#description').val() + '</td>' +
                        '</tr>';
                    objRow=$(htmlRow);
                    oTable.row.add(objRow).draw();
                  $('#frmModalAjaxWait').modal('hide');
            });
        }
    }
    
    //Make an Ajax call to the backend so as to save entitled days amount
    function saveInputByAjax(e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
            $('#frmModalAjaxWait').modal('show');
            if ($("#txtCredit").val() == "") $("#txtCredit").val("0");
            credit = parseFloat($("#txtCredit").val());
            $.ajax({
                url: "<?php echo base_url();?>entitleddays/ajax/incdec",
                                type: "POST",
                    data: { id: $("#txtCredit").closest( "tr" ).data( "id" ),
                                operation: "credit",
                                days: $("#txtCredit").val()
                        }
              }).done(function() {
                  $('#frmModalAjaxWait').modal('hide');
                  text2td();
              });
        } else {
            //Force decimal separator whatever the locale is
            var value = $("#txtCredit").val();
            value = value.replace(",", ".");
            $("#txtCredit").val(value);
        }
    }
    
    //Change back an input text box into the former readonly HTML element (e.g. SPAN or TD)
    function text2td() {
        if (current_input != null) {
           $("txtCredit").off('keyup');
           current_input.html(credit.toFixed(2));
           $(".credit").on('click', spanClick);
           current_input = null;
       }
    }
    
    //Change a SPAN element into an input text box
    function spanClick(){
        var days = parseFloat($(this).text());
        $(this).html("<input type='text' id='txtCredit' class='input-small' value='" + days + "'/>");
        text2td();
        current_input = $(this);
        credit = days;
        $(".credit").off('click');
        $("#txtCredit").on('keyup', saveInputByAjax);       //If enter key, save by ajax, otherwise force decimal
        $("#txtCredit").focus();
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
        
        $("body").on("keyup", function(e){
            if (e.keyCode == 27) {
                if ($('#frmAddEntitledDays').hasClass('in')) {
                    $('#frmAddEntitledDays').modal('hide');
                } else {
                    text2td();
                }
            }
        });
        
        //Transform the HTML table in a fancy datatable
        oTable = $('#entitleddayscontract').DataTable({
                    "order": [[ 1, "desc" ]],
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
                },
                    "drawCallback": function() {
                        //Transform a text label into an editable text field, algo
                        //- On ESC or on click, transform all existing text field back to text label.
                        //- onclick on a TD with .credit class transform it into a text field
                        //- Handle dynamic update of credit field
                        $(".credit").on('click', spanClick);
                    }
            });
    });
</script>
