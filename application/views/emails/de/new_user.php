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
 */

    //You can change the content of this template
?>
<html lang="en">
    <body>
        <h3>{Title}</h3>
        Willkommen bei Jorani, {Firstname} {Lastname}. Bitte nutzen Sie den angegebenen Usernamen und Passwort um sich <a href="{BaseURL}">ins System einzuloggen!</a> :
        <table border="0">
            <tr>
                <td>Login</td><td>{Login}</td>
            </tr>
            <tr>
                <?php if ($this->config->item('ldap_enabled') == FALSE) { ?>
                <td>Passwort</td><td>{Password}</td>
                <?php } else { ?>
                <td>Passwort</td><td><i>Das Passwort entspricht dem Anmeldepasswort Ihres Computers (Windows, Linux, etc.) bzw. einem anderen Ihrer genutzten Dienste (Exchange, Sharepoint, etc.).</i></td>
                <?php } ?>
            </tr>            
        </table>
        <?php if ($this->config->item('ldap_enabled') == FALSE) { ?>
        <a href="http:/jorani.org/how-to-change-my-password.html" title="Link to documentation" target="_blank">Hier</a> wird beschrieben können Sie Ihr Passwort ändern sobald Sie eingeloggt sind.
        <?php } ?>
    </body>
</html>
