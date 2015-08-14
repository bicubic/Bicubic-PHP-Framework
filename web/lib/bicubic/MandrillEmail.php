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
