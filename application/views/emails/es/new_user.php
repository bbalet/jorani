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
        Bienvenido a Jorani {Firstname} {Lastname}. Por favor, use estas credenciales para <a href="{BaseURL}">acceder al sistema</a> :
        <table border="0">
            <tr>
                <td>Usuario</td><td>{Login}</td>
            </tr>
            <tr>
                <?php if ($this->config->item('ldap_enabled') == FALSE) { ?>
                <td>Contraseña</td><td>{Password}</td>
                <?php } else { ?>
                <td>Contraseña</td><td><i>La contraseña que utiliza para abrir una sesión en su sistema operativo (Windows, Linux, etc.).</i></td>
                <?php } ?>
            </tr>            
        </table>
        <?php if ($this->config->item('ldap_enabled') == FALSE) { ?>
        Una vez conectado, puede cambiar su contraseña, pulsando  <a href="http://jorani.org/how-to-change-my-password.html" title="Link to documentation" target="_blank">aqui</a>.
        <?php } ?>
    </body>
</html>
