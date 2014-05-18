<h2>Create a new user &nbsp;
<a href="http://www.leave-management-system.org/en/documentation/page-create-a-new-user/" title="Link to documentation" target="_blank"><i class="icon-question-sign"></i></a>
</h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'target');
echo form_open('users/create', $attributes); ?>

    <input type="hidden" name="CipheredValue" id="CipheredValue" />
    <label for="firstname">Firstname</label>
    <input type="input" name="firstname" id="firstname" required /><br />

    <label for="lastname">Lastname</label>
    <input type="input" name="lastname" id="lastname" required /><br />

    <label for="role[]">Role</label>
    <select name="role[]" multiple="multiple" size="2">
    <?php foreach ($roles as $roles_item): ?>
        <option value="<?php echo $roles_item['id'] ?>" <?php if ($roles_item['id'] == 2) echo "selected" ?>><?php echo $roles_item['name'] ?></option>
    <?php endforeach ?>
    </select>
    
    <label for="login">Login</label>
    <input type="input" name="login" id="login" required /><br />

    <label for="email">E-mail</label>
    <input type="email" id="email" name="email" required /><br />
    
    <br />
    <input type="hidden" name="manager" id="manager" required /><br />
    <label for="txtManager">Select the manager</label>
    <div class="input-append">
        <input type="text" id="txtManager" name="txtManager" />
        <a id="cmdSelfManager" class="btn btn-primary">Self</a>
        <a id="cmdSelectManager" class="btn btn-primary">Select</a>
    </div><br />
    <i>If a user is its own manager (Self), it can validate its own leave requests.</i>
    <br />
    
    <input type="hidden" name="entity" id="entity" required /><br />
    <label for="txtEntity">Select the entity</label>
    <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" />
        <a id="cmdSelectEntity" class="btn btn-primary">Select</a>
    </div>
    <br />
    
    <input type="hidden" name="position" id="position" required /><br />
    <label for="txtPosition">Select the position</label>
    <div class="input-append">
        <input type="text" id="txtPosition" name="txtPosition" />
        <a id="cmdSelectPosition" class="btn btn-primary">Select</a>
    </div>    
    <br />
    
</form>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required />&nbsp;
    <a class="btn" id="cmdGeneratePassword">
        <i class="icon-refresh"></i>&nbsp;Generate password
    </a>
    <br />
    
    <button id="send" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;Create user</button>
    &nbsp;
    <a href="<?php echo base_url(); ?>users" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;Cancel</a>

<div id="frmSelectManager" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmSelectManager').modal('hide')" class="close">&times;</a>
         <h3>Select the manager</h3>
    </div>
    <div class="modal-body" id="frmSelectManagerBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="javascript:select_manager();" class="btn secondary">OK</a>
        <a href="javascript:$('#frmSelectManager').modal('hide')" class="btn secondary">Cancel</a>
    </div>
</div>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmSelectEntity').modal('hide')" class="close">&times;</a>
         <h3>Select an entity</h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="javascript:select_entity();" class="btn secondary">OK</a>
        <a href="javascript:$('#frmSelectEntity').modal('hide')" class="btn secondary">Cancel</a>
    </div>
</div>

<div id="frmSelectPosition" class="modal hide fade">
    <div class="modal-header">
        <a href="javascript:$('#frmSelectPosition').modal('hide')" class="close">&times;</a>
         <h3>Select a position</h3>
    </div>
    <div class="modal-body" id="frmSelectPositionBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="javascript:select_position();" class="btn secondary">OK</a>
        <a href="javascript:$('#frmSelectPosition').modal('hide')" class="btn secondary">Cancel</a>
    </div>
</div>
    
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jqBootstrapValidation.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/lms.password.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jsencrypt.min.js"></script>
<script type="text/javascript">

    function select_manager() {
        var manager = $('#employees .row_selected td:first').text();
        var text = $('#employees .row_selected td:eq(1)').text();
        text += ' ' + $('#employees .row_selected td:eq(2)').text();
        $('#manager').val(manager);
        $('#txtManager').val(text);
        $("#frmSelectManager").modal('hide');
    }
    
    function select_entity() {
        var entity = $('#organization').jstree('get_selected')[0];
        var text = $('#organization').jstree().get_text(entity);
        $('#entity').val(entity);
        $('#txtEntity').val(text);
        $("#frmSelectEntity").modal('hide');
    }
    
    function select_position() {
        var position = $('#positions .row_selected td:first').text();
        var text = $('#positions .row_selected td:eq(1)').text();
        $('#position').val(position);
        $('#txtPosition').val(text);
        $("#frmSelectPosition").modal('hide');
    }

    $(function () {
        $("input").not("[type=submit]").jqBootstrapValidation(); 
        
        $("#cmdGeneratePassword").click(function() {
            $("#password").val(password_generator(<?php echo $this->config->item('password_length');?>));
        });
        
        //On any change on firstname or lastname fields, automatically build the
        //login identifier with first character of firstname and lastname
        $("#firstname").change(function() {
            $("#login").val($("#firstname").val().charAt(0).toLowerCase() +
                    $("#lastname").val().toLowerCase());            
        });
        $("#lastname").change(function() {
            $("#login").val($("#firstname").val().charAt(0).toLowerCase() +
                    $("#lastname").val().toLowerCase());            
        });
        
        $('#send').click(function() {
            var encrypt = new JSEncrypt();
            encrypt.setPublicKey($('#pubkey').val());
            var encrypted = encrypt.encrypt($('#password').val());
            $('#CipheredValue').val(encrypted);
            $('#target').submit();
        });
        
        //Popup select position
        $("#cmdSelectManager").click(function() {
            $("#frmSelectManager").modal('show');
            $("#frmSelectManagerBody").load('<?php echo base_url(); ?>users/employees');
        });
        
        //Popup select position
        $("#cmdSelectPosition").click(function() {
            $("#frmSelectPosition").modal('show');
            $("#frmSelectPositionBody").load('<?php echo base_url(); ?>positions/select');
        });
        
        //Popup select entity
        $("#cmdSelectEntity").click(function() {
            $("#frmSelectEntity").modal('show');
            $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
        });

        //Load alert forms
        $("#frmSelectEntity").alert();
        //Prevent to load always the same content (refreshed each time)
        $('#frmSelectEntity').on('hidden', function() {
            $(this).removeData('modal');
        });
        
        //Self manager button
        $("#cmdSelfManager").click(function() {
            $("#manager").val('-1');
            $('#txtManager').val('No line manager');
        });
    });

</script>

<textarea id="pubkey" style="visibility:hidden;"><?php echo $public_key; ?></textarea>
