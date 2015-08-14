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

class MandrillEmail {

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

    function send($key, $html, $text = "") {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://mandrillapp.com/api/1.0/messages/send.json");
	curl_setopt($ch, CURLOPT_POST, 1);
	$jsonObject = new ObjectJson();
	$jsonObject->key = $key;
	$jsonObject->message = new ObjectJson();
	$jsonObject->message->html = $html;
	$jsonObject->message->text = $text;
	$jsonObject->message->subject = $this->subject;
	$jsonObject->message->from_email = $this->from;
	$jsonObject->message->from_name = $this->fromName;
	$jsonObject->message->to = array();
	$toObject = new ObjectJson();
	$toObject->email = $this->to;
	$jsonObject->message->to [] = $toObject;
	$jsonObject->async = true;
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonObject));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $result = curl_exec($ch);
//        if ($result) {
//            $r = json_decode($result);
//            if ($r) {
//                if (property_exists($r, "status") && $r->status == "error") {
//                    $this->error = $r->message;
//                } else {
//                    foreach ($r as $key=> $response) {
//                        if (property_exists($response, "status") && $response->status != "error") {
//                            return true;
//                        }
//                        if (property_exists($response, "status") && $response->status == "error") {
//                            $this->error = $r->message;
//                        }
//                        break;
//                    }
//                }
//            }
//        }
//        curl_close($ch);
//        return false;
	curl_exec($ch);
	curl_close($ch);
	return true;
    }

}
