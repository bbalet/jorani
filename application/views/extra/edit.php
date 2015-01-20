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
$this->lang->load('status', $language);
$this->lang->load('global', $language);?>

<h2><?php echo lang('extra_edit_title');?><?php echo $extra['id']; ?>&nbsp;<span class="muted">(<?php echo $name ?>)</span></h2>

<?php echo validation_errors(); ?>

<?php if (isset($_GET['source'])) {
    echo form_open('extra/edit/' . $id . '?source=' . $_GET['source']);
} else {
    echo form_open('extra/edit/' . $id);
} ?>

    <label for="viz_date"><?php echo lang('extra_edit_field_date');?></label>
    <input type="input" name="viz_date" id="viz_date" value="<?php $date = new DateTime($extra['date']); echo $date->format(lang('global_date_format'));?>" required />
    <input type="hidden" name="date" id="date" value="<?php echo $extra['date']; ?>" />
    
    <label for="duration"><?php echo lang('extra_edit_field_duration');?></label>
    <input type="input" name="duration" id="duration" value="<?php echo $extra['duration']; ?>" required />&nbsp;<span><?php echo lang('extra_edit_field_duration_description');?></span>
    
    <label for="cause"><?php echo lang('extra_edit_field_cause');?></label>
    <textarea name="cause" required><?php echo $extra['cause']; ?></textarea>
    
    <label for="status"><?php echo lang('extra_edit_field_status');?></label>
    <select name="status" required>
        <option value="1" <?php if ($extra['status'] == 1) echo 'selected'; ?>><?php echo lang('Planned');?></option>
        <option value="2" <?php if ($extra['status'] == 2) echo 'selected'; ?>><?php echo lang('Requested');?></option>
        <?php if ($is_hr) {?>
        <option value="3" <?php if ($extra['status'] == 3) echo 'selected'; ?>><?php echo lang('Accepted');?></option>
        <option value="4" <?php if ($extra['status'] == 4) echo 'selected'; ?>><?php echo lang('Rejected');?></option>        
        <?php } ?>
    </select><br />

    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('extra_edit_button_update');?></button>
    &nbsp;
    <?php if (isset($_GET['source'])) {?>
        <a href="<?php echo base_url() . $_GET['source']; ?>" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('extra_edit_button_cancel');?></a>
    <?php } else {?>
        <a href="<?php echo base_url(); ?>extra" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('extra_edit_button_cancel');?></a>
    <?php } ?>
</form>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui-1.10.4.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript">
    $(function () {
        $("#viz_date").datepicker({
            changeMonth: true,
            changeYear: true,
            altFormat: "yy-mm-dd",
            altField: "#date"
        }, $.datepicker.regional['<?php echo $language_code;?>']);
        
        //Force decimal separator whatever the locale is
        $( "#duration" ).keyup(function() {
            var value = $("#duration").val();
            value = value.replace(",", ".");
            $("#duration").val(value);
        });
    });
</script>
