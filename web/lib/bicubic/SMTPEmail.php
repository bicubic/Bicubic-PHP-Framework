<?php

/*
 * The MIT License
 *
 * Copyright 2015 Juan Francisco Rodríguez.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
