<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
abstract class SMTPEmail {

    public $to;
    public $from;
    public $fromName;
    public $subject;
    public $error;

    function __construct($to, $from, $fromName, $subject) {
        $this->to = $to;
        $this->from = $from;
        $this->fromName = $fromName;
        $this->subject = $subject;
    }

    function send($host, $port, $auth, $username, $password, $body) {
        $headers["From"] = "$this->fromName<$this->from>";
        $headers["Reply-To"] = "$this->fromName<$this->from>";
        $headers["To"] = $this->to;
        $headers["Subject"] = $this->subject;
        $headers["Content-type"] = "text/html; charset=utf8";
        $params["host"] = $host;
        $params["port"] = $port;
        $params["auth"] = $auth;
        $params["username"] = $username;
        $params["password"] = $password;
        $mail_object = & Mail::factory("smtp", $params);
        $result = $mail_object->send($this->to, $headers, $body);
        if ($result === true) {
            return true;
        }
        $this->error = $result;
        return false;
    }

}

