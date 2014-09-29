<?php defined('BASEPATH') OR exit('No direct script access allowed.');

/**
 * CodeIgniter compatible email-library powered by PHPMailer.
 * Version: 1.1.3
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2012-2014.
 * @license The MIT License (MIT), http://opensource.org/licenses/MIT
 * @link https://github.com/ivantcholakov/codeigniter-phpmailer
 *
 * Tested on production sites with CodeIgniter 3.0-dev (May 24th, 2014) and
 * PHPMailer Version 5.2.8 (May 14th, 2014).
 */

class MY_Email extends CI_Email {

    static $mailer_engine = 'codeigniter';
    static $phpmailer;
    protected $CI;

    protected $_is_ci_3 = NULL;

    protected static $protocols = array('mail', 'sendmail', 'smtp');
    protected static $mailtypes = array('html', 'text');

    public function __construct($config = array()) {

        $this->_is_ci_3 = (bool) ((int) CI_VERSION >= 3);

        $this->CI = get_instance();
        $this->CI->load->helper('email');
        $this->CI->load->helper('html');

        if (!is_array($config)) {
            $config = array();
        }

        if (isset($config['useragent'])) {

            $useragent = trim($config['useragent']);
            $mailer_engine = strtolower($useragent);

            if (strpos($mailer_engine, 'phpmailer') !== false) {
                $this->mailer_engine = 'phpmailer';
            } elseif(strpos($mailer_engine, 'codeigniter') !== false) {
                $this->mailer_engine = 'codeigniter';
            } else {
                unset($config['useragent']);    // An invalid setting;
            }
        }

        if (isset($config['charset'])) {

            $charset = trim($config['charset']);

            if ($charset != '') {
                $this->charset = $charset;
                unset($config['charset']);      // We don't need this anymore.
            }

        } else {

            $charset = trim(config_item('charset'));

            if ($charset != '') {
                $this->charset = $charset;
            }
        }

        $this->charset = strtoupper($this->charset);

        if ($this->mailer_engine == 'phpmailer') {

            // If your system uses class autoloading feature,
            // then the following require statement would not be needed.
            if (!class_exists('PHPMailer', false)) {
                require_once APPPATH.'third_party/PHPMailer/PHPMailerAutoload.php';
            }
            //

            $this->phpmailer = new PHPMailer();
            $this->phpmailer->PluginDir = APPPATH.'third_party/phpmailer/';

            $this->_copy_property_to_phpmailer('charset');
        }

        if (count($config) > 0) {

            $this->initialize($config);

        } else {

            $this->_smtp_auth = ($this->smtp_user == '' AND $this->smtp_pass == '') ? FALSE : TRUE;

            if ($this->mailer_engine == 'phpmailer') {
                $this->_copy_property_to_phpmailer('_smtp_auth');
            }
        }

        $this->_safe_mode = ( ! is_php('5.4') && ini_get('safe_mode'));

        log_message('debug', 'MY_Email Class Initialized (Engine: '.$this->mailer_engine.')');
    }

    /**
     * Define these options within the $config array or
     * within the configuration file email.php:
     * useragent
     * protocol
     * mailpath
     * smtp_host
     * smtp_user
     * smtp_pass
     * smtp_port
     * smtp_timeout
     * smtp_crypto
     * set_wordwrap
     * wrapchars
     * mailtype
     * charset
     * validate
     * priority
     * crlf
     * newline
     * bcc_batch_mode
     * bcc_batch_size
     */
    public function initialize($config = array()) {

        if (!is_array($config)) {
            $config = array();
        }

        foreach ($config as $key => $val) {

            $method = 'set_'.$key;

            if (method_exists($this, $method)) {

                $this->$method($val);

            } elseif (isset($this->$key)) {

                $this->$key = $val;

                if ($this->mailer_engine == 'phpmailer') {
                    $this->_copy_property_to_phpmailer($key);
                }
            }
        }

        $this->clear();

        $this->_smtp_auth = ($this->smtp_user == '' AND $this->smtp_pass == '') ? FALSE : TRUE;

        if ($this->mailer_engine == 'phpmailer') {
            $this->_copy_property_to_phpmailer('_smtp_auth');
        }

        return $this;
    }

    public function clear($clear_attachments = false) {

        $clear_attachments = !empty($clear_attachments);

        parent::clear($clear_attachments);

        if ($this->mailer_engine == 'phpmailer') {

            $this->phpmailer->clearAllRecipients();
            $this->phpmailer->clearReplyTos();
            if ($clear_attachments) {
                $this->phpmailer->clearAttachments();
            }

            $this->phpmailer->clearCustomHeaders();

            $this->phpmailer->Subject = '';
            $this->phpmailer->Body = '';
            $this->phpmailer->AltBody = '';
        }

        return $this;
    }

