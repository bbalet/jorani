<?php 
/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jorani is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */
?>

<div class="input-append">
<input type="text" placeholder="<?php echo lang('organization_select_field_search_placeholder');?>" id="txtSearch" />
<button id="cmdSearchOrg" class="btn btn-primary"><?php echo lang('organization_select_button_search');?></button>
</div>

<div style="text-align: left;" id="organization"></div>

<link rel="stylesheet" href='<?php echo base_url(); ?>assets/jsTree/themes/default/style.css' type="text/css" media="screen, projection" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/jsTree/jstree.min.js"></script>

<script type="text/javascript">
    $(function () {
        //Search in the treeview
        $("#cmdSearchOrg").click(function () {
            $("#organization").jstree("search", $("#txtSearch").val());
        });
        
        $('#organization').jstree({
            rules : {
                deletable  : false,
                creatable  : false,
                draggable  : false,
                dragrules  : false,
                renameable : false
              },
            core : {
              multiple : false,
              data : {
                url : function (node) {
                  return node.id === '#' ? 
                    '<?php echo base_url(); ?>organization/root' : 
                    '<?php echo base_url(); ?>organization/children';
                },
                'data' : function (node) {
                  return { 'id' : node.id };
                }
              },
              'check_callback' : true
            },
            plugins: [ "search", "state", "sort" ]
        });
    });
</script>
