<?php 
/**
 * This view is included into all desktop full views. It contains the footer of the application.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
                    </div><!-- /span12 -->
                </div><!-- /row -->
            </div><!-- /container -->
        <div id="push"></div>
    </div><!-- /wrap -->
    <!-- FOOTER -->
    <div class="row" id="footer">
      <div class="span4 pull-right">
            <?php switch ($language_code){
                case 'fr' : echo '<a class="anchor" href="http://fr.jorani.org/" target="_blank">Jorani</a>'; break;
                default : echo '<a class="anchor" href="http://jorani.org/" target="_blank">Jorani</a>'; break;
            } ?>          
        &nbsp;v0.5.1&nbsp;&copy;2014-2017 Benjamin BALET
      </div>
    </div>
    </div>
</body>
</html>
