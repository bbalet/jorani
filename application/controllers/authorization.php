<?php
/**
 * TODO : This controller 
 * http://bshaffer.github.io/oauth2-server-php-docs/cookbook/
 * 
 * @copyright  Copyright (c) 2014-2017 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.6.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class implements a 
 */
class Authorization extends CI_Controller {
    
    /**
     * OAuth2 server used by all methods in order to determine if the user is connected
     * @var OAuth2\Server Authentication server 
     */
    protected $server; 
    
    /**
     * Default constructor
     * Initializing of OAuth2 server
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        require_once(APPPATH . 'third_party/OAuth2/Autoloader.php');
        OAuth2\Autoloader::register();
        $dsn = 'mysql:dbname=' . $this->db->database . ';host=' . $this->db->hostname;
        $username = $this->db->username;
        $password = $this->db->password;
        $storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
        $this->server = new OAuth2\Server($storage);
        $this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
        $this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
        
        if ($this->session->userdata('language') === FALSE) {
            $availableLanguages = explode(",", $this->config->item('languages'));
            $this->load->library('polyglot');
            $languageCode = $this->polyglot->language2code($this->config->item('language'));
            if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                if (in_array($_SERVER['HTTP_ACCEPT_LANGUAGE'], $availableLanguages)) {
                    $languageCode = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
                }
            }
            $this->session->set_userdata('language_code', $languageCode);
            $this->session->set_userdata('language', $this->polyglot->code2language($languageCode));
        }
        $this->lang->load('session', $this->session->userdata('language'));
        $this->lang->load('global', $this->session->userdata('language'));
        
        //CORS setup
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: x-requested-with');
        header('Access-Control-Allow-Methods: GET, POST');
    }

    /**
     * OAuth2 authorize endpoint 
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function authorize() {
        require_once(APPPATH . 'third_party/OAuth2/Server.php');
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();

        // validate the authorize request
        if (!$this->server->validateAuthorizeRequest($request, $response)) {
            $response->send();
            die;
        }
        
        //Is the user logged in?
        //Is the application already authorized?
        
        //Display simple login form if the user is not logged-in
        if (!$this->session->userdata('logged_in')) {
            if (empty($_POST)) {
                $this->load->library('form_validation');
                $data['public_key'] = file_get_contents('./assets/keys/public.pem', TRUE);
                $data['salt'] = $this->generateRandomString(rand(5, 20));
                $data['language'] = $this->session->userdata('language');
                $data['language_code'] = $this->session->userdata('language_code');
                $this->session->set_userdata('salt', $data['salt']);
                $this->load->view('session/login_simple', $data);
            }
        }
        
        if ($this->session->userdata('logged_in')) {
            $this->load->model('oauthclients_model');
            $userId = $this->session->userdata('id');
            $clientId = $this->input->get_post('client_id');
            //Test if we received a form authorizing the application
            if (empty($_POST)) {
                // Does the application was already authorized by user
                if ($this->oauthclients_model->isOAuthAppAllowed($clientId, $userId)) {
                    $this->server->handleAuthorizeRequest($request, $response, TRUE, $userId);
                    $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
                    header("Content-Type: text/plain");
                    echo $code;
                } else {
                    // display an authorization form
                    $data['clientId'] = $clientId;
                    $data['responseType'] = $this->input->get('response_type');
                    $data['state'] = $this->input->get('state');
                    $data['language'] = $this->session->userdata('language');
                    $data['language_code'] = $this->session->userdata('language_code');
                    $this->load->view('session/authorize', $data);
                }
            } else {
                // print the authorization code if the user has authorized your client
                $is_authorized = ($_POST['authorized'] === 'yes');
                $this->server->handleAuthorizeRequest($request, $response, $is_authorized, $userId);
                if ($is_authorized) {
                    $this->oauthclients_model->allowOAuthApp($clientId, $userId);
                    $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
                    header("Content-Type: text/plain");
                    echo $code;
                } else {
                    $response->send();
                }
            } 
        }
    }
    
    /**
     * Get the details of the connected user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function userinfo() {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
        } else {
            $token = $this->server->getAccessTokenData(OAuth2\Request::createFromGlobals());
            $this->load->model('users_model');
            $result = $this->users_model->getUsers($token['user_id']);
            unset($result['password']);
            header("Content-Type: application/json");
            echo json_encode($result);
        }
    }
    
    /**
     * Handle the Simplified login form for OAuth authorization
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function login() {
        header("Content-Type: text/plain");
        //Decrypt password
        $password = '';
        if (function_exists('openssl_pkey_get_private')) {
            $privateKey = openssl_pkey_get_private(file_get_contents('./assets/keys/private.pem', TRUE));
            openssl_private_decrypt(base64_decode($this->input->post('CipheredValue')), $password, $privateKey);
        } else {
            require_once(APPPATH . 'third_party/phpseclib/vendor/autoload.php');
            $rsa = new phpseclib\Crypt\RSA();
            $private_key = file_get_contents('./assets/keys/private.pem', TRUE);
            $rsa->setEncryptionMode(phpseclib\Crypt\RSA::ENCRYPTION_PKCS1);
            $rsa->loadKey($private_key, phpseclib\Crypt\RSA::PRIVATE_FORMAT_PKCS1);
            $password = $rsa->decrypt(base64_decode($this->input->post('CipheredValue')));
        }
        //Remove the salt and check the credentials
        $len_salt = strlen($this->session->userdata('salt')) * (-1);
        $password = substr($password, 0, $len_salt);
        $this->load->model('users_model');
        $loggedin = $this->users_model->checkCredentials($this->input->post('login'), $password);    
        echo ($loggedin===TRUE)?"TRUE":"FALSE";
    }
    
    /**
     * Generate a random string by using openssl, dev/urandom or random
     * @param int $length optional length of the string
     * @return string random string
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function generateRandomString($length = 10) {
        if(function_exists('openssl_random_pseudo_bytes')) {
          $rnd = openssl_random_pseudo_bytes($length, $strong);
          if ($strong === TRUE)
            return base64_encode($rnd);
        }
        $sha =''; $rnd ='';
        if (file_exists('/dev/urandom')) {
          $fp = fopen('/dev/urandom', 'rb');
          if ($fp) {
              if (function_exists('stream_set_read_buffer')) {
                  stream_set_read_buffer($fp, 0);
              }
              $sha = fread($fp, $length);
              fclose($fp);
          }
        }
        for ($i=0; $i<$length; $i++) {
          $sha  = hash('sha256',$sha.mt_rand());
          $char = mt_rand(0,62);
          $rnd .= chr(hexdec($sha[$char].$sha[$char+1]));
        }
        return base64_encode($rnd);
    }
}
