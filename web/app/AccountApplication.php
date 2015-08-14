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

class AccountApplication extends Application {

    public $user;

    function __construct($config, $lang, Data $data = null, $name = "account") {
	if (!$data) {
	    $data = new PostgreSQLData($config);
	}
	parent::__construct($config, $lang, $data, $name);
    }

    public function execute() {
	parent::execute();
	require_once("nav/LoginNavigation.php");
	$navigation = new LoginNavigation($this);
	$this->user = $navigation->loginCheck();
	if (!$this->user) {
	    $this->redirect("login", "login");
	}
	$this->navigation = $this->getUrlParam($this->config('param_navigation'), PropertyTypes::$_LETTERS, false);
	switch ($this->navigation) {
	    case "logout" : {
		    require_once("nav/LoginNavigation.php");
		    $navigation = new LoginNavigation($this);
		    $navigation->logout("home", "home");
		    break;
		}
	    case "revalidate" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->resendValidation();
		    break;
		}
	    case "account" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->account();
		    break;
		}
	    case "profileSubmit" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->profileSubmit();
		    break;
		}
	    case "passwordSubmit" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->passwordSubmit();
		    break;
		}
	    case "emailSubmit" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->emailSubmit();
		    break;
		}
	}
    }

}
