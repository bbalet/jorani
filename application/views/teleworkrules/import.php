<?php
/**
 * This view allows to create a bundle of telework rules by import.
 * @copyright  Copyright (c) 2021 Maithyly SIVAPALAN
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<h2><?php echo lang('telework_rule_import_title');?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open_multipart('teleworkrules/import');?>
			<label for="file"><?php echo lang('telework_rule_import_field_file');?></label>
            <input type="file" name="file" id="file" size="150" />
            <br /><br />
            <input class="btn btn-primary" type="submit" value="<?php echo lang('telework_rule_import_button');?>" />
        </form>