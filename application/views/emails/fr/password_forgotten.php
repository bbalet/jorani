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
        <p>Veuillez utiliser ces identifiants pour <a href="{BaseURL}">vous connecter à l'application</a> :</p>
        <table>
            <tr>
                <td>Identifiant</td><td>{Login}</td>
            </tr>
            <tr>
                <td>Mot de passe</td><td>{Password}</td>
            </tr>            
        </table>
        <p>Une fois connecté, vous pouvez modifier votre mot de passe comme expliqué <a href="http://www.leave-management-system.org/how-to-change-my-password.html" title="Lien vers la documentation" target="_blank">dans cet article (en anglais)</a>.</p>
        <hr>
        <h5>*** Ceci est un message généré automatiquement, veuillez ne pas répondre à ce message ***</h5>
    </body>
</html>
