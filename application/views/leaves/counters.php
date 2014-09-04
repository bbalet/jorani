<?php
/*
 * This file is part of lms.
 *
 * lms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('leaves', $language);
$this->lang->load('global', $language);?>

<h1><?php echo lang('leaves_summary_title');?> &nbsp;
<a href="http://www.leave-management-system.org/page-my-summary.html" title="<?php echo lang('global_link_tooltip_documentation');?>" target="_blank"><i class="icon-question-sign"></i></a></h1>

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
      <td><?php echo (float) $value[0]; ?></td>
      <td><?php echo (float) $value[1]; ?></td>
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
