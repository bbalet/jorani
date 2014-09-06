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
$this->lang->load('users', $language);?>

<h1><?php echo lang('users_myprofile_title');?></h1>

<table border="1">
    <tr>
        <td><strong><?php echo lang('users_myprofile_field_firstname');?></strong></td>
        <td><?php echo $user['firstname'];?></td>
    </tr>
    <tr>
        <td><strong><?php echo lang('users_myprofile_field_lastname');?></strong></td>
        <td><?php echo $user['lastname'];?></td>
    </tr>
    <tr>
        <td><strong><?php echo lang('users_myprofile_field_manager');?></strong></td>
        <td><?php echo $manager_label;?></td>
    </tr>
    <tr>
        <td><strong><?php echo lang('users_myprofile_field_contract');?></strong></td>
        <td><?php echo $contract_label;?></td>
    </tr>
    <tr>
        <td><strong><?php echo lang('users_myprofile_field_position');?></strong></td>
        <td><?php echo $position_label;?></td>
    </tr>
    <tr>
        <td><strong><?php echo lang('users_myprofile_field_entity');?></strong></td>
        <td><?php echo $organization_label;?></td>
    </tr>
    <tr>
        <td><strong><?php echo lang('users_myprofile_field_hired');?></strong></td>
        <td><?php echo $user['datehired'];?></td>
    </tr>
    <tr>
        <td><strong><?php echo lang('users_myprofile_field_identifier');?></strong></td>
        <td><?php echo $user['identifier'];?></td>
    </tr>
</table>
