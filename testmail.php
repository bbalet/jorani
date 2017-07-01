<?php
/**
 * This diagnostic page helps you to check email settings.
 * You can use this script in order to try to send an email with a debug trace.
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.0
 */

define('BASEPATH', '.'); //Make this script works with nginx
$env = is_null(getenv('CI_ENV'))?'':getenv('CI_ENV');
//Configuration values are taken from application/config/(env)/email.php
//-----------------------------------------------------------------
//Please enter a valid target email address. A test email will be sent here
define('EMAIL_ADDRESS', '');

//-----------------------------------------------------------------
?>
<html>
    <head>
        <title>Jorani Email Configuration</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
        <link rel="icon" type="image/x-icon" href="favicon.ico" sizes="32x32">
        <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="assets/js/jquery-2.2.0.min.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <ul class="nav nav-pills">
                <li><a href="home" title="login to Jorani"><i class="icon-home"></i></a></li>
                <li><a href="requirements.php">Requirements</a></li>
                <li class="active"><a href="#">Email</a></li>
                <li><a href="testldap.php">LDAP</a></li>
                <li><a href="testssl.php">SSL</a></li>
                <li><a href="testoauth2.php">OAuth2</a></li>
                <li><a href="opcache.php">Opcache</a></li>
              </ul>
            <h1>Test of your e-mail configuration</h1>

<?php
//Check if we can access to the configuration file
$pathCIConfigFile = realpath(join(DIRECTORY_SEPARATOR, array('application', 'config', $env, 'config.php')));
$pathConfigFile = realpath(join(DIRECTORY_SEPARATOR, array('application', 'config', $env, 'email.php')));
$configCIFileExists = file_exists($pathCIConfigFile);
$configFileExists = file_exists($pathConfigFile);

if (EMAIL_ADDRESS == '') {
    echo '<b>ERROR:</b> Please provide a valid e-mail address in testmail.php.<br />' . PHP_EOL;
} else {
    if ($configFileExists && $configCIFileExists) {
        include $pathCIConfigFile;
        include $pathConfigFile;
        try {
            //Include shipped PHPMailer library
            $phpmailerLib = realpath(join(DIRECTORY_SEPARATOR, array('application', 'third_party', 'PHPMailer', 'PHPMailerAutoload.php')));
            require_once $phpmailerLib;
            $mail = new PHPMailer(true); //true => throw exceptions on error
            $mail->SMTPDebug = 2;     //Debug informations
            $mail->Debugoutput = function($str, $level) {
                echo nl2br($str);
            };

            //Test if we use SMTP protocol
            if (strcasecmp($config['protocol'], 'smtp') == 0) {
                echo '<b>INFO:</b> Selecting SMTP Protocol.<br />' . PHP_EOL;
                $mail->IsSMTP();
                $mail->Host = $config['smtp_host'];
                $mail->Port = $config['smtp_port'];
                if (strpos($config['smtp_port'], 'gmail') !== false) {
                    echo '<b>INFO:</b> Using GMAIL.<br />' . PHP_EOL;
                }
            }

            //Test if we use sendmail
            if (strcasecmp($config['protocol'], 'sendmail') == 0) {
                echo '<b>INFO:</b> Selecting sendmail.<br />' . PHP_EOL;
                $mail->IsSendmail();
                if (array_key_exists('mailpath', $config)) {
                    if ($config['mailpath'] != '') {
                        echo '<b>INFO:</b> Changing sendmail path.<br />' . PHP_EOL;
                        ini_set('sendmail_path', $config['mailpath']);
                    }
                }
            }

            //GMAIL requires _smtp_auth set to TRUE
            if ($config['_smtp_auth'] == TRUE) {
                echo '<b>INFO:</b> SMTP with authentication.<br />' . PHP_EOL;
                $mail->SMTPAuth = true;
                $mail->Username = $config['smtp_user'];
                $mail->Password = $config['smtp_pass'];
            }

            //GMAIL requires smtp_crypto set to tls
            if ($config['smtp_crypto'] != '') {
                echo '<b>INFO:</b> SMTP with crypto.<br />' . PHP_EOL;
                $mail->SMTPSecure = $config['smtp_crypto'];
            }

            $mail->CharSet = $config['charset'];
            $mail->AddAddress(EMAIL_ADDRESS, "Test e-mail");
            $mail->SetFrom($config['from_mail'], "Jorani application");
            $mail->Subject = "Test Message";
            $mail->Body = 'This is a test.';
            $mail->Send();

            echo '<b>INFO:</b> Message sent.<br />' . PHP_EOL;
        } catch (phpmailerException $e) {
            echo '<b>ERROR:</b> PHPMailer has encountered an error.<br />' . PHP_EOL;
            $text = $e->errorMessage();
            $text = iconv(mb_detect_encoding($text, mb_detect_order(), true), "UTF-8", $text);
            echo $text . PHP_EOL;
        } catch (Exception $e) {
            echo '<b>ERROR:</b> Unexpected error.<br />' . PHP_EOL;
            $text = $e->getMessage();
            $text = iconv(mb_detect_encoding($text, mb_detect_order(), true), "UTF-8", $text);
            echo $text . PHP_EOL;
        }
    } else {
        echo '<b>ERROR:</b> The configuration doesn\'t exist.<br />' . PHP_EOL;
    }
}
?>
            <h3>Troubleshooting</h2>
                <p>In case of error, here are some additional steps:</p>
                <ul>
                    <li>Check the configuration with your IT Admin team.</li>
                    <li>If you are using GMAIL, please read <a target="_blank" href="https://support.google.com/mail/answer/78775?hl=en">this article</a>.</li>
                    <li>The STMP port may be blocked by your organization/server's security policy (firewall, etc.).</li>
                    <li>When running SELinux, the webserver is blocked by default (it cannot send e-mails or open a network connection). Please consider unblocking it:
                        <p>
<pre>
$ setsebool -P httpd_can_sendmail 1
$ setsebool -P httpd_can_network_connect 1
</pre>
                        </p>
                    </li>
                    <li>Some e-mail servers (eg Office 360) require to set a valid sender e-mail. Update <tt>config/config.php</tt>
                        <p>
                            <code>$config['from_mail'] = 'do.not@reply.me';</code>
                        </p>
                    </li>
                    <li>Some antivirus block STMP port by default.</li>
                    <li>Some SMTP server require the application server sending emails (i.e. Jorani) to be whitelisted (on the SMTP server).</li>
                    <li>Your webhosting company may forbid email functions.</li>
                    <li>Maybe that the emails are sent but put into SPAM folder.</li>
                </ul>
        </div>
    </body>
</html>
