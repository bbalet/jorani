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
<html lang="km">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
    </head>
    <body>
        <h3>{Title}</h3>
        {Firstname} {Lastname} ស្នើសុំចាកចេញមួយខាងក្រោមសេចក្ដីលម្អិត :
        <table border="0">
            <tr>
                <td>មកពី &nbsp;</td><td>{StartDate}</td>
            </tr>
            <tr>
                <td>ទៅកាន់ &nbsp;</td><td>{EndDate}</td>
            </tr>
            <tr>
                <td>Type &nbsp;</td><td>{Type}</td>
            </tr>
            <tr>
                <td>Reason &nbsp;</td><td>{Reason}</td>
            </tr>
        </table>
        <a href="{UrlAccept}">ទទួលយកបាន</a>
        <a href="{UrlReject}">បដិសេធចោល</a>
    </body>
</html>
