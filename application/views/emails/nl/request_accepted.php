<?php
/**
 * Email template.You can change the content of this template
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<html lang="nl">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
    </head>
    <body>
        <h3>{Title}</h3>
        Beste {Firstname} {Lastname}, <br />
        <br />
        Het afwezigheidsverzoek dat u heeft ingediend is geaccepteerd. Hieronder vindt u de details :
        <table border="0">
            <tr>
                <td>Van &nbsp;</td><td>{StartDate}&nbsp;({StartDateType})</td>
            </tr>
            <tr>
                <td>Tot &nbsp;</td><td>{EndDate}&nbsp;({EndDateType})</td>
            </tr>
            <tr>
                <td>Type &nbsp;</td><td>{Type}</td>
            </tr>
            <tr>
                <td>Reden &nbsp;</td><td>{Cause}</td>
            </tr>
            <tr>
                <td>Last comment &nbsp;</td><td>{Comments}</td>
            </tr>
        </table>
    </body>
</html>
