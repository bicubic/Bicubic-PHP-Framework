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

class ScriptApplication extends Application {

    function __construct($config, $lang, $data = null, $name = "script") {
	if (!$data) {
	    $data = new PostgreSQLData($config);
	}
	parent::__construct($config, $lang, $data, $name);
    }

    public function execute() {
	parent::execute();
	$this->navigation = $this->getUrlParam($this->config('param_navigation'), PropertyTypes::$_LETTERS, false);
	switch ($this->navigation) {
	    case "data" : {
		    $this->script_generateDB(true);
		    break;
		}
	    case "db" : {
		    $this->script_generateDB(false);
		    break;
		}
	    case "password" : {
		    $this->script_generatePassword();
		    break;
		}
	    case "lang" : {
		    $this->script_generateLangFiles();
		    break;
		}
	    default : {
		    echo "no nav";
		    break;
		}
	}
    }

    public function error($message) {
	echo $message;
	$this->endApp();
    }

}
