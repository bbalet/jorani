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
$this->lang->load('leaves', $language);
$this->lang->load('global', $language);?>

<h2><?php echo lang('leaves_edit_title');?><?php echo $leave['id']; ?> <?php echo $help;?>&nbsp;<span class="muted">(<?php echo $name ?>)</span></h2>

<div class="row-fluid">
    <div class="span8">

<?php echo validation_errors(); ?>

<?php 
$attributes = array('id' => 'frmLeaveForm');
if (isset($_GET['source'])) {
    echo form_open('leaves/edit/' . $id . '?source=' . $_GET['source'], $attributes);
} else {
    echo form_open('leaves/edit/' . $id, $attributes);
} ?>

    <label for="viz_startdate" required><?php echo lang('leaves_edit_field_start');?></label>
    <input type="text" name="viz_startdate" id="viz_startdate" value="<?php $date = new DateTime($leave['startdate']); echo $date->format(lang('global_date_format'));?>" />
    <input type="hidden" name="startdate" id="startdate" value="<?php echo $leave['startdate'];?>" />
    <select name="startdatetype" id="startdatetype">
        <option value="Morning" <?php if ($leave['startdatetype'] == "Morning") {echo "selected";}?>><?php echo lang('leaves_date_type_morning');?></option>
        <option value="Afternoon" <?php if ($leave['startdatetype'] == "Afternoon") {echo "selected";}?>><?php echo lang('leaves_date_type_afternoon');?></option>
    </select><br />
    
    <label for="viz_enddate" required><?php echo lang('leaves_edit_field_end');?></label>
    <input type="text" name="viz_enddate" id="viz_enddate" value="<?php $date = new DateTime($leave['enddate']); echo $date->format(lang('global_date_format'));?>" />
    <input type="hidden" name="enddate" id="enddate" value="<?php echo $leave['enddate'];?>" />
    <select name="enddatetype" id="enddatetype">
        <option value="Morning" <?php if ($leave['enddatetype'] == "Morning") {echo "selected";}?>><?php echo lang('leaves_date_type_morning');?></option>
        <option value="Afternoon" <?php if ($leave['enddatetype'] == "Afternoon") {echo "selected";}?>><?php echo lang('leaves_date_type_afternoon');?></option>
    </select><br />
    
    <label for="type" required><?php echo lang('leaves_edit_field_type');?></label>
    <select name="type" id="type">
    <?php foreach ($types as $types_item): ?>
        <option value="<?php echo $types_item['id'] ?>" <?php if ($types_item['id'] == $leave['type']) echo "selected" ?>><?php echo $types_item['name'] ?></option>
    <?php endforeach ?>    
    </select>&nbsp;<span id="lblCredit"><?php if (!is_null($credit)) { ?>(<?php echo $credit; ?>)<?php } ?></span><br />
    
    <label for="duration" required><?php echo lang('leaves_edit_field_duration');?></label>
    <input type="text" name="duration" id="duration" value="<?php echo $leave['duration']; ?>" />
    
    <div class="alert hide alert-error" id="lblCreditAlert">
        <button type="button" class="close">&times;</button>
        <?php echo lang('leaves_edit_field_duration_message');?>
    </div>
    
    <div class="alert hide alert-error" id="lblOverlappingAlert" onclick="$('#lblOverlappingAlert').hide();">
        <button type="button" class="close">&times;</button>
        <?php echo lang('leaves_create_field_overlapping_message');?>
    </div>
    
    <label for="cause"><?php echo lang('leaves_edit_field_cause');?></label>
    <textarea name="cause"><?php echo $leave['cause']; ?></textarea>
    
    <label for="status" required><?php echo lang('leaves_edit_field_status');?></label>
    <select name="status">
        <option value="1" <?php if ($leave['status'] == 1) echo 'selected'; ?>><?php echo lang('Planned');?></option>
        <option value="2" <?php if ($leave['status'] == 2) echo 'selected'; ?>><?php echo lang('Requested');?></option>
        <?php if ($is_hr) {?>
        <option value="3" <?php if ($leave['status'] == 3) echo 'selected'; ?>><?php echo lang('Accepted');?></option>
        <option value="4" <?php if ($leave['status'] == 4) echo 'selected'; ?>><?php echo lang('Rejected');?></option>        
        <?php } ?>
    </select><br />

    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('leaves_edit_button_update');?></button>
    &nbsp;
    <?php if (isset($_GET['source'])) {?>
        <a href="<?php echo base_url() . $_GET['source']; ?>" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('leaves_edit_button_cancel');?></a>
    <?php } else {?>
        <a href="<?php echo base_url(); ?>leaves" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('leaves_edit_button_cancel');?></a>
    <?php } ?>
    
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
<script type="text/javascript">
    var baseURL = '<?php echo base_url();?>';
    var userId = <?php echo $leave['employee']; ?>;
    var leaveId = <?php echo $leave['id']; ?>;
    var languageCode = '<?php echo $language_code;?>';
    
    var noContractMsg = "<?php echo lang('leaves_validate_flash_msg_no_contract');?>";
    var noTwoPeriodsMsg = "<?php echo lang('leaves_validate_flash_msg_overlap_period');?>";
    
function validate_form() {
    result = false;
    var fieldname = "";
    if ($('#viz_startdate').val() == "") fieldname = "<?php echo lang('leaves_edit_field_start');?>";
    if ($('#viz_enddate').val() == "") fieldname = "<?php echo lang('leaves_edit_field_end');?>";
    if ($('#duration').val() == "") fieldname = "<?php echo lang('leaves_edit_field_duration');?>";
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
