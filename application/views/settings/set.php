<h1>Settings</h1>

<ul class="nav nav-tabs">
  <li><a href="#general" data-toggle="tab">General</a></li>
  <li><a href="#modules" data-toggle="tab">Modules</a></li>
  <li><a href="#security" data-toggle="tab">Security</a></li>
  <li><a href="#email" data-toggle="tab">E-mail</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="general">
      Nothing to set
  </div>
    
  <div class="tab-pane" id="modules">
      Nothing to set
  </div>
    
  <div class="tab-pane" id="security">
      Nothing to set
  </div>
    
  <div class="tab-pane" id="email">
  
      TODO : E-mail template can be changed by updating views/emails/requests.php

    <label for="protocol">protocol</label>
    <input type="input" name="protocol" id="protocol" required /><br />
    <label for="mailpath">mailpath</label>
    <input type="input" name="mailpath" id="mailpath" /><br />
    <label for="smtp_host">smtp_host</label>
    <input type="input" name="smtp_host" id="smtp_host" required /><br />
    <label for="smtp_user">smtp_user</label>
    <input type="input" name="smtp_user" id="smtp_user" required /><br />
    <label for="smtp_pass">smtp_pass</label>
    <input type="input" name="smtp_pass" id="smtp_pass" required /><br />
    <label for="smtp_port">smtp_port</label>
    <input type="input" name="smtp_port" id="smtp_port" required /><br />
    <label for="charset">charset</label>
    <input type="input" name="charset" id="charset" required /><br />
    <label for="mailtype">mailtype</label>
    <input type="input" name="mailtype" id="mailtype" required /><br />
    <label for="wordwrap">wordwrap</label>
    <input type="input" name="wordwrap" id="wordwrap" required /><br />

    <a href="<?php echo base_url();?>" class="btn btn-primary"><i class="icon-plus-sign icon-white"></i>&nbsp; Apply</a>
  </div>
</div>
