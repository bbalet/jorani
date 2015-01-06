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

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('leaves_summary_title');?> &nbsp;
<a href="<?php echo lang('global_link_doc_page_my_summary');?>" title="<?php echo lang('global_link_tooltip_documentation');?>" target="_blank" rel="nofollow"><i class="icon-question-sign"></i></a></h2>

    <p><?php echo lang('leaves_summary_date_field');?>&nbsp;
        <input type="text" id="refdate" value="<?php $date = new DateTime($refDate); echo $date->format(lang('global_date_format'));?>" />
    </p>

<table class="table table-bordered table-hover">
<thead>
    <tr>
      <th><?php echo lang('leaves_summary_thead_type');?></th>
      <th><?php echo lang('leaves_summary_thead_available');?></th>
      <th><?php echo lang('leaves_summary_thead_taken');?></th>
      <th><?php echo lang('leaves_summary_thead_entitled');?></th>
      <th><?php echo lang('leaves_summary_thead_description');?></th>
    </tr>
  </thead>
  <tbody>
  <?php if (count($summary) > 0) {
  foreach ($summary as $key => $value) { ?>
    <tr>
      <td><?php echo $key; ?></td>
      <td><?php echo ((float) $value[1] - (float) $value[0]); ?></td>
      <td><?php if ($value[2] == '') { echo ((float) $value[0]); } else { echo '-'; } ?></td>
      <td><?php if ($value[2] == '') { echo ((float) $value[1]); } else { echo '-'; } ?></td>
      <td><?php echo $value[2]; ?></td>
    </tr>
  <?php }
  } else {?>
    <tr>
      <td colspan="4"><?php echo lang('leaves_summary_tbody_empty');; ?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>

        </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui-1.10.4.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {
        
        $('#refdate').datepicker({
            onSelect: function(dateText, inst) {
                    tmpUnix = moment($("#refdate").datepicker("getDate")).utc().unix();
                    url = "<?php echo base_url();?>leaves/counters/" + tmpUnix;
                    window.location = url;
            }
        });
    });
</script>
