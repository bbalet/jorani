<?php 
/**
 * This view shows the result of the import function.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.4
 */
?>

<div class="row-fluid">
    <div class="span12">

        <h2><?php echo $title;?><?php echo $help;?></h2>

        <table class="table table-bordered table-hover table-condensed">
          <thead>
            <tr>
                <td>Description</td>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($messages as $message): ?>
            <tr><td><?php echo $message; ?></td></tr>
          <?php endforeach ?>
          </tbody>
        </table>

    </div>
</div>
