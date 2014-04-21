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
            case "beans" : {
                    $this->generateBeans();
                    break;
                }
            case "password" : {
                    $this->generatePassword();
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

    private function generateBeans() {
        $data = new PostgreSQLData($this->config);
        $query = "SELECT table_name FROM information_schema.tables WHERE table_schema='public'";
        $result = $data->performRead($query);
        $classes = array();
        while ($row = $data->readNext($result)) {
            $class = $row['table_name'];
            $classes [] = $class;
        }

        foreach ($classes as $class) {
            $query = "SELECT column_name FROM information_schema.columns WHERE table_name ='$class'";
            $result = $data->performRead($query);
            echo "begin class $class \n";
            while ($row = $data->readNext($result)) {
                $column = $row['column_name'];
                echo 'private $' . $column . ";\n";
            }
            echo "end class $class \n";
        }
        
        foreach ($classes as $class) {
            $query = "SELECT column_name FROM information_schema.columns WHERE table_name ='$class'";
            $result = $data->performRead($query);
            echo "begin class $class \n";
            while ($row = $data->readNext($result)) {
                $column = $row['column_name'];
                echo "\"$column\" => [\"name\" => \"$column\", \"type\" => PropertyTypes::\$_LONG , \"required\" => true, \"serializable\" => true, \"updatenull\" => true, \"hidden\" => false, \"private\" => false],\n";
            }
            echo "end class $class \n";
        }
    }

    private function generatePassword() {
        $clave = $this->navigation = $this->getUrlParam("password", "string");
        echo $clave . " converted to " . $this->blowfishCrypt($clave, 10);
        echo "\n";
    }

}

?>