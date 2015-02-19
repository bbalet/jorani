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
<html lang="es">
    <body>
        <h3>{Title}</h3>
        {Firstname} {Lastname} solicitud de una licencia. A continuación, el <a href="{BaseUrl}leaves/{LeaveId}">detalle</a> :
        <table border="0">
            <tr>
                <td>Desde &nbsp;</td><td>{StartDate}</td>
            </tr>
            <tr>
                <td>Hasta &nbsp;</td><td>{EndDate}</td>
            </tr>
            <tr>
                <td>Tipo &nbsp;</td><td>{Type}</td>
            </tr>
            <tr>
                <td>Motivo &nbsp;</td><td>{Reason}</td>
            </tr>
            <tr>
                <td><a href="{BaseUrl}requests/accept/{LeaveId}">Aceptar</a> &nbsp;</td><td><a href="{BaseUrl}requests/reject/{LeaveId}">Rechazar</a></td>
            </tr>
        </table>
<br />
Puede comprobar el <a href="{BaseUrl}requests/counters/{UserId}">balance de permisos</a> antes de la solicitud del permiso.

    </body>
</html>
