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
<html>
    <body>
        <h3>{Title}</h3>
        Welcome to LMS {Firstname} {Lastname}. Please use these credentials to <a href="{BaseURL}">login to the system</a> :
        <table border="0">
            <tr>
                <td>Login</td><td>{Login}</td>
            </tr>
            <tr>
                <td>Password</td><td>{Password}</td>
            </tr>            
        </table>
        Once connected, you can change your password, as explained <a href="https://github.com/bbalet/lms/wiki/how-to-change-my-password-%3F" target="_blank">here</a>.
    </body>
</html>
