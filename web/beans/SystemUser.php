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

class SystemUser extends DataObject {

    private $id;
    private $name;
    private $email;
    private $password;
    private $sessiontoken;
    private $confirmemailtoken;
    private $forgottoken;
    private $changeemailtoken;
    private $newemail;
    private $usercountry;
    private $userlang;

    function __construct($id = null, $email = null, $password = null) {
	$this->id = $id;
	$this->email = $email;
	$this->password = $password;
    }

    public function __getProperties() {
	return array(
	    "id" => ["name" => "id", "lang" => 'lang_id', "type" => PropertyTypes::$_LONG, "required" => false, "default" => null, "serializable" => true, "index" => true, "reference" => null, "updatenull" => true, "hidden" => true, "private" => false, "unique" => true, "table" => true],
	    "name" => ["name" => "name", "lang" => 'lang_name', "type" => PropertyTypes::$_STRING256, "required" => false, "default" => null, "serializable" => true, "index" => false, "reference" => null, "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => true],
	    "email" => ["name" => "email", "lang" => 'lang_email', "type" => PropertyTypes::$_EMAIL, "required" => true, "default" => null, "serializable" => true, "index" => true, "reference" => null, "updatenull" => true, "hidden" => false, "private" => false, "unique" => true, "table" => true],
	    "password" => ["name" => "password", "lang" => 'lang_password', "type" => PropertyTypes::$_PASSWORD, "required" => false, "default" => null, "serializable" => true, "index" => true, "reference" => null, "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => false],
	    "sessiontoken" => ["name" => "sessiontoken", "lang" => 'lang_sessiontoken', "type" => PropertyTypes::$_STRING1024, "required" => false, "default" => "", "serializable" => true, "index" => false, "reference" => null, "updatenull" => true, "hidden" => false, "private" => true, "unique" => false, "table" => false],
	    "confirmemailtoken" => ["name" => "confirmemailtoken", "lang" => 'lang_confirmemailtoken', "type" => PropertyTypes::$_STRING1024, "required" => false, "default" => null, "serializable" => true, "index" => true, "reference" => null, "updatenull" => true, "hidden" => false, "private" => true, "unique" => false, "table" => false],
	    "forgottoken" => ["name" => "forgottoken", "lang" => 'lang_forgottoken', "type" => PropertyTypes::$_STRING1024, "required" => false, "default" => null, "serializable" => true, "index" => true, "reference" => null, "updatenull" => true, "hidden" => false, "private" => true, "unique" => false, "table" => false],
	    "changeemailtoken" => ["name" => "changeemailtoken", "lang" => 'lang_changeemailtoken', "type" => PropertyTypes::$_STRING1024, "required" => false, "default" => null, "serializable" => true, "index" => true, "reference" => null, "updatenull" => true, "hidden" => false, "private" => true, "unique" => false, "table" => false],
	    "newemail" => ["name" => "newemail", "lang" => 'lang_newemail', "type" => PropertyTypes::$_STRING256, "required" => false, "default" => null, "serializable" => true, "index" => true, "reference" => null, "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => false],
	    "usercountry" => ["name" => "usercountry", "lang" => 'lang_usercountry', "type" => PropertyTypes::$_STRINGLIST, "required" => false, "default" => null, "serializable" => true, "index" => true, "reference" => null, "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => false],
	    "userlang" => ["name" => "userlang", "lang" => 'lang_userlang', "type" => PropertyTypes::$_STRINGLIST, "required" => false, "default" => null, "serializable" => true, "index" => true, "reference" => null, "updatenull" => true, "hidden" => false, "private" => false, "unique" => false, "table" => false],
	);
    }

    public function __getList($paramname, Application $application) {
	switch ($paramname) {
	    case "usercountry" : {
		    $navigation = new Navigation($application);
		    $countries = $navigation->sortByLang($navigation->item(Country::$_ENUM, $navigation->config('lang')));
		    return $countries;
		}
	    case "userlang" : {
		    $navigation = new Navigation($application);
		    Lang::$_ENUM = $navigation->sortByLang(Lang::$_ENUM);
		    return Lang::$_ENUM;
		}
	}
	return [];
    }

    public function getId() {
	return $this->id;
    }

    public function getName() {
	return $this->name;
    }

    public function getEmail() {
	return $this->email;
    }

    public function getPassword() {
	return $this->password;
    }

    public function getSessiontoken() {
	return $this->sessiontoken;
    }

    public function getConfirmemailtoken() {
	return $this->confirmemailtoken;
    }

    public function getForgottoken() {
	return $this->forgottoken;
    }

    public function getChangeemailtoken() {
	return $this->changeemailtoken;
    }

    public function getNewemail() {
	return $this->newemail;
    }

    public function getUsercountry() {
	return $this->usercountry;
    }

    public function getUserlang() {
	return $this->userlang;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setName($name) {
	$this->name = $name;
    }

    public function setEmail($email) {
	$this->email = $email;
    }

    public function setPassword($password) {
	$this->password = $password;
    }

    public function setSessiontoken($sessiontoken) {
	$this->sessiontoken = $sessiontoken;
    }

    public function setConfirmemailtoken($confirmemailtoken) {
	$this->confirmemailtoken = $confirmemailtoken;
    }

    public function setForgottoken($forgottoken) {
	$this->forgottoken = $forgottoken;
    }

    public function setChangeemailtoken($changeemailtoken) {
	$this->changeemailtoken = $changeemailtoken;
    }

    public function setNewemail($newemail) {
	$this->newemail = $newemail;
    }

    public function setUsercountry($usercountry) {
	$this->usercountry = $usercountry;
    }

    public function setUserlang($userlang) {
	$this->userlang = $userlang;
    }

}
