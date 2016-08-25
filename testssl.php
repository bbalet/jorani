<?php
/**
 * This diagnostic page helps you to check openssl setup and to generate a pair of keys.
 * Please note that the configuration is not exposed by this page and that the pair of keys
 * is calculated each time the page is reloaded.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.3
 */

define('BASEPATH', '.'); //Make this script works with nginx
define('KEY_SIZE', 1024);   //Change the RSA key size

//You can define additionnal constants (please read phpseclib doc), for example:
//CRYPT_RSA_MODE to 1 if you want to test the speed of Internal mode (without OpenSSL)
//CRYPT_RSA_EXPONENT to 65537
//CRYPT_RSA_SMALLEST_PRIME to 64 and multi-prime RSA is used
?>
<html>
    <head>
        <title>Jorani OpenSSL Configuration</title>
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
                <li><a href="testmail.php">Email</a></li>
                <li><a href="testldap.php">LDAP</a></li>
                <li class="active"><a href="#">SSL</a></li>
                <li><a href="testoauth2.php">OAuth2</a></li>
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

$time_start = microtime(true);
$rsa->loadKey($privateKey, phpseclib\Crypt\RSA::PRIVATE_FORMAT_PKCS1);
echo "<tr><td>Decrypted message</td><td>" . $rsa->decrypt($ciphertext)  . '</td></tr>';
$time_end = microtime(true);
$time = $time_end - $time_start;
echo "<tr><td>Decryption speed (fallback)</td><td>" . $time  . '</td></tr>';

if (function_exists('openssl_pkey_get_private')) {
    $time_start = microtime(true);
    $key = openssl_pkey_get_private($privateKey);
    openssl_private_decrypt($ciphertext, $message, $key);
    echo "<tr><td>Decrypted message</td><td>" . $message . '</td></tr>';
    $time_end = microtime(true);
    $time = $time_end - $time_start;
    echo "<tr><td>Decryption speed (native)</td><td>" . $time  . '</td></tr>';
}

//Generate public and private keys for a single usage
extract($rsa->createKey(KEY_SIZE));
?>
                  </tbody>
            </table>
            
            <h2 id="pair">Private and public key pair</h2>
            
            <p>This section will help you to create <code>assets/keys/private.pem</code> and <code>assets/keys/public.pem</code> files.
             Beware that it is a pair of keys that must be set together (i.e. you must update the both files at the same time with the corresponding content).</p>
            
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
