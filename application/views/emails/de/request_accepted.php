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
        Lieber {Firstname} {Lastname}, <br />
        <br />
        Der beantragte Urlaub wurde genehmigt. Hierzu die Details :
        <table border="0">
            <tr>
                <td>Von &nbsp;</td><td>{StartDate}</td>
            </tr>
            <tr>
                <td>Bis &nbsp;</td><td>{EndDate}</td>
            </tr>
            <tr>
                <td>Art &nbsp;</td><td>{Type}</td>
            </tr>
            <tr>
                <td>Begründung &nbsp;</td><td>{Reason}</td>
            </tr>
        </table>
    </body>
</html>
