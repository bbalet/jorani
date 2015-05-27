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
        {Firstname} {Lastname} requests a leave. Below, the <a href="{BaseUrl}leaves/{LeaveId}">details</a> :
        <table border="0">
            <tr>
                <td>From &nbsp;</td><td>{StartDate}&nbsp;({StartDateType})</td>
            </tr>
            <tr>
                <td>To &nbsp;</td><td>{EndDate}&nbsp;({EndDateType})</td>
            </tr>
            <tr>
                <td>Type &nbsp;</td><td>{Type}</td>
            </tr>
            <tr>
                <td>Reason &nbsp;</td><td>{Reason}</td>
            </tr>
            <tr>
                <td><a href="{BaseUrl}requests/accept/{LeaveId}">Accept</a> &nbsp;</td><td><a href="{BaseUrl}requests/reject/{LeaveId}">Reject</a></td>
            </tr>
        </table>
<br />
You can check the <a href="{BaseUrl}requests/counters/{UserId}">leave balance</a> before validating the leave request.

    </body>
</html>
