<?php
/**
 * This view allows to create a new telework campaign
 * @copyright  Copyright (c) 2021 Maithyly SIVAPALAN
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<h2><?php echo lang('telework_campaign_create_title');?></h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'frmTeleworkCampaignForm');
echo form_open('teleworkcampaigns/create', $attributes); ?>

    <label for="name"><?php echo lang('telework_campaign_create_field_name');?></label>
    <input type="text" name="name" id="name" value="<?php echo set_value('name'); ?>" /><br />

    <label for="startdate"><?php echo lang('telework_campaign_create_field_startdate');?></label>
    <input type="text" name="startdate" id="startdate" value="<?php echo set_value('startdate'); ?>" autocomplete="off" />

    <label for="enddate"><?php echo lang('telework_campaign_create_field_enddate');?></label>
    <input type="text" name="enddate" id="enddate" value="<?php echo set_value('enddate'); ?>" autocomplete="off" />
    
    <br />
    <button id="send" class="btn btn-primary"><i class="mdi mdi-check"></i>&nbsp;<?php echo lang('telework_campaign_create_button_create');?></button>
    &nbsp;
    <a href="<?php echo base_url(); ?>teleworkcampaigns" class="btn btn-danger"><i class="mdi mdi-close"></i>&nbsp;<?php echo lang('telework_campaign_create_button_cancel');?></a>
</form>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>

<script type="text/javascript">
    var baseURL = '<?php echo base_url();?>';
    var campaignId = null;
    var languageCode = '<?php echo $language_code;?>';
    var dateJsFormat = '<?php echo lang('global_date_js_format');?>';

    function validate_form() {
        var fieldname = "";

		if ($('#enddate').val() == "") fieldname = "<?php echo lang('telework_campaign_create_field_enddate');?>";
        if ($('#startdate').val() == "") fieldname = "<?php echo lang('telework_campaign_create_field_startdate');?>";        
        if ($('#name').val() == "") fieldname = "<?php echo lang('telework_campaign_create_field_name');?>";
        if (fieldname == "") {
            return true;
        } else {
            bootbox.alert(<?php echo lang('telework_campaign_mandatory_js_msg');?>);
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
<script type="text/javascript" src="<?php echo base_url();?>assets/js/lms/campaign-telework.edit-0.7.0.js" type="text/javascript"></script>