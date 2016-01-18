<?php
/**
 * This view allows an employees (or HR admin/Manager) to create a new leave request
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<h2><?php echo lang('leaves_create_title');?> &nbsp;<?php echo $help;?></h2>

<div class="row-fluid">
    <div class="span8">

<?php echo validation_errors(); ?>

<?php 
$attributes = array('id' => 'frmLeaveForm');
echo form_open('leaves/create', $attributes) ?>

    <label for="type"><?php echo lang('leaves_create_field_type');?></label>
    <select name="type" id="type">
    <?php
    $default_type = $this->config->item('default_leave_type');
    $default_type = $default_type == FALSE ? 0 : $default_type;
    foreach ($types as $types_item): ?>
        <option value="<?php echo $types_item['id'] ?>" <?php if ($types_item['id'] == $default_type) echo "selected" ?>><?php echo $types_item['name'] ?></option>
    <?php endforeach ?>
    </select>&nbsp;<span id="lblCredit"><?php if (!is_null($credit)) { ?>(<?php echo $credit; ?>)<?php } ?></span><br />
        
    <label for="viz_startdate"><?php echo lang('leaves_create_field_start');?></label>
    <input type="text" name="viz_startdate" id="viz_startdate" value="<?php echo set_value('startdate'); ?>" autocomplete="off" />
    <input type="hidden" name="startdate" id="startdate" />
    <select name="startdatetype" id="startdatetype">
        <option value="Morning" selected><?php echo lang('Morning');?></option>
        <option value="Afternoon"><?php echo lang('Afternoon');?></option>
    </select><br />
    
    <label for="viz_enddate"><?php echo lang('leaves_create_field_end');?></label>
    <input type="text" name="viz_enddate" id="viz_enddate" value="<?php echo set_value('enddate'); ?>" autocomplete="off" />
    <input type="hidden" name="enddate" id="enddate" />
    <select name="enddatetype" id="enddatetype">
        <option value="Morning"><?php echo lang('Morning');?></option>
        <option value="Afternoon" selected><?php echo lang('Afternoon');?></option>
    </select><br />

    <label for="duration"><?php echo lang('leaves_create_field_duration');?></label>
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
    
    <div class="alert hide alert-error" id="lblOverlappingDayOffAlert" onclick="$('#lblOverlappingDayOffAlert').hide();">
        <button type="button" class="close">&times;</button>
        <?php echo lang('leaves_flash_msg_overlap_dayoff');?>
    </div>
    
    <label for="cause"><?php echo lang('leaves_create_field_cause');?></label>
    <textarea name="cause"><?php echo set_value('cause'); ?></textarea>
    
    <label for="status"><?php echo lang('leaves_create_field_status');?></label>
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
        <div class="row-fluid">
            <div class="span12">
                <span id="spnDayType"></span>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <span id="spnDaysOffList"></span>
            </div>
        </div>
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
    var dateJsFormat = '<?php echo lang('global_date_js_format');?>';
    var dateMomentJsFormat = '<?php echo lang('global_date_momentjs_format');?>';
    
    var noContractMsg = "<?php echo lang('leaves_validate_flash_msg_no_contract');?>";
    var noTwoPeriodsMsg = "<?php echo lang('leaves_validate_flash_msg_overlap_period');?>";
    
    var overlappingWithDayOff = "<?php echo lang('leaves_flash_msg_overlap_dayoff');?>";
    var listOfDaysOffTitle = "<?php echo lang('leaves_flash_spn_list_days_off');?>";
    
function validate_form() {
    var fieldname = "";
    
    //Call custom trigger defined into local/triggers/leave.js
    if (typeof triggerValidateCreateForm == 'function') { 
       if (triggerValidateCreateForm() == false) return false;
    }
    
    if ($('#viz_startdate').val() == "") fieldname = "<?php echo lang('leaves_create_field_start');?>";
    if ($('#viz_enddate').val() == "") fieldname = "<?php echo lang('leaves_create_field_end');?>";
    if ($('#duration').val() == "" || $('#duration').val() == 0) fieldname = "<?php echo lang('leaves_create_field_duration');?>";
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