    public function set_protocol($protocol = 'mail') {

        $protocol = trim(strtolower($protocol));

        $this->protocol = in_array($protocol, self::$protocols) ? $protocol : 'mail';

        if ($this->mailer_engine == 'phpmailer') {

            switch ($this->protocol) {

                case 'mail':
                    $this->phpmailer->isMail();
                    break;

                case 'sendmail':
                    $this->phpmailer->isSendmail();
                    break;

                case 'smtp':
                    $this->phpmailer->isSMTP();
                    break;
            }
        }

        return $this;
    }

    public function set_smtp_crypto($smtp_crypto = '') {

        $smtp_crypto = trim(strtolower($smtp_crypto));

        if ($smtp_crypto != 'tls' && $smtp_crypto != 'ssl') {
            $smtp_crypto = '';
        }

        $this->smtp_crypto = $smtp_crypto;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->set('SMTPSecure', $smtp_crypto);
        }

        return $this;
    }

    public function set_wordwrap($wordwrap = TRUE) {

        $this->wordwrap = !empty($wordwrap);

        if (!$this->wordwrap) {

            if ($this->mailer_engine == 'phpmailer') {
                $this->phpmailer->set('WordWrap', 0);
            }
        }

        return $this;
    }

    public function set_mailtype($type = 'text') {

        $type = trim(strtolower($type));

        $this->mailtype = in_array($type, self::$mailtypes) ? $type : 'text';

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->isHTML($this->mailtype == 'html');
        }

        return $this;
    }

    public function set_priority($n = 3) {

        $this->priority = preg_match('/^[1-5]$/', $n) ? (int) $n : 3;

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->set('Priority', $this->priority);
        }

        return $this;
    }

    public function valid_email($email) {

        return valid_email($email);
    }

    public function from($from, $name = '', $return_path = NULL) {

        $from = (string) $from;
        $name = (string) $name;
        $return_path = (string) $return_path;

        if ($this->mailer_engine == 'phpmailer') {

            if (preg_match( '/\<(.*)\>/', $from, $match)) {
                $from = $match['1'];
            }

            if ($this->validate) {

                $this->validate_email($this->_str_to_array($from));

                if ($return_path) {
                    $this->validate_email($this->_str_to_array($return_path));
                }
            }

            $this->phpmailer->setFrom($from, $name, 0);

            if (!$return_path) {
                $return_path = $from;
            }

            $this->phpmailer->set('Sender', $return_path);

        } else {

            if ($this->_is_ci_3) {
                parent::from($from, $name, $return_path);
            } else {
                parent::from($from, $name);
            }
        }

        return $this;
    }

    public function reply_to($replyto, $name = '') {

        $replyto = (string) $replyto;
        $name = (string) $name;

        if ($this->mailer_engine == 'phpmailer') {

            if (preg_match( '/\<(.*)\>/', $replyto, $match)) {
                $replyto = $match['1'];
            }

            if ($this->validate) {
                $this->validate_email($this->_str_to_array($replyto));
            }

            if ($name == '') {
                $name = $replyto;
            }

            $this->phpmailer->addReplyTo($replyto, $name);

            $this->_replyto_flag = TRUE;

        } else {

            parent::reply_to($replyto, $name);
        }

        return $this;
    }

    public function to($to) {

        if ($this->mailer_engine == 'phpmailer') {

            $to = $this->_str_to_array($to);
            $names = $this->_extract_name($to);
            $to = $this->clean_email($to);

            if ($this->validate) {
                $this->validate_email($to);
            }

            reset($names);

            foreach ($to as $address) {

                list($key, $name) = each($names);
                $this->phpmailer->addAddress($address, $name);
            }

        } else {

            parent::to($to);
        }

        return $this;
    }

    public function cc($cc) {

        if ($this->mailer_engine == 'phpmailer') {

            $cc = $this->_str_to_array($cc);
            $names = $this->_extract_name($cc);
            $cc = $this->clean_email($cc);

            if ($this->validate) {
                $this->validate_email($cc);
            }

            reset($names);

            foreach ($cc as $address) {

                list($key, $name) = each($names);
                $this->phpmailer->addCC($address, $name);
            }

        } else {

            parent::cc($cc);
        }

        return $this;
    }

    public function bcc($bcc, $limit = '') {

        if ($this->mailer_engine == 'phpmailer') {

            $bcc = $this->_str_to_array($bcc);
            $names = $this->_extract_name($bcc);
            $bcc = $this->clean_email($bcc);

            if ($this->validate) {
                $this->validate_email($bcc);
            }

            reset($names);

            foreach ($bcc as $address) {

                list($key, $name) = each($names);
                $this->phpmailer->addBCC($address, $name);
            }

        } else {

            parent::bcc($bcc, $limit);
        }

        return $this;
    }

    public function subject($subject) {

        $subject = (string) $subject;

        if ($this->mailer_engine == 'phpmailer') {

           $this->phpmailer->Subject = (string) $subject;

        } else {

            parent::subject($subject);
        }

        return $this;
    }

    public function message($body) {

        $body = (string) $body;

        if ($this->mailer_engine == 'phpmailer') {

            $this->phpmailer->Body = $body;
        }

        parent::message($body);

        return $this;
    }

    // Modified by Ivan Tcholakov, 16-JAN-2014.
    //public function attach($file, $disposition = '', $newname = NULL, $mime = '') {
    public function attach($file, $disposition = '', $newname = NULL, $mime = '', $embedded_image = false) {
    //

        $file = (string) $file;

        $disposition = (string) $disposition;

        if ($disposition == '') {
            $disposition ='attachment';
        }

        if ($this->mailer_engine == 'phpmailer') {

            $newname = (string) $newname;
            $mime = (string) $mime;

            if ($mime == '') {

                if (strpos($file, '://') === FALSE && ! file_exists($file)) {

                    $this->_set_error_message('lang:email_attachment_missing', $file);
                    // Modified by Ivan Tcholakov, 14-JAN-2014.
                    //return FALSE;
                    return $this;
                    //
                }

                if (!$fp = @fopen($file, FOPEN_READ)) {

                    $this->_set_error_message('lang:email_attachment_unreadable', $file);
                    // Modified by Ivan Tcholakov, 14-JAN-2014.
                    //return FALSE;
                    return $this;
                    //
                }

                $file_content = stream_get_contents($fp);
                $mime = $this->_mime_types(pathinfo($file, PATHINFO_EXTENSION));
                fclose($fp);

                $newname = basename($file);

            } else {

                $file_content =& $file; // Buffered file.
                // Added by Ivan Tcholakov, 14-JAN-2014.
                $file = $newname;
                //
            }

            $this->_attachments[] = array(
                'name' => array($file, $newname),
                'disposition' => $disposition,
                'type' => $mime,
            );

            if (empty($embedded_image)) {

                $this->phpmailer->addStringAttachment($file_content, $newname, 'base64', $mime, $disposition);

            } else {

                $cid = $this->attachment_cid($file);
                $this->phpmailer->addStringEmbeddedImage($file_content, $cid, $newname, 'base64', $mime, $disposition);
            }

        } else {

            if ($this->_is_ci_3) {
                parent::attach($file, $disposition, $newname, $mime);
            } else {
                parent::attach($file, $disposition);
            }
        }

        return $this;
    }

    public function attachment_cid($filename) {

        if ($this->mailer_engine == 'phpmailer') {

            for ($i = 0, $c = count($this->_attachments); $i < $c; $i++) {

                if ($this->_attachments[$i]['name'][0] === $filename) {

                    $this->_attachments[$i]['cid'] = uniqid(basename($this->_attachments[$i]['name'][0]).'@');
                    return $this->_attachments[$i]['cid'];
                }
            }

        } elseif ($this->_is_ci_3) {

            return parent::attachment_cid($filename);
        }

        return FALSE;
    }

    // Added by Ivan Tcholakov, 16-JAN-2014.
    public function get_attachment_cid($filename) {

        for ($i = 0, $c = count($this->_attachments); $i < $c; $i++) {

            if ($this->_attachments[$i]['name'][0] === $filename) {
                return empty($this->_attachments[$i]['cid']) ? FALSE : $this->_attachments[$i]['cid'];
            }
        }

        return FALSE;
    }
    //

    public function send($auto_clear = true) {

        $auto_clear = !empty($auto_clear);

        if ($this->mailer_engine == 'phpmailer') {

            if ($this->mailtype == 'html') {
                $this->phpmailer->AltBody = $this->_get_alt_message();
            }

            $result = (bool) $this->phpmailer->send();

            if ($result) {

                $this->_set_error_message('lang:email_sent', $this->_get_protocol());

                if ($auto_clear) {
                    $this->clear();
                }

            } else {

                $this->_set_error_message($this->phpmailer->ErrorInfo);
            }

        } else {

            if ($this->_is_ci_3) {
                $result = parent::send($auto_clear);
            } else {
                $result = parent::send();
            }
        }

        return $result;
    }


    // Custom methods ----------------------------------------------------------

    // PHPMailer's SMTP debug info level
    // 0 = off, 1 = commands, 2 = commands and data, 3 = as 2 plus connection status, 4 = low level data output.
    public function set_smtp_debug($level) {

        $level = (int) $level;

        if ($level < 0) {
            $level = 0;
        }

        if ($this->mailer_engine == 'phpmailer') {
            $this->phpmailer->SMTPDebug = $level;
        }

        return $this;
    }

    public function full_html($subject, $message) {

        $full_html =
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>'.htmlspecialchars($subject, ENT_QUOTES, $this->charset).'</title>

    <style type="text/css">

        /* See http://htmlemailboilerplate.com/ */

        /* Based on The MailChimp Reset INLINE: Yes. */  
        /* Client-specific Styles */
        #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
        body {
            width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:40px;
            font-family: Arial, Verdana, Helvetica, sans-serif; font-size: 16px;
        } 
        /* End reset */

        /* Some sensible defaults for images
        Bring inline: Yes. */
        img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;} 
        a img {border:none;} 

        /* Yahoo paragraph fix
        Bring inline: Yes. */
        p {margin: 1em 0;}

        /* Hotmail header color reset
        Bring inline: Yes. */
        h1, h2, h3, h4, h5, h6 {color: black !important;}

        h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: blue !important;}

        h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
        color: red !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
        }

        h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
        color: purple !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
        }

        /* Outlook 07, 10 Padding issue fix
        Bring inline: No.*/
        table td {border-collapse: collapse;}

        /* Remove spacing around Outlook 07, 10 tables
        Bring inline: Yes */
        table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }

        /* Styling your links has become much simpler with the new Yahoo.  In fact, it falls in line with the main credo of styling in email and make sure to bring your styles inline.  Your link colors will be uniform across clients when brought inline.
        Bring inline: Yes. */
        a {color: blue;}

    </style>

