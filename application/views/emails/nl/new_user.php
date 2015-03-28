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
<html lang="nl">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
    </head>
    <body>
        <h3>{Title}</h3>
        Welkom bij Jorani {Firstname} {Lastname}. Hieronder vindt u de gevens om <a href="{BaseURL}">in te loggen in het systeem</a> :
        <table border="0">
            <tr>
                <td>Gebruikersnaam</td><td>{Login}</td>
            </tr>
            <tr>
                <?php if ($this->config->item('ldap_enabled') == FALSE) { ?>
                <td>Wachtwoord</td><td>{Password}</td>
                <?php } else { ?>
                <td>Password</td><td><i>Het wachtwoord heeft u nodig om een sessie te starten via uw operating system (Windows, Linux, etc.).</i></td>
                <?php } ?>
            </tr>            
        </table>
        <?php if ($this->config->item('ldap_enabled') == FALSE) { ?>
        Nadat u bent ingelogd, kunt u het wachtwoord wijzigen (zie <a href="http:/jorani.org/how-to-change-my-password.html" title="Link to documentation" target="_blank">hier</a> voor nadere uitleg).
        <?php } ?>
    </body>
</html>
