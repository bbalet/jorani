<?php
/**
 * Email template.You can change the content of this template
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<html lang="en">
    <body>
        <h3>{Title}</h3>
        Bitte benutzen Sie diese Anmeldedaten um sich <a href="{BaseURL}">ins System einzuloggen</a> :
        <table border="0">
            <tr>
                <td>Username</td><td>{Login}</td>
            </tr>
            <tr>
                <td>Passwort</td><td>{Password}</td>
            </tr>            
        </table>
        <a href="http://jorani.org/how-to-change-my-password.html" title="Link to documentation" target="_blank">Hier</a> wird beschrieben können Sie Ihr Passwort ändern sobald Sie eingeloggt sind..
    </body>
</html>
