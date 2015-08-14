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
