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

require_once("app/AccountApplication.php");

class PrivateApplication extends AccountApplication {

    function __construct($config, $lang, Data $data = null, $name = "private") {
	if (!$data) {
	    $data = new PostgreSQLData($config);
	}
	parent::__construct($config, $lang, $data, $name);
    }

    public function execute() {
	parent::execute();
	$this->navigation = $this->getUrlParam($this->config('param_navigation'), PropertyTypes::$_LETTERS, false);
	switch ($this->navigation) {
	    case "home" : {
		    require_once("nav/HelloNavigation.php");
		    $navigation = new HelloNavigation($this);
		    $navigation->helloPrivate();
		    break;
		}
	    default : {
		    $this->redirect($this->name, "home");
		    break;
		}
	}
    }

    public function setMainTemplate($navigationFolder, $navigationFile, $title = "") {
	parent::setMainTemplate($navigationFolder, $navigationFile, $title);
	$this->setHTMLVariableTemplate('LINK-ACCOUNT', $this->getAppUrl($this->name, "account"));
	$this->setHTMLVariableTemplate('LINK-LOGOUT', $this->getAppUrl($this->name, "logout"));
	$this->parseTemplate("USER");
	if ($this->user->getConfirmemailtoken()) {
	    $this->setHTMLVariableTemplate('LINK-REVALIDATE', $this->getAppUrl($this->name, "revalidate", array(new Param("token", $this->user->getConfirmemailtoken()))));
	    $this->parseTemplate("REVALIDATE");
	}
    }

}
