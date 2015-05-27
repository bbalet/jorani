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
<html lang="fr">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
        <style>
            table {width:50%;margin:5px;border-collapse:collapse;}
            table, th, td {border: 1px solid black;}
            th, td {padding: 20px;}
            h5 {color:red;}
        </style>
    </head>
    <body>
        <h3>{Title}</h3>
        <p>Bienvenue dans Jorani {Firstname} {Lastname}. Veuillez utiliser ces identifiants pour <a href="{BaseURL}">vous connecter à l'application</a> :</p>
        <table>
            <tr>
                <td>Identifiant</td><td>{Login}</td>
            </tr>
            <tr>
                <?php if ($this->config->item('ldap_enabled') == FALSE) { ?>
                <td>Mot de passe</td><td>{Password}</td>
                <?php } else { ?>
                <td>Mot de passe</td><td><i>Le mot de passe que vous utilisez pour ouvrir une session sur votre système d'exploitation (Windows , Linux , etc. ).</i></td>
                <?php } ?>
            </tr>            
        </table>
        <?php if ($this->config->item('ldap_enabled') == FALSE) { ?>
        <p>Une fois connecté, vous pouvez modifier votre mot de passe comme expliqué <a href="http://fr.jorani.org/utilisation/comment-modifier-mon-mot-de-passe.html" title="Lien vers la documentation" target="_blank">dans cet article</a>.</p>
        <?php } ?>
        <hr>
        <h5>*** Ceci est un message généré automatiquement, veuillez ne pas répondre à ce message ***</h5>
    </body>
</html>
