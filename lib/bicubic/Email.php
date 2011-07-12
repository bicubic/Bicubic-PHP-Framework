<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
abstract class Email {

    public $to;
    public $from;
    public $fromName;
    public $subject;

    function __construct($to, $from, $fromName, $subject) {
        $this->to = $to;
        $this->from = $from;
        $this->fromName = $fromName;
        $this->subject = $subject;
    }

    abstract function getBody();

    abstract function getPlainBody();

    function send($host, $port, $auth, $username, $password) {
        $headers["From"] = "$this->fromName<$this->from>";
        $headers["Reply-To"] = "$this->fromName<$this->from>";
        $headers["To"] = $this->to;
        $headers["Subject"] = $this->subject;
        $headers["Content-type"] = "text/html; charset=utf8";
        $body = $this->getBody();
        $params["host"] = $host;
        $params["port"] = $port;
        $params["auth"] = $auth;
        $params["username"] = $username;
        $params["password"] = $password;
        $mail_object = & Mail::factory("smtp", $params);
        if (!$mail_object->send($this->to, $headers, $body)) {
            return false;
        }
        return true;
    }

    function sendPlain($host, $port, $auth, $username, $password) {
        $headers["From"] = "$this->fromName<$this->from>";
        $headers["Reply-To"] = "$this->fromName<$this->from>";
        $headers["To"] = $this->to;
        $headers["Subject"] = $this->subject;
        $headers["Content-type"] = "text/html; charset=utf8";
        $body = $this->getPlainBody();
        $params["host"] = $host;
        $params["port"] = $port;
        $params["auth"] = $auth;
        $params["username"] = $username;
        $params["password"] = $password;
        $mail_object = & Mail::factory("smtp", $params);
        if (!$mail_object->send($this->to, $headers, $body)) {
            return false;
        }
        return true;
    }

    public function utf8tohtml($utf8, $encodeTags) {
        $result = '';
        for ($i = 0; $i < strlen($utf8); $i++) {
            $char = $utf8[$i];
            $ascii = ord($char);
            if ($ascii < 128) {
                $result .= ( $encodeTags) ? htmlentities($char) : $char;
            } else if ($ascii < 192) {

            } else if ($ascii < 224) {
                $result .= htmlentities(substr($utf8, $i, 2), ENT_QUOTES, 'UTF-8');
                $i++;
            } else if ($ascii < 240) {
                $ascii1 = ord($utf8[$i + 1]);
                $ascii2 = ord($utf8[$i + 2]);
                $unicode = (15 & $ascii) * 4096 +
                        (63 & $ascii1) * 64 +
                        (63 & $ascii2);
                $result .= "&#$unicode;";
                $i += 2;
            } else if ($ascii < 248) {
                $ascii1 = ord($utf8[$i + 1]);
                $ascii2 = ord($utf8[$i + 2]);
                $ascii3 = ord($utf8[$i + 3]);
                $unicode = (15 & $ascii) * 262144 +
                        (63 & $ascii1) * 4096 +
                        (63 & $ascii2) * 64 +
                        (63 & $ascii3);
                $result .= "&#$unicode;";
                $i += 3;
            }
        }
        return $result;
    }

}

?>
