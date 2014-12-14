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
 * along with lms.  If not, see <http://www.gnu.org/licenses/>.
 */

    //You can change the content of this template
?>
<html lang="fr">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
    </head>
    <body>
        <h3>{Title}</h3>
        Bienvenue dans Jorani {Firstname} {Lastname}. Veuillez utiliser ces identifiants pour <a href="{BaseURL}">vous connecter à l'application</a> :
        <table border="0">
            <tr>
                <td>Identifiant</td><td>{Login}</td>
            </tr>
            <tr>
                <td>Mot de passe</td><td>{Password}</td>
            </tr>            
        </table>
        Une fois connecté, vous pouvez modifier votre mot de passe comme expliqué <a href="http://www.leave-management-system.org/how-to-change-my-password.html" title="Lien vers la documentation" target="_blank">dans cet article (en anglais)</a>.
    </body>
</html>
