<?php
/**
 * This view displays the profile (basic information) of the connected user.
 * If ICS feed is activated, a link allows the user to import non-working days into a remote calendar application.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.3.0
 */
?>

<h2><?php echo lang('users_myprofile_title');?></h2>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_firstname');?></strong></div>
    <div class="span3"><?php echo $user['firstname'];?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_lastname');?></strong></div>
    <div class="span3"><?php echo $user['lastname'];?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_manager');?></strong></div>
    <div class="span3"><?php echo $manager_label;?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_contract');?></strong></div>
    <div class="span3"><?php echo $contract_label;?>
    <?php if (($this->config->item('ics_enabled') == TRUE) && ($contract_id != 0)) {?>
    &nbsp;(<a id="lnkICS" href="#"><i class="icon-globe"></i> ICS</a>)</div>
    <?php } else {?>
    </div>
    <?php }?>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_position');?></strong></div>
    <div class="span3"><?php echo $position_label;?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_entity');?></strong></div>
    <div class="span3"><?php echo $organization_label;?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_hired');?></strong></div>
    <div class="span3"><?php 
$date = new DateTime($user['datehired']);
echo $date->format(lang('global_date_format'));
?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span3"><strong><?php echo lang('users_myprofile_field_identifier');?></strong></div>
    <div class="span3"><?php echo $user['identifier'];?></div>
    <div class="span6">&nbsp;</div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div id="frmLinkICS" class="modal hide fade">
    <div class="modal-header">
        <h3>ICS<a href="#" onclick="$('#frmLinkICS').modal('hide');" class="close">&times;</a></h3>
    </div>
    <div class="modal-body" id="frmSelectDelegateBody">
        <div class='input-append'>
                <input type="text" class="input-xlarge" id="txtIcsUrl" onfocus="this.select();" onmouseup="return false;" 
                    value="<?php echo base_url() . 'ics/dayoffs/' . $user_id . '/' . $contract_id;?>" />
                 <button id="cmdCopy" class="btn" data-clipboard-text="<?php echo base_url() . 'ics/dayoffs/' . $user_id . '/' . $contract_id;?>">
                     <i class="fa fa-clipboard"></i>
                 </button>
                <a href="#" id="tipCopied" data-toggle="tooltip" title="<?php echo lang('copied');?>" data-placement="right" data-container="#cmdCopy"></a>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmLinkICS').modal('hide');" class="btn btn-primary"><?php echo lang('OK');?></a>
    </div>
</div>

<script src="<?php echo base_url();?>assets/js/ZeroClipboard.min.js"></script>
<script type="text/javascript">
$(function() {
    //Copy/Paste ICS Feed
    var client = new ZeroClipboard($("#cmdCopy"));
    $('#lnkICS').click(function () {
        $("#frmLinkICS").modal('show');
    });
    client.on( "aftercopy", function( event ) {
        $('#tipCopied').tooltip('show');
        setTimeout(function() {$('#tipCopied').tooltip('hide')}, 1000);
    });
});
</script>
