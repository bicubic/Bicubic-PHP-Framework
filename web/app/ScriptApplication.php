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
        while ($row = $data->readNext($result)) {
            $string = "";
            $class = $cammelName = strtoupper(substr($row['table_name'], 0, 1)) . substr($row['table_name'], 1);
            $string .= "<?php\n";
            $string .= "class $class extends DataObject {\n";
            $string .= "\n";
            $string .= "}\n";
            $string .= "?>\n";
            $string .= "\n";
            $string .= "\n";
            $string .= "\n";
            $string .= "\n";
            $string .= "\n";
            $string .= "\n";
            $string .= "function $setter(\$value) { \n";
            $string .= "function $setter(\$value) { \n";
            $string .= "    \$this->$item = \$value; \n";
            $string .= "} \n";
            $string .= " \n";
            $string .= "function $getter() { \n";
            $string .= "    return \$this->$item; \n";
            $string .= "} \n";
            $string .= " \n";
        }
    }

    private function generatePassword() {
        $clave = 'admin';
        echo $clave . " converted to " . $this->blowfishCrypt($clave, 10);
        echo "\n";
    }

}

?>