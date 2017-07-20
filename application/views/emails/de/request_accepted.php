<?php
/**
 * Email template.You can change the content of this template
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
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
            <tr>
                <td>Last comment &nbsp;</td><td>{Comments}</td>
            </tr>
        </table>
    </body>
</html>
