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
$this->lang->load('extra', $language);
$this->lang->load('global', $language);?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('extra_create_title');?>&nbsp;<?php echo $help;?></h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'frmCreateExtra');
echo form_open('extra/create', $attributes) ?>

    <label for="viz_date" required><?php echo lang('extra_create_field_date');?></label>
    <input type="text" name="viz_date" id="viz_date" value="<?php echo set_value('date'); ?>" />
    <input type="hidden" name="date" id="date" />
    
    <label for="duration" required><?php echo lang('extra_create_field_duration');?></label>
    <input type="text" name="duration" id="duration" value="<?php echo set_value('duration'); ?>" />&nbsp;<span><?php echo lang('extra_create_field_duration_description');?></span>
    
    <label for="cause"><?php echo lang('extra_create_field_cause');?></label>
    <textarea name="cause" id="cause"><?php echo set_value('cause'); ?></textarea>
    
    <label for="status" required><?php echo lang('extra_create_field_status');?></label>
    <select name="status">
        <option value="1" selected><?php echo lang('Planned');?></option>
        <option value="2"><?php echo lang('Requested');?></option>
    </select>
</form>

    <div class="row-fluid"><div class="span12">&nbsp;</div></div>
    <div class="row-fluid"><div class="span12">
        <button id="cmdCreateExtra" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp; <?php echo lang('extra_create_button_create');?></button>
        &nbsp;
        <a href="<?php echo base_url(); ?>extra" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp; <?php echo lang('extra_create_button_cancel');?></a>
    </div></div>
    <div class="row-fluid"><div class="span12">&nbsp;</div></div>
    </div>
</div>

    
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript">
    function validate_form() {
        result = false;
        var fieldname = "";
        if ($('#viz_date').val() == "") fieldname = "<?php echo lang('extra_create_field_date');?>";
        if ($('#duration').val() == "") fieldname = "<?php echo lang('extra_create_field_duration');?>";
        if ($('#cause').val() == "") fieldname = "<?php echo lang('extra_create_field_cause');?>";
        if (fieldname == "") {
            return true;
        } else {
            bootbox.alert(<?php echo lang('extra_create_mandatory_js_msg');?>);
            return false;
        }
    }
    
    $(function () {
        $("#viz_date").datepicker({
            changeMonth: true,
            changeYear: true,
            altFormat: "yy-mm-dd",
            altField: "#date"
        }, $.datepicker.regional['<?php echo $language_code;?>']);
        
        //Force decimal separator whatever the locale is
        $("#duration").keyup(function() {
            var value = $("#duration").val();
            value = value.replace(",", ".");
            $("#duration").val(value);
        });
        
        $("#cmdCreateExtra").click(function() {
            if (validate_form()) {
                $("#frmCreateExtra").submit();
            }
        });
    });
</script>
