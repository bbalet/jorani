<?php
/**
 * Email template.You can change the content of this template
 * @copyright  Copyright (c) 2014-2019 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<html lang="fr">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
        <style>
            table {width:50%;margin:5px;border-collapse:collapse;}
            table, th, td {border: 1px solid black;}
            th, td {padding: 20px;}
            h5 {color:red;}
        </style>
    </head>
    <body>
        <h3>{Title}</h3>
        <p>{Firstname} {Lastname} einen antrag auf telearbeit bei Ihnen einreicht. Hier sind die <a href="{BaseUrl}teleworks/requests/{TeleworkId}">details</a> :</p>
        <table>
            <tr>
                <td>Von &nbsp;</td><td>{StartDate}&nbsp;({StartDateType})</td>
            </tr>
            <tr>
                <td>Bis &nbsp;</td><td>{EndDate}&nbsp;({EndDateType})</td>
            </tr>
            <tr>
                <td>Dauer &nbsp;</td><td>{Duration}</td>
            </tr>
            <tr>
                <td>Grund &nbsp;</td><td>{Reason}</td>
            </tr>
            <tr>
              <td>Letzter kommentar &nbsp;</td><td>{Comments}</td>
            </tr>
            <tr>
                <td><a href="{BaseUrl}teleworkrequests/accept/{TeleworkId}">Akzeptieren</a> &nbsp;</td>
                <td><a href="{BaseUrl}teleworkrequests?rejected={TeleworkId}">Ablehnen</a></td>
            </tr>
        </table>
        <br />
        <p>Sie können prüfen <a href="{BaseUrl}hr/teleworks/{UserId}">status der telearbeit</a> bevor sie diesen antrag genehmigen.</p>
        <hr>
        <h5>*** Dies ist eine automatisch generierte Nachricht, bitte antworten Sie nicht auf diese Nachricht ***</h5>
    </body>
</html>
