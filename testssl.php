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
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */

define('BASEPATH', '.'); //Make this script works with nginx
define('KEY_SIZE', 1024);   //Change the RSA key size

//You can define additionnal constants (please read phpseclib doc), for example:
//CRYPT_RSA_MODE to 1 if you want to test the speed of Internal mode (without OpenSSL)
//CRYPT_RSA_EXPONENT to 65537
//CRYPT_RSA_SMALLEST_PRIME to 64 and multi-prime RSA is used

//-----------------------------------------------------------------
?>
<html>
    <head>
        <title>Jorani OpenSSL Configuration</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
        <link rel="icon" type="image/x-icon" href="favicon.ico" sizes="32x32">
        <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="assets/js/jquery-1.11.0.min.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <ul class="nav nav-pills">
                <li><a href="requirements.php">Requirements</a></li>
                <li><a href="testmail.php">Email</a></li>
                <li><a href="testldap.php">LDAP</a></li>
                <li class="active"><a href="#">SSL</a></li>
                <li><a href="opcache.php">Opcache</a></li>
              </ul>
            <h1>Test of your OpenSSL setup</h1>

            <p>This page will help you to check your OpenSSL setup and to generate a <a href="#pair">private and public key pair</a>.</p>
            
            <p>The public and private keys are generated on the fly each time the page is loaded.</p>
            
            <h2>PHP Security library</h2>
            
            <table class="table table-bordered table-hover table-condensed">
                <thead>
                    <tr>
                      <th>Requirement</th>
                      <th>Value / Description</th>
                    </tr>
                  </thead>
                  <tbody>
<?php

$pathLibFile = realpath(join(DIRECTORY_SEPARATOR, array('application', 'third_party', 'phpseclib', 'vendor', 'autoload.php')));
include $pathLibFile;

$cnfFile = realpath(join(DIRECTORY_SEPARATOR, array('application', 'third_party', 'phpseclib', 'phpseclib', 'openssl.cnf')));
$privateKeyFile = realpath(join(DIRECTORY_SEPARATOR, array('assets', 'keys', 'private.pem')));
$publicKeyFile = realpath(join(DIRECTORY_SEPARATOR, array('assets', 'keys', 'public.pem')));

if (file_exists($privateKeyFile)) $privateKey=file_get_contents($privateKeyFile); else $privateKey = "";
if (file_exists($publicKeyFile)) $publicKey=file_get_contents($publicKeyFile); else $publicKey = "";

ob_start();
@phpinfo();
$content = ob_get_contents();
ob_end_clean();
preg_match_all('#OpenSSL (Header|Library) Version(.*)#im', $content, $matches);
$versions = array();
if (!empty($matches[1])) {
    $len = count($matches[1]);
    for ($i = 0; $i < $len; $i++) {
        $fullVersion = trim(str_replace('=>', '', strip_tags($matches[2][$i])));
        // Remove letter part in OpenSSL version
        if (!preg_match('/(\d+\.\d+\.\d+)/i', $fullVersion, $m)) {
            $versions[$matches[1][$i]] = $fullVersion;
        } else {
            $versions[$matches[1][$i]] = $m[0];
        }
    }
}

echo "<tr><td>PHP_VERSION</td><td>" . (version_compare(PHP_VERSION, '4.2.0', '>=')? '>=4.2.0' : '<4.2.0')  . '</td></tr>';
echo "<tr><td>openssl</td><td>" . ((extension_loaded('openssl'))? 'extension loaded' : 'extension not loaded')  . '</td></tr>';
echo "<tr><td>openssl_pkey_get_details</td><td>" . ((function_exists('openssl_pkey_get_details'))? 'exists' : 'doesn\'t exist')  . '</td></tr>';
echo "<tr><td>Private key</td><td>" . (($privateKey!='')? 'Found' : 'Not found')  . '</td></tr>';
echo "<tr><td>Public key</td><td>" . (($publicKey!='')? 'Found' : 'Not found')  . '</td></tr>';

echo "<tr><td>OpenSSL Library</td><td>" . ((isset($versions['Library']))? $versions['Library'] : 'Not found')  . '</td></tr>';
echo "<tr><td>OpenSSL Header</td><td>" . ((isset($versions['Header']))? $versions['Header'] : 'Not found')  . '</td></tr>';

$rsa = new \phpseclib\Crypt\RSA();
echo "<tr><td>CRYPT_RSA_MODE</td><td>" . ((CRYPT_RSA_MODE==1)? 'MODE_INTERNAL' : 'MODE_OPENSSL')  . '</td></tr>';

$rsa->setEncryptionMode(phpseclib\Crypt\RSA::ENCRYPTION_PKCS1);
$plaintext = 'Jorani is the best open source Leave Management System';
$rsa->loadKey($publicKey);
$ciphertext = $rsa->encrypt($plaintext);

$rsa->loadKey($privateKey, phpseclib\Crypt\RSA::PRIVATE_FORMAT_PKCS1);
$time_start = microtime(true);
echo "<tr><td>Decrypted message</td><td>" . $rsa->decrypt($ciphertext)  . '</td></tr>';
$time_end = microtime(true);
$time = $time_end - $time_start;
echo "<tr><td>Decryption speed</td><td>" . $time  . '</td></tr>';

//Generate public and private keys for a single usage
extract($rsa->createKey(KEY_SIZE));
?>
                  </tbody>
            </table>
            
            <h2 id="pair">Private and public key pair</h2>
            
            <p>This section will help you to create <code>assets/keys/private.pem</code> and <code>assets/keys/public.pem</code> files.
             Beware that is a pair of keys that must be set together (i.e. you must update the both files at the same time with the corresponding content).</p>
            
            <h3>Private Key</h3>
            
            <p>You can copy/paste the content below into <code>assets/keys/private.pem</code>.</p>
            
<div class="row-fluid">
    <div class="span6">
<pre><?php echo $privatekey; ?></pre>
    </div>
    <div class="span6"></div>
</div>

            <h3>Public Key</h3>
            
            <p>You can copy/paste the content below into <code>assets/keys/public.pem</code>.</p>
            
<div class="row-fluid">
    <div class="span6">       
<pre><?php echo $publickey; ?></pre>
    </div>
    <div class="span6"></div>
</div>
        </div>
    </body>
</html>