</head>

<body>

'.$message.'

</body>
</html>';

        return $full_html;
    }


    // Protected methods -------------------------------------------------------

    protected function _get_alt_message() {

        if (!empty($this->alt_message)) {

            return ($this->wordwrap)
                ? $this->word_wrap($this->alt_message, 76)
                : $this->alt_message;
        }

        $body = $this->_plain_text($this->_body);

        return ($this->wordwrap)
            ? $this->word_wrap($body, 76)
            : $body;
    }

    protected function _plain_text($html) {

        if (!function_exists('html_to_text')) {

            $body = @ html_entity_decode($html, ENT_QUOTES, $this->charset); // Added by Ivan Tcholakov, 28-JUL-2013.

            $body = preg_match('/\<body.*?\>(.*)\<\/body\>/si', $body, $match) ? $match[1] : $body;
            $body = str_replace("\t", '', preg_replace('#<!--(.*)--\>#', '', trim(strip_tags($body))));

            for ($i = 20; $i >= 3; $i--)
            {
                $body = str_replace(str_repeat("\n", $i), "\n\n", $body);
            }

            // Reduce multiple spaces
            $body = preg_replace('| +|', ' ', $body);

            return $body;
        }

        // Also, a special helper function based on Markdown or Textile libraries may be used.
        //
        // An example of Markdown-based implementation, see http://milianw.de/projects/markdownify/
        //
        // Make sure the class Markdownify_Extra is autoloaded (or simply loaded somehow).
        // Place in MY_html_helper.php the following function.
        //
        // function html_to_text($html) {
        //     static $parser;
        //     if (!isset($parser)) {
        //         $parser = new Markdownify_Extra();
        //         $parser->keepHTML = false;
        //     }
        //     return @ $parser->parseString($html);
        // }
        //

        return html_to_text($html);
    }

    protected function _copy_property_to_phpmailer($key) {

        static $properties = array(
            '_smtp_auth' => 'SMTPAuth',
            'mailpath' => 'Sendmail',
            'smtp_host' => 'Host',
            'smtp_user' => 'Username',
            'smtp_pass' => 'Password',
            'smtp_port' => 'Port',
            'smtp_timeout' => 'Timeout',
            'wrapchars' => 'WordWrap',
            'charset' => 'CharSet',
        );

        if (isset($properties[$key])) {
            $this->phpmailer->set($properties[$key], $this->$key);
        }

        if ($key == 'wrapchars') {

            if (!$this->wordwrap) {
                $this->phpmailer->set('WordWrap', 0);
            }
        }
    }

    protected function _extract_name($address) {

        if (!is_array($address)) {

            $address = trim($address);

            if (preg_match('/(.*)\<(.*)\>/', $address, $match)) {
                return trim($match['1']);
            } else {
                return '';
            }
        }

        $result = array();

        foreach ($address as $addr) {

            $addr = trim($addr);

            if (preg_match('/(.*)\<(.*)\>/', $addr, $match)) {
                $result[] = trim($match['1']);
            } else {
                $result[] = '';
            }
        }

        return $result;
    }

}
