<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
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
                    $this->script_generateDB();
                    break;
                }
            case "password" : {
                    $this-> script_generatePassword();
                    break;
                }
            case "lang" : {
                    $this-> script_generateLangFiles();
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
?>