<?php
/**
 * Email template.You can change the content of this template
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
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
        Nadat u bent ingelogd, kunt u het wachtwoord wijzigen (zie <a href="http://jorani.org/how-to-change-my-password.html" title="Link to documentation" target="_blank">hier</a> voor nadere uitleg).
        <?php } ?>
    </body>
</html>
