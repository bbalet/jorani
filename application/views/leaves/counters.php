<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('leaves', $language);?>

<h1><?php echo lang('leaves_summary_title');?></h1>

<table class="table table-bordered table-hover">
<thead>
    <tr>
      <th><?php echo lang('leaves_summary_thead_type');?></th>
      <th><?php echo lang('leaves_summary_thead_taken');?></th>
      <th><?php echo lang('leaves_summary_thead_entitled');?></th>
      <th><?php echo lang('leaves_summary_thead_description');?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($summary as $key => $value) { ?>
    <tr>
      <td><?php echo $key; ?></td>
      <td><?php echo $value[0]; ?></td>
      <td><?php echo $value[1]; ?></td>
      <td><?php echo $value[2]; ?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
