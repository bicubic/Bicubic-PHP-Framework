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

require_once("nav/AccountNavigation.php");

class HelloNavigation extends AccountNavigation {

    function __construct(Application $application) {
	parent::__construct($application);
    }

    public function hello() {
	$this->checkSignedInUser("private", "home");
	$this->application->setMainTemplate("hello", "hello");
	$this->application->setVariableTemplate("SIGNUP-FORM", $this->makeSignUpForm());
	$this->application->render();
    }

    public function helloJson() {
	$json = new ObjectJson();
	$json->myhello = $this->lang('text_helloworld');
	$this->application->renderToJson($json);
    }

    public function helloPrivate() {
	$this->application->setMainTemplate("hello", "helloprivate");
	$this->application->setHtmlVariableTemplate("USER", $this->application->user->getName());
	$this->application->render();
    }

}
