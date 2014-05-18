<div class="row-fluid">
    <div class="span12">
        
<h1>List of users</h1>

<div class="row-fluid">
    <div class="span4">
        <label for="txtEntity">Select the entity</label>
        <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" />
        <button id="cmdSelectEntity" class="btn btn-primary">Select</button>
        </div>
    </div>
    <div class="span3">
        <label class="checkbox">
            <input type="checkbox" value="" id="chkIncludeChildren"> Include sub-departments
        </label>
    </div>
    <div class="span5">&nbsp;</div>
</div>

<div id="reportResult"></div>

	</div>
</div>

<div class="row-fluid">
	<div class="span12">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span4">
      <a href="<?php echo base_url();?>users/export" class="btn btn-primary"><i class="icon-file icon-white"></i>&nbsp; Export this report</a>
    </div>
    <div class="span8">&nbsp;</div>
</div>



<script type="text/javascript">

var entity = -1; //Id of the selected entity
var text; //Label of the selected entity

function select_entity() {
    entity = $('#organization').jstree('get_selected')[0];
    text = $('#organization').jstree().get_text(entity);
    $('#txtEntity').val(text);
    $('#calendar').fullCalendar('removeEvents');
    if ($('#chkIncludeChildren').prop('checked') == true) {
        $('#calendar').fullCalendar('addEventSource', '<?php echo base_url();?>leaves/organization/' + entity + '?children=true');
    } else {
        $('#calendar').fullCalendar('addEventSource', '<?php echo base_url();?>leaves/organization/' + entity + '?children=false');
    }
    $('#calendar').fullCalendar('rerenderEvents');
    $("#frmSelectEntity").modal('hide');
}

$(document).ready(function() {
    
    
        //On click the check box "include sub-department", refresh the content if a department was selected
        $('#chkIncludeChildren').click(function() {
            if (entity != -1) {
                if ($('#chkIncludeChildren').prop('checked') == true) {
                    $('#calendar').fullCalendar('addEventSource', '<?php echo base_url();?>leaves/organization/' + entity + '?children=true');
                } else {
                    $('#calendar').fullCalendar('addEventSource', '<?php echo base_url();?>leaves/organization/' + entity + '?children=false');
                }
            }
        });
});
</script>
