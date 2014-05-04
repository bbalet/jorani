<?php
CI_Controller::get_instance()->load->helper('language');
$this->lang->load('organization', $language);?>

<div class="input-append">
<input type="text" placeholder="Search for an entity" id="txtSearch" />
<button id="cmdSearchOrg" class="btn btn-primary">Search</button>
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
            "plugins" : [
              "search", "state", "sort"
            ]
        });
    });
</script>
