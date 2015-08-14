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
//add custom require here
require_once("data/AtomManager.php");
require_once("beans/Constant.php");
require_once("beans/SystemUser.php");
require_once("beans/SystemUserLog.php");

class LangFactory {

    public static function getAvailableLangList() {
	return array("es", "en", "pt");
    }

    public static function getDefaultLang() {
	return "es";
    }

}

//manage applications here, do not rename this class or functions
class ApplicationFactory {

    public static function makeScriptApplication($app, $config, $lang) {
	$application = null;
	return $application;
    }

    public static function defaultScriptApplication($config, $lang) {
	echo "wrong param " . $config['param_app'] . "\n";
    }

    public static function makeWebApplication($app, $config, $lang) {
	$application = null;
	switch ($app) {
	    case "private": {
		    require_once("app/PrivateApplication.php");
		    $application = new PrivateApplication($config, $lang);
		    break;
		}
	    case "json": {
		    require_once("app/JsonApplication.php");
		    $application = new JsonApplication($config, $lang);
		    break;
		}
	    default : {
		    require_once("app/HomeApplication.php");
		    $application = new HomeApplication($config, $lang);
		}
	}
	return $application;
    }

    public static function defaultWebApplication($config, $lang) {

    }

}
