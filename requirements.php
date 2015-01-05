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

//MD5 hash table of the tables structure for every released version of LMS
/*$signatures['090176a36afb8ed6c1f394ef047454bb'] = 'latest';        //actions
$signatures['ba852f157316c6c648c44e1be48bc8cd'] = 'latest';        //activities
$signatures['4d61910d9105a31d5b965177b10fb874'] = 'latest';     //activities_employee
$signatures['0c3fc9a0f779c75ec4b4c96341727104'] = 'latest';         //activities_employee_history
$signatures['8f58c1bb23216c29171d3838dd9a08f4'] = 'latest';       //activities_history
$signatures['08d5d68aa1d0fc5f9cf73ac04a7b0057'] = 'latest';         //contracts
$signatures['f3508b98f88cfaa881d334994c1b4d08'] = 'latest';        //contracts_history
$signatures['26b43a360dc13a9760db648847a68010'] = 'latest';     //dayoffs
$signatures['2ef1e76ae608e837f46d5715d9b7e18e'] = 'latest';        //entitleddays
$signatures['8e35f873c983d4cf85ec1f1ffc59bae0'] = 'latest';            //entitleddays_history
$signatures['4885d0aadb560a1846449ef89a9af045'] = 'latest';       //leaves
$signatures['02353210d13510ff9994471589090b3a'] = 'latest';     //leaves_history
$signatures['b056b80de22211ce5f0bd2898aae13b4'] = 'latest';      //organization
$signatures['05532f48b5530b397a6b1b2a2c142ad1'] = 'latest';      //organization_history
$signatures['6be565294dd5a041015d8ef92f4d9c75'] = 'latest';       //overtime
$signatures['6bcf8d452d58c8b2b8ddadfb8cd25e52'] = 'latest';        //overtime_history
$signatures['71840bdcd051b2431e76765d8af6a4c1'] = 'latest';       //positions
$signatures['35d39862a5755abb1d76a745538518e2'] = 'latest';     //positions_history
$signatures['a2fd495bedfe1e75ca0e1519c0e8c0e8'] = 'latest';         //roles
$signatures['24c0a757bde1ab069a29d4a11f7e32b7'] = 'latest';         //settings
$signatures['80cd57612b7b3b84006041ad45aad0bc'] = 'latest';     //status
$signatures['450aa4f7d58b3299c351af9792884b53'] = 'latest';     //tasks
$signatures['109b379aa2ccc978ea11e9313b8efe7e'] = 'latest';     //time
$signatures['e44f7363fbcdec8b0a22c743acde4f90'] = 'latest';         //types
$signatures['6d0ad9caace1ac137d98732d0d8dd7ce'] = 'latest';     //users_history
$signatures['705e271e745223739b914c8b76d107cf'] = 'latest';     //types_history
$signatures['a2315fb06d5d24c1fd2e287ccf959141'] = 'latest';         //users

$signatures['1731fe5490a1d9a111da01471928bcd7'] = 'v0.1.4';//contracts
$signatures['547b7878e4287fdac66ec6d802afb621'] = 'v0.1.4';//entitleddays*/

if (function_exists('apache_get_modules')) {
  $modules = apache_get_modules();
  $mod_rewrite = in_array('mod_rewrite', $modules);
} else {
  $mod_rewrite =  getenv('HTTP_MOD_REWRITE')=='On' ? true : false ;
}

$allow_overwrite =  getenv('ALLOW_OVERWRITE');
$mod_rewrite =  getenv('HTTP_MOD_REWRITE');
$server_software = getenv('SERVER_SOFTWARE');

if ($mod_rewrite == "") $mod_rewrite = '<b>.htaccess not visited</b>';
if ($allow_overwrite == "") $allow_overwrite = '<b>Off</b>';

