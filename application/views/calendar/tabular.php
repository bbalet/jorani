<?php
/**
 * This view builds a monthly tabular calendar for a group of employees.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.3.0
 */
?>

<h2><?php echo lang('calendar_tabular_title');?> &nbsp;<?php echo $help;?></h2>

<div class="row-fluid">
    <div class="span4">
        <label for="txtEntity">
            <?php echo lang('calendar_organization_field_select_entity');?>
            &nbsp;(<input type="checkbox" checked id="chkIncludeChildren" name="chkIncludeChildren"> <?php echo lang('calendar_tabular_check_include_subdept');?>)
        </label>
        <div class="input-append">
            <input type="text" id="txtEntity" name="txtEntity" value="<?php echo $department;?>" readonly />
            <button id="cmdSelectEntity" class="btn btn-primary" title="<?php echo lang('calendar_tabular_button_select_entity');?>"><i class="fa fa-sitemap" aria-hidden="true"></i></button>
            <!--<button id="cmdSelectList" class="btn btn-primary" title="<?php echo lang('calendar_tabular_button_select_list');?>"><i class="fa fa-users" aria-hidden="true"></i></button>//-->
        </div>
    </div>
    <div class="span4">
        <label for="txtMonthYear">
        <?php echo lang('calendar_tabular_field_month');?> / <?php echo lang('calendar_tabular_field_year');?>
        </label>
        <div class="input-prepend input-append">
            <button id="cmdPrevious" class="btn btn-primary" title="<?php echo lang('calendar_tabular_button_previous');?>"><i class="icon-chevron-left icon-white"></i></button>
            <input type="text" style="cursor:pointer;" id="txtMonthYear" name="txtMonthYear" value="<?php echo $monthName . ' ' . $year;?>" class="input-medium" readonly />
            <button id="cmdNext" class="btn btn-primary" title="<?php echo lang('calendar_tabular_button_next');?>"><i class="icon-chevron-right icon-white"></i></button>
        </div>
    </div>
    <div class="span4">
        <span class="pull-right">
            <button id="cmdExport" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp;<?php echo lang('calendar_tabular_button_export');?></button>
        </span>
    </div>
</div>

<div class="row-fluid">
    <div class="span3"><span class="label"><?php echo lang('Planned');?></span></div>
    <div class="span3"><span class="label label-success"><?php echo lang('Accepted');?></span></div>
    <div class="span3"><span class="label label-warning"><?php echo lang('Requested');?></span></div>
    <div class="span3">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span12" id="spnTabularView"><?php echo $tabularPartialView; ?></div>
</div>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('calendar_tabular_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn"><?php echo lang('calendar_tabular_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn"><?php echo lang('calendar_tabular_popup_entity_button_cancel');?></a>
    </div>
</div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/bootstrap-datepicker-1.6.4/css/bootstrap-datepicker.min.css">
<script src="<?php echo base_url();?>assets/bootstrap-datepicker-1.6.4/js/bootstrap-datepicker.min.js"></script>
<?php if ($language_code != 'en') {?>
<script src="<?php echo base_url();?>assets/bootstrap-datepicker-1.6.4/locales/bootstrap-datepicker.<?php echo $language_code;?>.min.js"></script>
<?php }?>

<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript">
    //Global locale for moment objects
    moment.locale('<?php echo $language_code;?>', {longDateFormat : {L : '<?php echo lang('global_date_momentjs_format');?>'}});

    var entity = -1; //Id of the selected entity
    var text; //Label of the selected entity
    var entity = <?php echo $entity;?>;
    var month = (<?php echo $month;?> - 1); //Monent.js uses 0 based numbers!
    var year = <?php echo $year;?>;
    var children = '<?php echo $children;?>';
    var currentDate = moment().year(year).month(month).date(1);
    
    // After selection of an entity from the modal dialog, refresh the partial
    // view if the entity is diferent
    function select_entity() {
        old_entity = entity;
        entity = $('#organization').jstree('get_selected')[0];
        text = $('#organization').jstree().get_text(entity);
        $('#txtEntity').val(text);
        $("#frmSelectEntity").modal('hide');
        if (old_entity != entity) {
            reloadTabularView();
        }
    }
    
    // Return a boolean value representing the value of checkbox
    function includeChildren() {
        if ($('#chkIncludeChildren').prop('checked') == true) {
            return 'true';
        } else {
            return 'false';
        }
    }
    
    // Reload the partial view containing the tabular calendar
    function reloadTabularView() {
        children = includeChildren();
        $("#spnTabularView").html('<img src="<?php echo base_url();?>assets/images/loading.gif">');
        //Month number needs to be converted between monment.js and PHP
        $("#spnTabularView").load('<?php echo base_url();?>calendar/tabular/partial/' + entity + '/' + (month + 1) + '/' + year+ '/' + children, function(response, status, xhr) {
            if (xhr.status == 401) {
                $("#frmShowHistory").modal('hide');
                bootbox.alert("<?php echo lang('global_ajax_timeout');?>", function() {
                    //After the login page, we'll be redirected to the current page 
                   location.reload();
                });
            }
        });
        // Update the text view containing the date
        var fullDate = moment().date(1).month(month).year(year).format("MMMM");
        fullDate = fullDate + ' ' + year;
        $("#txtMonthYear").val(fullDate);
    }
    
    $(document).ready(function() {
        //Select radio button depending on URL
        if (children == '1') {
            $("#chkIncludeChildren").prop("checked", true);
        } else {
            $("#chkIncludeChildren").prop("checked", false);
        }
        
        // On changing 'include children' checkbox, reload the partial view
        $('#chkIncludeChildren').change(function() {
            reloadTabularView();
        });
        
        //Intialize Month/Year selection
        $("#txtMonthYear").datepicker({
            format: "MM yyyy",
            startView: 1,
            minViewMode: 1,
            todayBtn: 'linked',
            todayHighlight: true,
            language: "<?php echo $language_code;?>",
            autoclose: true
        }).on("changeDate", function(e) {
            month = new Date(e.date).getMonth();
            //Doesn't work : year = new Date(e.date).getYear();
            year = parseInt(String(e.date).split(" ")[3]);
            currentDate = moment().year(year).month(month).date(1);
            reloadTabularView();
        });
        
        //Popup select entity
        $("#cmdSelectEntity").click(function() {
            $("#frmSelectEntity").modal('show');
            $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
        });

        //Export the report into Excel
        $("#cmdExport").click(function() {
            children = includeChildren();
            if (entity != -1) {
                url = '<?php echo base_url();?>calendar/tabular/export/' + entity + '/' + month+ '/' + year+ '/' + children;
                document.location.href = url;
            }
        });
        
        //Previous/Next
        $('#cmdPrevious').click(function() {
            currentDate = currentDate.add(-1, 'M');
            month = currentDate.month();
            year = currentDate.year();
            reloadTabularView();
        });
        $('#cmdNext').click(function() {
            currentDate = currentDate.add(1, 'M');
            month = currentDate.month();
            year = currentDate.year();
            reloadTabularView();
        });
        
        //Load alert forms
        $("#frmSelectEntity").alert();
        //Prevent to load always the same content (refreshed each time)
        $('#frmSelectEntity').on('hidden', function() {
            $(this).removeData('modal');
        });
    });
</script>
