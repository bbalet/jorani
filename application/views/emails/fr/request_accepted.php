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

//You can change the content of this template
?>
<html lang="fr">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
    </head>
    <body>
        <h3>{Title}</h3>
        Cher {Firstname} {Lastname}, <br />
        <br />
        La demande d'absence que vous avez soumise a été acceptée. Voici les détails :
        <table border="0">
            <tr>
                <td>Du &nbsp;</td><td>{StartDate}</td>
            </tr>
            <tr>
                <td>Au &nbsp;</td><td>{EndDate}</td>
            </tr>            
        </table>
    </body>
</html>