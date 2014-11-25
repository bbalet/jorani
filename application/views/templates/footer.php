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
 * along with lms.  If not, see <http://www.gnu.org/licenses/>.
 */

CI_Controller::get_instance()->load->helper('language');
$this->lang->load('footer', $language);?>

<!-- FOOTER -->
      <footer>		
        <hr style="margin: 20px 0;">
        <div class="row-fluid">
            <div class="span5"><?php echo lang('footer_copyright_notice');?></div>
            <div class="span3">v0.1.4</div>
            <div class="span4"><span class="pull-right"><a href="#"><?php echo lang('footer_link_go_top');?></a></span></div>
        </div>
      </footer>

    </div><!-- /.container -->
</body>
</html>