//Check if we can access to the configuration file
$dbExists = FALSE;
$rs = NULL;
$pathConfigFile = realpath(join(DIRECTORY_SEPARATOR, array('application', 'config', 'database.php')));
$configFileExists = file_exists($pathConfigFile);
if ($configFileExists ) {
    include $pathConfigFile;
    //Try to connect to database
    $dbConn = @mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']);
    if ($dbConn) {
        //Try to select LMS database
        if (@mysql_select_db($db['default']['database'], $dbConn)) {
            //Try to get the signature of the schema
            $dbExists = TRUE;
            $sql = "SELECT TABLE_NAME, MD5(GROUP_CONCAT(CONCAT(TABLE_NAME, COLUMN_NAME, COALESCE(COLUMN_DEFAULT, ''), IS_NULLABLE, COLUMN_TYPE, COALESCE(COLLATION_NAME, '')) SEPARATOR ', ')) AS signature"
                        . " FROM information_schema.columns"
                        . " WHERE table_schema =  DATABASE()"
                        . " GROUP BY TABLE_NAME";
            $rs = @mysql_query($sql, $dbConn);
        }
    }
}
?>
<html>
    <head>
    <title>LMS Requirements</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <meta charset="UTF-8">
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    
        <div class="container-fluid">

            <h1>LMS Requirements</h1>
            
            <h2>Web Server</h2>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                      <th>Requirement</th>
                      <th>Value / Description</th>
                    </tr>
                  </thead>
                  <tbody>
                      <tr><td><i class="icon-info-sign"></i>&nbsp;Server software</td><td><?php echo $server_software; ?></td></tr>

                      <tr><td><?php if (strtolower($allow_overwrite) == "on") {?><i class="icon-ok-sign"></i><?php } else { ?><i class="icon-remove-sign"></i><?php } ?>
                      &nbsp;Allow overwrite (.htaccess files)</td><td><?php echo $allow_overwrite; ?> (used for cool URLs).</td></tr>

                      <tr><td><?php if (strtolower($mod_rewrite) == "on") {?><i class="icon-ok-sign"></i><?php } else { ?><i class="icon-remove-sign"></i><?php } ?>
                      &nbsp;Apache module rewrite (mod_rewrite)</td><td><?php echo $mod_rewrite; ?> (used for cool URLs).</td></tr>

                      <?php if (extension_loaded('openssl')) {?>
                      <tr><td><i class="icon-ok-sign"></i>&nbsp;openssl is LOADED</td>
                      <?php } else { ?>
                      <tr><td><i class="icon-exclamation-sign"></i>&nbsp;openssl IS NOT LOADED.</td>
                      <?php } ?><td>PHP Extension openssl speeds up the log in page</td></tr>
                      
                      <?php if (extension_loaded('ldap')) {?>
                      <tr><td><i class="icon-ok-sign"></i>&nbsp;ldap is LOADED</td>
                      <?php } else { ?>
                      <tr><td><i class="icon-exclamation-sign"></i>&nbsp;ldap IS NOT LOADED</td>
                      <?php } ?><td>PHP Extension ldap is optional and allows you to use LDAP for authentication.</td></tr>
                      
                      <?php if (extension_loaded('zip')) {?>
                      <tr><td><i class="icon-ok-sign"></i>&nbsp;zip is LOADED</td>
                      <?php } else { ?>
                      <tr><td><i class="icon-exclamation-sign"></i>&nbsp;zip IS NOT LOADED</td>
                      <?php } ?><td>PHP Extension zip allows you to use the export to Excel feature.</td></tr>
                      
                      <?php if (extension_loaded('xml')) {?>
                      <tr><td><i class="icon-ok-sign"></i>&nbsp;xml is LOADED</td>
                      <?php } else { ?>
                      <tr><td><i class="icon-exclamation-sign"></i>&nbsp;xml IS NOT LOADED</td>
                      <?php } ?><td>PHP Extension xml allows you to use the export to Excel feature.</td></tr>
                      
                      <?php if (extension_loaded('gd')) {?>
                      <tr><td><i class="icon-ok-sign"></i>&nbsp;gd is LOADED</td>
                      <?php } else { ?>
                      <tr><td><i class="icon-exclamation-sign"></i>&nbsp;gd IS NOT LOADED</td>
                      <?php } ?><td>PHP Extension gd2 allows you to use the export to Excel feature.</td></tr>
                      
                      <?php if (extension_loaded('mysqli')) {?>
                      <tr><td><i class="icon-ok-sign"></i>&nbsp;mysqli is LOADED</td>
                      <?php } else { ?>
                      <tr><td><i class="icon-exclamation-sign"></i>&nbsp;mysqli IS NOT LOADED</td>
                      <?php } ?><td>mysqli is the recommended database driver.</td></tr>

                      
                  </tbody>
            </table>

            <h2>Database</h2>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                      <th>Requirement</th>
                      <th>Value / Description</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php if ($configFileExists) { ?><tr><td><i class="icon-ok-sign"></i>&nbsp;Configuration file</td><td>Found</td></tr>
                      <?php } else { ?><tr><td><i class="icon-remove-sign"></i>&nbsp;Configuration file</td><td>Not Found</td></tr>
                      <?php } ?>
                      
                      <?php if ($dbConn) { ?><tr><td><i class="icon-ok-sign"></i>&nbsp;Database connection</td><td>OK</td></tr>
                      <?php } else { ?><tr><td><i class="icon-remove-sign"></i>&nbsp;Database connection</td><td>Error</td></tr>
                      <?php } ?>
                      
                      <?php if ($dbExists) { ?><tr><td><i class="icon-ok-sign"></i>&nbsp;Database name</td><td>Found</td></tr>
                      <?php } else { ?><tr><td><i class="icon-remove-sign"></i>&nbsp;Database name</td><td>Doesn't exist</td></tr>
                      <?php } ?>

                      <?php if ($rs) { ?><tr><td><i class="icon-ok-sign"></i>&nbsp;Database query</td><td>OK</td></tr>
                      <?php } else { ?><tr><td><i class="icon-remove-sign"></i>&nbsp;Database query</td><td>Error</td></tr>
                      <?php } ?>
                  </tbody>
            </table>

            <?php /* ?>
            <h2>Schema</h2>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                      <th>Requirement</th>
                      <th>Value / Description</th>
                    </tr>
                  </thead>
                  <tbody>                      
                        <?php if ($rs) {
                        while ($row = mysql_fetch_assoc($rs)) {
                            if (array_key_exists($row['signature'], $signatures)) {
                                if ($signatures[$row['signature']] != 'latest') { ?>
                                <tr><td><i class="icon-remove-sign"></i>&nbsp;<?php echo $row['TABLE_NAME']; ?></td><td>is not patched at the latest version (<?php echo $signatures[$row['signature']]; ?>)</td></tr>
                               <?php } else { ?>
                                <tr><td><i class="icon-ok-sign"></i>&nbsp;<?php echo $row['TABLE_NAME']; ?></td><td>OK</td></tr>
                                <?php } ?>
                           <?php } else { ?>
                                <tr><td><i class="icon-exclamation-sign"></i>&nbsp;<?php echo $row['TABLE_NAME']; ?></td><td>Modifications made to table structure</td></tr>
                        <?php }
                            }
                        } else { ?>
                                <tr><td colspan="2"><i>Impossible to query database</i></td></tr>
                        <?php } ?>
                  </tbody>
            </table>
            <?php */ ?>
        </div>
    </body>
</html>