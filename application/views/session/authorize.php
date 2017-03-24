<?php 
/**
 * This view displays a simplified login form for OAtuh2 authorization.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.6.0
 */
?>
<link href="<?php echo base_url();?>assets/css/jorani-0.5.1.css" rel="stylesheet">
<style>
<?php //Font mapping with languages needing a better font than the default font
$fonts = $this->config->item('fonts');
if (!is_null($fonts)) {
    if (array_key_exists($language_code, $fonts)) { ?>
    @font-face {
      font-family: '<?php echo $fonts[$language_code]['name'];?>';
      src: url('<?php echo base_url(), 'assets/fonts/', $fonts[$language_code]['asset'];?>') format('truetype');
    }
    body, button, input, select, .ui-datepicker, .selectize-input {
        font-family: '<?php echo $fonts[$language_code]['name'];?>' !important;
    }
<?php 
        }
    } ?>
</style>

<div class="container">
    <div class="row">
        <div class="span12">
            <img width="100" src="<?php echo base_url();?>assets/images/logo_simple.png">
            &nbsp;<i class="icon-retweet"></i>&nbsp;
            <img width="100" src="<?php echo base_url();?>local/images/<?php echo $clientId;?>.png">
        </div>
    </div>
    <div class="row">
        <div class="span12">
            <h4>Do You Authorize <?php echo $clientId;?>?</h4>
            <button id="yes" class="btn btn-primary">Yes</button>
            <button id="no" class="btn btn-primary">No</button>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function () {
    $('#yes').click(function() {
        $.ajax({
            url: "<?php echo base_url();?>api/authorization/authorize",
            type: "POST",
            data: {
                client_id: "<?php echo $clientId;?>",
                response_type: "<?php echo $responseType;?>",
                state: "<?php echo $state;?>",
                authorized: "yes",
            },
            success: function(data){
                if (typeof JoraniOAuthAuthorizedCallback == 'function') {
                    JoraniOAuthAuthorizedCallback(data);
                } else {
                    alert("No OAuth2/Authorized callback function was defined");
                }
            }
        });
    });

    $('#no').click(function() {
        if (typeof JoraniOAuthCancelCallback == 'function') {
            JoraniOAuthCancelCallback();
        } else {
            alert("No OAuth2/Cancel callback function was defined");
        }
    });
});
</script>
