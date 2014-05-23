<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('footer', $language);?>

<!-- FOOTER -->
      <footer>		
        <hr style="margin: 20px 0;">
        <div class="row-fluid">
            <div class="span5"><?php echo lang('footer_copyright_notice');?></div>
            <div class="span3"><?php echo $this->config->item('version');?></div>
            <div class="pull-right"><span class="pull-right"><a href="#"><?php echo lang('footer_link_go_top');?></a></span></div>
        </div>
      </footer>

    </div><!-- /.container -->
</body>
</html>