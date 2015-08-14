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
