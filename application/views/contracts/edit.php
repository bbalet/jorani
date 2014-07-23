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
$this->lang->load('contract', $language);
$this->lang->load('calendar', $language);?>

<h2><?php echo lang('contract_edit_description');?> <?php echo $contract['id']; ?></h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'target');
echo form_open('contracts/edit/' . $contract['id'], $attributes); ?>

    <input type="hidden" name="id" value="<?php echo $contract['id']; ?>" required />
    
    <label for="name"><?php echo lang('contract_edit_field_name');?></label>
    <input type="input" name="name" value="<?php echo $contract['name']; ?>" required /><br />
  
    <label for="startentdatemonth"><?php echo lang('contract_edit_field_start_month');?></label>
    <select name="startentdatemonth" required>
        <option value="1" <?php if (substr($contract['startentdate'], 0, 2) == '1') { echo "selected"; } ?>><?php echo lang('January');?></option>
        <option value="2" <?php if (substr($contract['startentdate'], 0, 2) == '2') { echo "selected"; } ?>><?php echo lang('February');?></option>
        <option value="3" <?php if (substr($contract['startentdate'], 0, 2) == '3') { echo "selected"; } ?>><?php echo lang('March');?></option>
        <option value="4" <?php if (substr($contract['startentdate'], 0, 2) == '4') { echo "selected"; } ?>><?php echo lang('April');?></option>
        <option value="5" <?php if (substr($contract['startentdate'], 0, 2) == '5') { echo "selected"; } ?>><?php echo lang('May');?></option>
        <option value="6" <?php if (substr($contract['startentdate'], 0, 2) == '6') { echo "selected"; } ?>><?php echo lang('June');?></option>
        <option value="7" <?php if (substr($contract['startentdate'], 0, 2) == '7') { echo "selected"; } ?>><?php echo lang('July');?></option>
        <option value="8" <?php if (substr($contract['startentdate'], 0, 2) == '8') { echo "selected"; } ?>><?php echo lang('August');?></option>
        <option value="9" <?php if (substr($contract['startentdate'], 0, 2) == '9') { echo "selected"; } ?>><?php echo lang('September');?></option>
        <option value="10" <?php if (substr($contract['startentdate'], 0, 2) == '10') { echo "selected"; } ?>><?php echo lang('October');?></option>
        <option value="11" <?php if (substr($contract['startentdate'], 0, 2) == '11') { echo "selected"; } ?>><?php echo lang('November');?></option>
        <option value="12" <?php if (substr($contract['startentdate'], 0, 2) == '12') { echo "selected"; } ?>><?php echo lang('December');?></option>
    </select>
    
    <label for="startentdateday"><?php echo lang('contract_edit_field_start_day');?></label>
    <select name="startentdateday" required>
        <option value='1' <?php if (substr($contract['startentdate'], 3, 2) == '1') { echo "selected"; } ?>>1</option>
        <option value='2' <?php if (substr($contract['startentdate'], 3, 2) == '2') { echo "selected"; } ?>>2</option>
        <option value='3' <?php if (substr($contract['startentdate'], 3, 2) == '3') { echo "selected"; } ?>>3</option>
        <option value='4' <?php if (substr($contract['startentdate'], 3, 2) == '4') { echo "selected"; } ?>>4</option>
        <option value='5' <?php if (substr($contract['startentdate'], 3, 2) == '5') { echo "selected"; } ?>>5</option>
        <option value='6' <?php if (substr($contract['startentdate'], 3, 2) == '6') { echo "selected"; } ?>>6</option>
        <option value='7' <?php if (substr($contract['startentdate'], 3, 2) == '7') { echo "selected"; } ?>>7</option>
        <option value='8' <?php if (substr($contract['startentdate'], 3, 2) == '8') { echo "selected"; } ?>>8</option>
        <option value='9' <?php if (substr($contract['startentdate'], 3, 2) == '9') { echo "selected"; } ?>>9</option>
        <option value='10' <?php if (substr($contract['startentdate'], 3, 2) == '10') { echo "selected"; } ?>>10</option>
        <option value='11' <?php if (substr($contract['startentdate'], 3, 2) == '11') { echo "selected"; } ?>>11</option>
        <option value='12' <?php if (substr($contract['startentdate'], 3, 2) == '12') { echo "selected"; } ?>>12</option>
        <option value='13' <?php if (substr($contract['startentdate'], 3, 2) == '13') { echo "selected"; } ?>>13</option>
        <option value='14' <?php if (substr($contract['startentdate'], 3, 2) == '14') { echo "selected"; } ?>>14</option>
        <option value='15' <?php if (substr($contract['startentdate'], 3, 2) == '15') { echo "selected"; } ?>>15</option>
        <option value='16' <?php if (substr($contract['startentdate'], 3, 2) == '16') { echo "selected"; } ?>>16</option>
        <option value='17' <?php if (substr($contract['startentdate'], 3, 2) == '17') { echo "selected"; } ?>>17</option>
        <option value='18' <?php if (substr($contract['startentdate'], 3, 2) == '18') { echo "selected"; } ?>>18</option>
        <option value='19' <?php if (substr($contract['startentdate'], 3, 2) == '19') { echo "selected"; } ?>>19</option>
        <option value='20' <?php if (substr($contract['startentdate'], 3, 2) == '20') { echo "selected"; } ?>>20</option>
        <option value='21' <?php if (substr($contract['startentdate'], 3, 2) == '21') { echo "selected"; } ?>>21</option>
        <option value='22' <?php if (substr($contract['startentdate'], 3, 2) == '22') { echo "selected"; } ?>>22</option>
        <option value='23' <?php if (substr($contract['startentdate'], 3, 2) == '23') { echo "selected"; } ?>>23</option>
        <option value='24' <?php if (substr($contract['startentdate'], 3, 2) == '24') { echo "selected"; } ?>>24</option>
        <option value='25' <?php if (substr($contract['startentdate'], 3, 2) == '25') { echo "selected"; } ?>>25</option>
        <option value='26' <?php if (substr($contract['startentdate'], 3, 2) == '26') { echo "selected"; } ?>>26</option>
        <option value='27' <?php if (substr($contract['startentdate'], 3, 2) == '27') { echo "selected"; } ?>>27</option>
        <option value='28' <?php if (substr($contract['startentdate'], 3, 2) == '28') { echo "selected"; } ?>>28</option>
        <option value='29' <?php if (substr($contract['startentdate'], 3, 2) == '29') { echo "selected"; } ?>>29</option>
        <option value='30' <?php if (substr($contract['startentdate'], 3, 2) == '30') { echo "selected"; } ?>>30</option>
        <option value='31' <?php if (substr($contract['startentdate'], 3, 2) == '31') { echo "selected"; } ?>>31</option>
    </select>
    
    <br /><br />
    
    <label for="endentdatemonth"><?php echo lang('contract_edit_field_end_month');?></label>
    <select name="endentdatemonth" required>
        <option value="1" <?php if (substr($contract['endentdate'], 0, 2) == '1') { echo "selected"; } ?>><?php echo lang('January');?></option>
        <option value="2" <?php if (substr($contract['endentdate'], 0, 2) == '2') { echo "selected"; } ?>><?php echo lang('February');?></option>
        <option value="3" <?php if (substr($contract['endentdate'], 0, 2) == '3') { echo "selected"; } ?>><?php echo lang('March');?></option>
        <option value="4" <?php if (substr($contract['endentdate'], 0, 2) == '4') { echo "selected"; } ?>><?php echo lang('April');?></option>
        <option value="5" <?php if (substr($contract['endentdate'], 0, 2) == '5') { echo "selected"; } ?>><?php echo lang('May');?></option>
        <option value="6" <?php if (substr($contract['endentdate'], 0, 2) == '6') { echo "selected"; } ?>><?php echo lang('June');?></option>
        <option value="7" <?php if (substr($contract['endentdate'], 0, 2) == '7') { echo "selected"; } ?>><?php echo lang('July');?></option>
        <option value="8" <?php if (substr($contract['endentdate'], 0, 2) == '8') { echo "selected"; } ?>><?php echo lang('August');?></option>
        <option value="9" <?php if (substr($contract['endentdate'], 0, 2) == '9') { echo "selected"; } ?>><?php echo lang('September');?></option>
        <option value="10" <?php if (substr($contract['endentdate'], 0, 2) == '10') { echo "selected"; } ?>><?php echo lang('October');?></option>
        <option value="11" <?php if (substr($contract['endentdate'], 0, 2) == '11') { echo "selected"; } ?>><?php echo lang('November');?></option>
        <option value="12" <?php if (substr($contract['endentdate'], 0, 2) == '12') { echo "selected"; } ?>><?php echo lang('December');?></option>
    </select>
    
    <label for="endentdateday"><?php echo lang('contract_edit_field_end_day');?></label>
    <select name="endentdateday" required>
        <option value='1' <?php if (substr($contract['endentdate'], 3, 2) == '1') { echo "selected"; } ?>>1</option>
        <option value='2' <?php if (substr($contract['endentdate'], 3, 2) == '2') { echo "selected"; } ?>>2</option>
        <option value='3' <?php if (substr($contract['endentdate'], 3, 2) == '3') { echo "selected"; } ?>>3</option>
        <option value='4' <?php if (substr($contract['endentdate'], 3, 2) == '4') { echo "selected"; } ?>>4</option>
        <option value='5' <?php if (substr($contract['endentdate'], 3, 2) == '5') { echo "selected"; } ?>>5</option>
        <option value='6' <?php if (substr($contract['endentdate'], 3, 2) == '6') { echo "selected"; } ?>>6</option>
        <option value='7' <?php if (substr($contract['endentdate'], 3, 2) == '7') { echo "selected"; } ?>>7</option>
        <option value='8' <?php if (substr($contract['endentdate'], 3, 2) == '8') { echo "selected"; } ?>>8</option>
        <option value='9' <?php if (substr($contract['endentdate'], 3, 2) == '9') { echo "selected"; } ?>>9</option>
        <option value='10' <?php if (substr($contract['endentdate'], 3, 2) == '10') { echo "selected"; } ?>>10</option>
        <option value='11' <?php if (substr($contract['endentdate'], 3, 2) == '11') { echo "selected"; } ?>>11</option>
        <option value='12' <?php if (substr($contract['endentdate'], 3, 2) == '12') { echo "selected"; } ?>>12</option>
        <option value='13' <?php if (substr($contract['endentdate'], 3, 2) == '13') { echo "selected"; } ?>>13</option>
        <option value='14' <?php if (substr($contract['endentdate'], 3, 2) == '14') { echo "selected"; } ?>>14</option>
        <option value='15' <?php if (substr($contract['endentdate'], 3, 2) == '15') { echo "selected"; } ?>>15</option>
        <option value='16' <?php if (substr($contract['endentdate'], 3, 2) == '16') { echo "selected"; } ?>>16</option>
        <option value='17' <?php if (substr($contract['endentdate'], 3, 2) == '17') { echo "selected"; } ?>>17</option>
        <option value='18' <?php if (substr($contract['endentdate'], 3, 2) == '18') { echo "selected"; } ?>>18</option>
        <option value='19' <?php if (substr($contract['endentdate'], 3, 2) == '19') { echo "selected"; } ?>>19</option>
        <option value='20' <?php if (substr($contract['endentdate'], 3, 2) == '20') { echo "selected"; } ?>>20</option>
        <option value='21' <?php if (substr($contract['endentdate'], 3, 2) == '21') { echo "selected"; } ?>>21</option>
        <option value='22' <?php if (substr($contract['endentdate'], 3, 2) == '22') { echo "selected"; } ?>>22</option>
        <option value='23' <?php if (substr($contract['endentdate'], 3, 2) == '23') { echo "selected"; } ?>>23</option>
        <option value='24' <?php if (substr($contract['endentdate'], 3, 2) == '24') { echo "selected"; } ?>>24</option>
        <option value='25' <?php if (substr($contract['endentdate'], 3, 2) == '25') { echo "selected"; } ?>>25</option>
        <option value='26' <?php if (substr($contract['endentdate'], 3, 2) == '26') { echo "selected"; } ?>>26</option>
        <option value='27' <?php if (substr($contract['endentdate'], 3, 2) == '27') { echo "selected"; } ?>>27</option>
        <option value='28' <?php if (substr($contract['endentdate'], 3, 2) == '28') { echo "selected"; } ?>>28</option>
        <option value='29' <?php if (substr($contract['endentdate'], 3, 2) == '29') { echo "selected"; } ?>>29</option>
        <option value='30' <?php if (substr($contract['endentdate'], 3, 2) == '30') { echo "selected"; } ?>>30</option>
        <option value='31' <?php if (substr($contract['endentdate'], 3, 2) == 31) { echo "selected"; } ?>>31</option>
    </select>

    <br /><br />
    <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('contract_edit_button_update');?></button>
    &nbsp;
    <a href="<?php echo base_url();?>contracts" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('contract_edit_button_cancel');?></a>
</form>
