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
