<?php
/**
 * SAML Configuration file (for Onelogin PHP Library)
 * Full documentation is available at https://developers.onelogin.com/saml/php
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.5.0
 */

//You must switch $config['saml_enabled'] to TRUE into config/config.php prior using SAML

//Field Mapping : how to get the e-mail
$samlMailMap = 'User.email';

//You shouldn't change the 'sp' sub-array, but only the content of 'idp' sub-array
//Sp is specific to the application (Jorani)
//Idp is specific to your identity provider
$settingsInfo = array(
    'sp' => array(
        'entityId' => base_url() . 'metadata',
        'assertionConsumerService' => array(
            'url' => base_url() . 'api/acs',
        ),
        'singleLogoutService' => array(
            'url' => base_url() . 'api/sls',
        ),
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
    ),
    'idp' => array(
        'entityId' => 'https://app.onelogin.com/saml/metadata/561670',
        'singleSignOnService' => array(
            'url' => 'https://jorani.onelogin.com/trust/saml2/http-post/sso/561670',
        ),
        'singleLogoutService' => array(
            'url' => 'https://jorani.onelogin.com/trust/saml2/http-redirect/slo/561670',
        ),
        'x509cert' => '-----BEGIN CERTIFICATE-----
MIIEETCCAvmgAwIBAgIUAlUlf+CytDcqaV/rFFR5LxkXxAUwDQYJKoZIhvcNAQEF
BQAwVjELMAkGA1UEBhMCVVMxDzANBgNVBAoMBkpvcmFuaTEVMBMGA1UECwwMT25l
TG9naW4gSWRQMR8wHQYDVQQDDBZPbmVMb2dpbiBBY2NvdW50IDg3MzUzMB4XDTE2
MDcwODEyMzYxMVoXDTIxMDcwOTEyMzYxMVowVjELMAkGA1UEBhMCVVMxDzANBgNV
BAoMBkpvcmFuaTEVMBMGA1UECwwMT25lTG9naW4gSWRQMR8wHQYDVQQDDBZPbmVM
b2dpbiBBY2NvdW50IDg3MzUzMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKC
AQEAs0erB7z+dN/CBeuPakhPRhK8mtT1PikMeYbRSh6W1cHGZVR9iKxIJR29tKX7
8IuifNKXDSwWH3sumva8ADgoyvJBZYFx7ryk2I0/vh3FPYpCdJRNAWH6vcWobgHm
IlJngHZf2dZqvx8FnLhddfxv3YdDegDp8pPe5ny+fum7I6zKec26ilUGLvTzL7uD
gLRccrXDkXFQ8ohpTkqAKaV4aW+p8bYBNEpSwpBZLN/RaWxjcx/yQNIlbXu3ezqG
I7vQupcxpDkyauh7/yLEsvkkXjFlvEFmlezPTu9UW5rKiWKefLz+4LVZD4HPUNcj
jPbQN15YN5QQ98pAbtc/vKyhsQIDAQABo4HWMIHTMAwGA1UdEwEB/wQCMAAwHQYD
VR0OBBYEFPpQe2bBNyf9/2fduw82NuenQmiGMIGTBgNVHSMEgYswgYiAFPpQe2bB
Nyf9/2fduw82NuenQmiGoVqkWDBWMQswCQYDVQQGEwJVUzEPMA0GA1UECgwGSm9y
YW5pMRUwEwYDVQQLDAxPbmVMb2dpbiBJZFAxHzAdBgNVBAMMFk9uZUxvZ2luIEFj
Y291bnQgODczNTOCFAJVJX/gsrQ3Kmlf6xRUeS8ZF8QFMA4GA1UdDwEB/wQEAwIH
gDANBgkqhkiG9w0BAQUFAAOCAQEAjo54wsSrfB0AQ57VAwNECU6zP+fr4IedRSP0
FgboAz0y3ujrkgdd+1aorXy1DhNPuUcIEJOX3Aa97ahPSBd/8hB6wzx1K/7Q4V96
3unfSJ07cMCeEEmip3mdlX0m9cul0XRoUkgpTusVYGeD+0go3/d6BWRJ4kF4XGTm
nqjxFIEueI9tsQXWaWLxh+ccjkKzzDHJFYX/mCygkM3Ho7cOfQuBrQpPbkbgWmKA
TP+yhnSo3b8aYe9VAEJIdK0d0daHIGFNGEFQXHqN+jEh55fVLwoRJtp1CzO5/Ebv
LnraIOHdhP7OCkyZ3FwpUnlYeFsp4IBQEdp2ooPouFyNE50wzQ==
-----END CERTIFICATE-----
',
    ),
);
