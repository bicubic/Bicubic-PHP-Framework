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

class LoginApplication extends Application {

    function __construct($config, $lang, Data $data = null, $name = "login") {
	if (!$data) {
	    $data = new PostgreSQLData($config);
	}
	parent::__construct($config, $lang, $data, $name);
    }

    public function execute() {
	parent::execute();
	$this->navigation = $this->getUrlParam($this->config('param_navigation'), PropertyTypes::$_LETTERS, false);
	switch ($this->navigation) {
	    case "login" : {
		    require_once("nav/LoginNavigation.php");
		    $navigation = new LoginNavigation($this);
		    $navigation->login();
		    break;
		}
	    case "loginSubmit" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->loginSubmit("private", "home");
		    break;
		}
	    case "signup" : {
		    require_once("nav/LoginNavigation.php");
		    $navigation = new LoginNavigation($this);
		    $navigation->signup();
		    break;
		}
	    case "signUpSubmit" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->signUpSubmit("private", "home");
		    break;
		}
	    case "validate" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->validate();
		    break;
		}
	    case "forgot" : {
		    require_once("nav/LoginNavigation.php");
		    $navigation = new LoginNavigation($this);
		    $navigation->forgot();
		    break;
		}
	    case "forgotSubmit" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->forgotSubmit();
		    break;
		}
	    case "forgotValidate" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->forgotValidate();
		    break;
		}
	    case "forgotValidateSubmit" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->forgotValidateSubmit();
		    break;
		}
	    case "emailValidate" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->emailValidate();
		    break;
		}
	    case "emailValidateSubmit" : {
		    require_once('nav/LoginNavigation.php');
		    $navigation = new LoginNavigation($this);
		    $navigation->emailValidateSubmit();
		    break;
		}
	}
    }

}
