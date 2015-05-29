<?php
/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
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
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 */

define('BASEPATH','.');//Make this script works with nginx
//Configuration values are taken from application/config/email.php

//-----------------------------------------------------------------

//Please enter a valid target email address. A test email will be sent here
define('EMAIL_ADDRESS','');

//-----------------------------------------------------------------

?>
<html>
    <head>
		<title>Jorani Email Configuration</title>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<meta charset="UTF-8">
		<link rel="icon" type="image/x-icon" href="favicon.ico" sizes="32x32">
		<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<script type="text/javascript" src="assets/js/jquery-1.11.0.min.js"></script>
    </head>
    <body>
    
        <div class="container-fluid">

            <h1>Test of your e-mail configuration</h1>

<?php			
//Check if we can access to the configuration file
$pathConfigFile = realpath(join(DIRECTORY_SEPARATOR, array('application', 'config', 'email.php')));
$configFileExists = file_exists($pathConfigFile);

if (EMAIL_ADDRESS == '') {
	echo '<b>ERROR:</b> Please provide a valid e-mail address in testmail.php.<br />' . PHP_EOL;
} else {
	if ($configFileExists) {
		include $pathConfigFile;
			try {
				//Include shipped PHPMailer library
				$phpmailerLib = realpath(join(DIRECTORY_SEPARATOR, array('application', 'third_party', 'PHPMailer', 'PHPMailerAutoload.php')));
				require_once $phpmailerLib;
				$mail = new PHPMailer(true); //true => throw exceptions on error
				$mail->SMTPDebug = 2;  		 //Debug informations
				$mail->Debugoutput = function($str, $level) { echo nl2br($str); };
				
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
				$mail->SetFrom("test@jorani.org","Jorani application");
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
			<li>The STMP port may be blocked by your organization/server's security policy.</li>
			<li>Some antivirus block STMP port.</li>
			<li>Some SMTP server require the application server (eg Jorani) to be whitelisted.</li>
			<li>Your webhosting company may forbid email functions.</li>
			<li>Maybe that the emails are sent but put into SPAM folder.</li>
		</ul>
        </div>
    </body>
</html>
