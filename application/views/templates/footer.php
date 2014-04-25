<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('footer', $language);?>

<!-- FOOTER -->
      <footer>		
        <hr style="margin: 20px 0;">
        <p><?php echo lang('footer_copyright_notice');?></p>
        <p class="pull-right"><a href="#"><?php echo lang('footer_link_go_top');?></a></p>
      </footer>

	</div><!-- /.container -->
</body>
</html>