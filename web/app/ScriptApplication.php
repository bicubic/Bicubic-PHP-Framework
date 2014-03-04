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
        $this->navigation = $this->getUrlParam($this->config('param_navigation'), "letters");
        switch ($this->navigation) {
            case "password" : {
                    $this->generatePassword();
                    break;
                }
            case "message" : {
                    require_once("nav/MessageNavigation.php");
                    $messageNavigation = new MessageNavigation($this);
                    $messageNavigation->message();
                    break;
                }
            default : {
                    echo "no nav";
                    break;
                }
        }
    }

    public function setMainTemplate($navigationFolder, $navigationFile, $title = "") {
        parent::setMainTemplate($navigationFolder, $navigationFile, $title);
    }

    public function error($message) {
        echo $message;
        parent::error($message);
    }

    private function generatePassword() {
        $clave = 'admin';
        echo $this->blowfishCrypt($clave, 10);
        echo "\n";
    }

}

?>