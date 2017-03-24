<?php 
/**
 * This view displays sso failure message. Its layout is the same than the login form.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.5.0
 */
?>

<style>
    body {
        background-image:url('<?php echo base_url();?>assets/images/login-background.jpg');
        background-size: 100% 100%;
        background-repeat: no-repeat;
    }
    
    .vertical-center {
        min-height: 90%;  /* Fallback for browsers that do NOT support vh unit */
        min-height: 90vh;
        display: flex;
        align-items: center;
      }
      
      .form-box {
        padding: 20px;
        border: 1px #e4e4e4 solid;
        border-radius: 4px;
        box-shadow: 0 0 6px #ccc;
        background-color: #fff;
      }
</style>

    <div class="row vertical-center">
        <div class="span3">&nbsp;</div>
            <div class="span6 form-box">
                <div class="row-fluid">
                    <div class="span6">
<h2><?php echo lang('session_login_title');?><?php echo $help;?></h2>

    <?php $languages = $this->polyglot->nativelanguages($this->config->item('languages'));?>
    <input type="hidden" name="last_page" value="session/failure" />
    <?php if (count($languages) == 1) { ?>
    <input type="hidden" name="language" value="<?php echo $language_code; ?>" />
    <?php } else { ?>
    <label for="language"><?php echo lang('session_login_field_language');?></label>
    <select class="input-medium" name="language" id="language">
        <?php foreach ($languages as $lang_code => $lang_name) { ?>
        <option value="<?php echo $lang_code; ?>" <?php if ($language_code == $lang_code) echo 'selected'; ?>><?php echo $lang_name; ?></option>
        <?php }?>
    </select>
    <?php } ?>
    <h4><?php echo $message;?></h4>
    <br />
    <a href="<?php echo base_url();?>api/sso" class="btn btn-primary"><i class="icon-user icon-white"></i>&nbsp;<?php echo lang('session_login_button_login');?></a>
    <br /><br />
                </div>
                <div class="span6" style="height:100%;">
                    <div class="row-fluid">
                        <div class="span12">
                            <img src="<?php echo base_url();?>assets/images/logo_simple.png">
                        </div>
                    </div>
                    <div class="row-fluid"><div class="span12">&nbsp;</div></div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span style="font-size: 250%; font-weight: bold; line-height: 100%;"><center><?php echo lang('Leave Management System');?></center></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="span3">&nbsp;</div>
    </div>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/selectize.bootstrap2.css" />
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.pers-brow.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/selectize.min.js"></script>
<script type="text/javascript">
    $(function () {
        //Refresh page language
        $('#language').selectize({
            onChange: function (value) {
                if (value != '') {
                    $.cookie('language', $('#language option:selected').val(), { expires: 90, path: '/'});
                    window.location.href = '<?php echo base_url();?>session/language?language=' + value;
                }
            }
        });
        
    });
</script>
