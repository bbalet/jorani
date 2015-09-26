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

<h2><?php echo lang('leaves_create_title');?> &nbsp;<?php echo $help;?></h2>

<div class="row-fluid">
    <div class="span8">

<?php echo validation_errors(); ?>

<?php 
$attributes = array('id' => 'frmLeaveForm');
echo form_open('leaves/create', $attributes) ?>

    <label for="type" required><?php echo lang('leaves_create_field_type');?></label>
    <select name="type" id="type">
    <?php
    $default_type = $this->config->item('default_leave_type');
    $default_type = $default_type == FALSE ? 0 : $default_type;
    foreach ($types as $types_item): ?>
        <option value="<?php echo $types_item['id'] ?>" <?php if ($types_item['id'] == $default_type) echo "selected" ?>><?php echo $types_item['name'] ?></option>
    <?php endforeach ?>
    </select>&nbsp;<span id="lblCredit"><?php if (!is_null($credit)) { ?>(<?php echo $credit; ?>)<?php } ?></span><br />
        
    <label for="viz_startdate" required><?php echo lang('leaves_create_field_start');?></label>
    <input type="text" name="viz_startdate" id="viz_startdate" value="<?php echo set_value('startdate'); ?>" />
    <input type="hidden" name="startdate" id="startdate" />
    <select name="startdatetype" id="startdatetype">
        <option value="Morning" selected><?php echo lang('leaves_date_type_morning');?></option>
        <option value="Afternoon"><?php echo lang('leaves_date_type_afternoon');?></option>
    </select><br />
    
    <label for="viz_enddate" required><?php echo lang('leaves_create_field_end');?></label>
    <input type="text" name="viz_enddate" id="viz_enddate" value="<?php echo set_value('enddate'); ?>" />
    <input type="hidden" name="enddate" id="enddate" />
    <select name="enddatetype" id="enddatetype">
        <option value="Morning"><?php echo lang('leaves_date_type_morning');?></option>
        <option value="Afternoon" selected><?php echo lang('leaves_date_type_afternoon');?></option>
    </select><br />

    <label for="duration" required><?php echo lang('leaves_create_field_duration');?></label>
    <?php if ($this->config->item('disable_edit_leave_duration') == TRUE) { ?>
    <input type="text" name="duration" id="duration" value="<?php echo set_value('duration'); ?>" readonly />
    <?php } else { ?>
    <input type="text" name="duration" id="duration" value="<?php echo set_value('duration'); ?>" />
    <?php } ?>
    
    <div class="alert hide alert-error" id="lblCreditAlert" onclick="$('#lblCreditAlert').hide();">
        <button type="button" class="close">&times;</button>
        <?php echo lang('leaves_create_field_duration_message');?>
    </div>
    
    <div class="alert hide alert-error" id="lblOverlappingAlert" onclick="$('#lblOverlappingAlert').hide();">
        <button type="button" class="close">&times;</button>
        <?php echo lang('leaves_create_field_overlapping_message');?>
    </div>
    
    <label for="cause"><?php echo lang('leaves_create_field_cause');?></label>
    <textarea name="cause"><?php echo set_value('cause'); ?></textarea>
    
    <label for="status" required><?php echo lang('leaves_create_field_status');?></label>
    <select name="status">
        
        <option value="1" <?php if ($this->config->item('leave_status_requested') == FALSE) echo 'selected'; ?>><?php echo lang('Planned');?></option>
        <option value="2" <?php if ($this->config->item('leave_status_requested') == TRUE) echo 'selected'; ?>><?php echo lang('Requested');?></option>
    </select><br />

    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp; <?php echo lang('leaves_create_button_create');?></button>
    &nbsp;
    <a href="<?php echo base_url(); ?>leaves" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp; <?php echo lang('leaves_create_button_cancel');?></a>
</form>

    </div>
    <div class="span4">
        <span id="spnDayOff">&nbsp;</span>
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
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>

<?php require_once dirname(BASEPATH) . "/local/triggers/leave_view.php"; ?>

<script type="text/javascript">
    var baseURL = '<?php echo base_url();?>';
    var userId = <?php echo $user_id; ?>;
    var leaveId = null;
    var languageCode = '<?php echo $language_code;?>';
    
    var noContractMsg = "<?php echo lang('leaves_validate_flash_msg_no_contract');?>";
    var noTwoPeriodsMsg = "<?php echo lang('leaves_validate_flash_msg_overlap_period');?>";
    
function validate_form() {
    var fieldname = "";
    
    //Call custom trigger defined into local/triggers/leave.js
    if (typeof triggerValidateCreateForm == 'function') { 
       if (triggerValidateCreateForm() == false) return false;
    }
    
    if ($('#viz_startdate').val() == "") fieldname = "<?php echo lang('leaves_create_field_start');?>";
    if ($('#viz_enddate').val() == "") fieldname = "<?php echo lang('leaves_create_field_end');?>";
    if ($('#duration').val() == "") fieldname = "<?php echo lang('leaves_create_field_duration');?>";
    if (fieldname == "") {
        return true;
    } else {
        bootbox.alert(<?php echo lang('leaves_validate_mandatory_js_msg');?>);
        return false;
    }
}

<?php if ($this->config->item('csrf_protection') == TRUE) {?>
$(function () {
    $.ajaxSetup({
        data: {
            <?php echo $this->security->get_csrf_token_name();?>: "<?php echo $this->security->get_csrf_hash();?>",
        }
    });
});
<?php }?>
</script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/lms/leave.edit.js" type="text/javascript"></script>
