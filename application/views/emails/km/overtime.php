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
<html lang="km">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
    </head>
    <body>
        <h3>{Title}</h3>
        {Firstname} {Lastname} ស្នើសុំថែមមួយម៉ោង។ ខាងក្រោមជាពត៏មានលម្អិត :
        <table border="0">
            <tr>
                <td>កាលបរិច្ឆេទ &nbsp;</td><td>{Date}</td>
            </tr>
            <tr>
                <td>រយៈពេល &nbsp;</td><td>{Duration}</td>
            </tr>
            <tr>
                <td>មូលហេតុ &nbsp;</td><td>{Cause}</td>
            </tr>
        </table>
        <a href="{UrlAccept}">ទទួលយកបាន</a>
        <a href="{UrlReject}">បដិសេធចោល</a>
    </body>
</html>
