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
