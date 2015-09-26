<?php
//This is a sample page showing how to create a custom report
//We can get access to all the framework, so you can do anything with the instance of the current controller ($this)
?>
<h2><?php echo lang('Leave Management System');?></h2>

<p>This is a sample report showing the total number of leave taken this year and grouped by type.</p>

<div class="row-fluid">
    <div class="span6">
        <form action="<?php echo base_url();?>custom-report" method="GET">
                <label for="cboYear">Year
                <select name="cboYear">
                    <?php
                    if ($this->input->get('cboYear', TRUE) === FALSE) {
                        $year =  date('Y');
                    } else {
                        $year =  $this->input->get('cboYear');
                    }
                    $len =  date('Y');
                    for ($ii=date('Y', strtotime('-6 year')); $ii<= $len; $ii++) {
                        if ($ii == $year) {
                            echo "<option val='" . $ii ."' selected>" . $ii ."</option>";
                        } else {
                            echo "<option val='" . $ii ."'>" . $ii ."</option>";
                        }
                    }?>
                </select></label><br />
                <label for="txtEntity">Entity
                <div class="input-append">
                <input type="text" id="txtEntity" name="txtEntity" readonly />
                <button type="button" id="cmdSelectEntity" class="btn btn-primary">Select</button>
                </div></label>
                <input type="hidden" id="txtEntityID" name="txtEntityID" />
                <label for="chkIncludeChildren">Include children
                <input type="checkbox" id="chkIncludeChildren" name="chkIncludeChildren" checked /></label><br />
            <input type="submit" class="btn btn-primary" />
        </form>
    </div>
    <div class="span6">	
        <div id="chart"></div>
    </div>
</div>
<br />

<a href="<?php echo base_url();?>sample-page">Back to the form</a><br />

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3>Select the entity</h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn secondary">OK</a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn secondary">Cancel</a>
    </div>
</div>

<!--We can load any asset, just use the base_url methos for safer URLs//-->
<script src="<?php echo base_url();?>assets/js/d3.min.js"></script>
<script type="text/javascript">
function select_entity() {
    entity = $('#organization').jstree('get_selected')[0];
    entityName = $('#organization').jstree().get_text(entity);
    $('#txtEntity').val(entityName);
    $('#txtEntityID').val(entity);
    $("#frmSelectEntity").modal('hide');
}
    
$(document).ready(function() {
    
    $("#frmSelectEntity").alert();
    
    $("#cmdSelectEntity").click(function() {
        $("#frmSelectEntity").modal('show');
        $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
    });
});

<?php
//If you want to load another model, you need to get the instance of the current controller
$ci = get_instance();
$ci->load->model('organization_model');
$entity = 0;
$include_children = TRUE;
if ($this->input->get('txtEntityID', TRUE) !== FALSE) {
    $entity = (int) $this->input->get('txtEntityID', TRUE);
    echo '$(\'#txtEntityID\').val(\'' . $this->input->get('txtEntityID', TRUE) .'\');';
}
$include_children = filter_var($this->input->get('chkIncludeChildren'), FILTER_VALIDATE_BOOLEAN);
if ($include_children) {
    echo '$(\'#chkIncludeChildren\').prop(\'checked\', true);';
} else {
    echo '$(\'#chkIncludeChildren\').prop(\'checked\', false);';
}
if ($this->input->get('txtEntity', TRUE) !== FALSE) {
    echo '$(\'#txtEntity\').val(\'' . $this->input->get('txtEntity', TRUE) .'\');';
}

log_message('error', '$entity=' . $entity);
$users = $ci->organization_model->allEmployees($entity, $include_children);
$ids = array(0);
foreach ($users as $user) {
    array_push($ids, (int) $user->id);
}
?>
</script>
    
<script type="text/javascript">
var w = 400;
var h = 400;
var r = h/2;
var color = d3.scale.category20c();

var data = [
<?php
//$this is the instance of the current controller, so you can use it for direct access to the database?
$this->db->select('count(*) as number', FALSE);
$this->db->select('types.name as type_name');
$this->db->from('leaves');
$this->db->join('types', 'leaves.type = types.id');
$this->db->where('leaves.status', 3);
if ($this->input->get('cboYear', TRUE) === FALSE) {
    $this->db->where('YEAR(startdate) = YEAR(CURDATE())');
} else {
    $this->db->where('YEAR(startdate) = ' . $this->db->escape($this->input->get('cboYear', TRUE)));
}
$this->db->where_in('leaves.employee', $ids);
$this->db->group_by('type'); 
$this->db->order_by('number', 'desc');
$rows = $this->db->get()->result_array();

foreach ($rows as $row) {
    echo '{"label":"' . $row['type_name'] . '", "value":' . $row['number'] . '},';
}
?>
];

var vis = d3.select('#chart').append("svg:svg").data([data]).attr("width", w).attr("height", h).append("svg:g").attr("transform", "translate(" + r + "," + r + ")");
var pie = d3.layout.pie().value(function(d){return d.value;});

// declare an arc generator function
var arc = d3.svg.arc().outerRadius(r);

// select paths, use arc generator to draw
var arcs = vis.selectAll("g.slice").data(pie).enter().append("svg:g").attr("class", "slice");
arcs.append("svg:path")
    .attr("fill", function(d, i){
        return color(i);
    })
    .attr("d", function (d) {
        return arc(d);
    });

// add the text
arcs.append("svg:text").attr("transform", function(d){
        d.innerRadius = 0;
        d.outerRadius = r;
    return "translate(" + arc.centroid(d) + ")";}).attr("text-anchor", "middle").text( function(d, i) {
    return data[i].label;}
);
</script>
