<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('reports', $language);?>

<?php 

include FCPATH . '/local/reports/' . $report . '/' . $action;

?>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>
<div class="row-fluid">
    <div class="span4">
      <a href="<?php echo base_url();?>reports" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('reports_execute_button_back_list');?></a>
    </div>
    <div class="span8">&nbsp;</div>
</div>
