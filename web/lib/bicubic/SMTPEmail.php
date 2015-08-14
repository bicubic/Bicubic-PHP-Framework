<?php

/*
 * Copyright (C)  Juan Francisco Rodríguez
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
