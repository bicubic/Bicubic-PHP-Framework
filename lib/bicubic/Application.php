<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
class Application {

    //logged user object
    public $user;
    //configuration data
    public $config;
    //languaje data
    public $lang;
    //data acces of the controlling $application
    public $data;
    //name of the controlling $application
    public $name;
    //template
    public $tpl;
    //navigation
    public $navigation;

    /**
     * generates a new application with HTLM parser
     * @param string $config <p>El archivo de configuracion</p>
     * @param string $lang <p>El archivo de lenguaje</p>
     * @param string $data <p>El motor de base de datos</p>
     * @param string $name <p>El nombre de la aplicacion</p>
     * @return una nueva aplicacion
     */
    function __construct($config, $lang, $data, $name) {
        $this->config = $config;
        $this->lang = $lang;
        $this->name = $name;
        $this->data = $data;
        $this->tpl = new HTML_Template_Sigma();
    }

    /**
     * Ejecuta la correspondiente navegacion
     * @return Nada
     */
    public function execute() {
        session_start();
        if ($this->config("maintenance")) {
            $this->error($this->lang('lang_maintenance'));
        }
    }

    /**
     * Construye una nueva aplicacion
     * @param string $config <p>El archivo de configuracion</p>
     * @param string $lang <p>El archivo de lenguaje</p>
     * @param string $data <p>El motor de base de datos</p>
     * @param string $name <p>El nombre de la aplicacion</p>
     * @return una nueva aplicacion
     */
    public function getSecureAppUrl($app, $navigation, $params = null) {
        $link = $this->config('web_secure_url') . "?" . $this->config('param_app') . "=" . $app;
        $link .= "&" . $this->config('param_navigation') . "=" . $navigation;
        $hasLang = false;
        if ($params) {
            foreach ($params as $param) {
                $link .= "&" . $param->name . "=" . $param->value;
            }
            if ($param->name == $this->config('param_lang')) {
                $hasLang = true;
            }
        }
        if (!$hasLang) {
            $link .= "&" . $this->config('param_lang') . "=" . $this->config("lang");
        }
        return $link;
    }

    /**
     * Construye una nueva aplicacion
     * @param string $config <p>El archivo de configuracion</p>
     * @param string $lang <p>El archivo de lenguaje</p>
     * @param string $data <p>El motor de base de datos</p>
     * @param string $name <p>El nombre de la aplicacion</p>
     * @return una nueva aplicacion
     */
    public function getSecureAppFlatUrl($app, $navigation, $id) {
        $link = $this->config('web_folder') . "$app/$navigation/$id";
        return $link;
    }

    /**
     * Construye una nueva aplicacion
     * @param string $config <p>El archivo de configuracion</p>
     * @param string $lang <p>El archivo de lenguaje</p>
     * @param string $data <p>El motor de base de datos</p>
     * @param string $name <p>El nombre de la aplicacion</p>
     * @return una nueva aplicacion
     */
    public function getAppUrl($app, $navigation, $params = null) {
        $link = $this->config('web_url') . "?" . $this->config('param_app') . "=" . $app;
        $link .= "&" . $this->config('param_navigation') . "=" . $navigation;
        $hasLang = false;
        if ($params) {
            foreach ($params as $param) {
                $link .= "&" . $param->name . "=" . $param->value;
            }
            if ($param->name == $this->config('param_lang')) {
                $hasLang = true;
            }
        }
        if (!$hasLang) {
            $link .= "&" . $this->config('param_lang') . "=" . $this->config("lang");
        }
        return $link;
    }

    /**
     * Construye una nueva aplicacion
     * @param string $config <p>El archivo de configuracion</p>
     * @param string $lang <p>El archivo de lenguaje</p>
     * @param string $data <p>El motor de base de datos</p>
     * @param string $name <p>El nombre de la aplicacion</p>
     * @return una nueva aplicacion
     */
    public function getUrlParam($name, $type, $force = false) {
        if (isset($_GET[$name])) {
            $value = $_GET[$name];
            $value = $this->filter($value, $type);
            if ($force && !isset($value)) {
                $this->error($this->lang('notvalid') . " : " . (array_key_exists($name, $this->lang) ? $this->lang($name) : $name));
            }
            return $value;
        }
        if ($force) {
            $this->error($this->lang('notvalid') . " : " . (array_key_exists($name, $this->lang) ? $this->lang($name) : $name));
        }
        return null;
    }

    /**
     * Obtiene una variable de formulario
     * @param string $config <p>El archivo de configuracion</p>
     * @param string $lang <p>El archivo de lenguaje</p>
     * @param string $data <p>El motor de base de datos</p>
     * @param string $name <p>El nombre de la aplicacion</p>
     * @return una nueva aplicacion
     */
    public function getFormParam($name, $type, $force = false) {
        if (isset($_POST[$name])) {
            $value = $_POST[$name];
            $value = $this->filter($value, $type);
            if ($force && !isset($value)) {
                $this->error($this->lang('lang_notvalid') . " : " . (array_key_exists($name, $this->lang) ? $this->lang($name) : $name));
            }
            return $value;
        } else if (isset($_FILES[$name])) {
            $value = $_FILES[$name]['name'];
            $value = $this->filter($value, $type);
            if ($force && !isset($value)) {
                $this->error($this->lang('lang_notvalid') . " : " . (array_key_exists($name, $this->lang) ? $this->lang($name) : $name));
            }
            return $value;
        }
        if ($force) {
            $this->error($this->lang('lang_notvalid') . " : " . (array_key_exists($name, $this->lang) ? $this->lang($name) : $name));
        }
        return null;
    }

    /**
     * Obtiene Un objeto de variables provenientes de un formulario
     * @return
     */
    public function getFormObject(DataObject $object, $force = true) {
        $objectName = get_class($object);
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        foreach ($properties as $property) {
            $fieldname = $property["name"];
            $paramName = strtoupper($fieldname);
            $cammelName = strtoupper(substr($fieldname, 0, 1)) . substr($fieldname, 1);
            $setter = "set$cammelName";
            $object->$setter($this->getFormParam("$objectName" . "_" . "$fieldname", $property["type"], false));
        }
        if ($force && !$object->__isComplete()) {
            $this->error($this->lang('lang_notcomplete'));
        }
        return $object;
    }

    public function getJsonParam($force = false) {
        $value = file_get_contents("php://input");
        $json = json_decode($value);
        if (isset($json)) {
            $json = get_object_vars($json);
            return $json;
        }
        if ($force) {
            $this->error($this->lang('lang_jsonnotvalid'));
        }
        return null;
    }

    /**
     * Obtiene una variable de session
     * @param string $name <p>El nombre de la variable</p>
     * @param string $type <p>El tipo de la variable para aplicar el filtro</p>
     * @return la variable de session o null si no existe o el filtro no corresponde
     */
    public function getSessionParam($name, $type="flat") {
        if (isset($_SESSION[$name])) {
            $value = $_SESSION[$name];
//            return $this->filter($value, $type);
            return $value;
        }
        return null;
    }

    /**
     * Elimina una variable de session
     * @param string $name <p>El nombre de la variable</p>
     * @param string $type <p>El tipo de la variable para aplicar el filtro</p>
     * @return la variable de session o null si no existe o el filtro no corresponde
     */
    public function killSessionParam($name) {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * Setea una variable de session
     * @param string $config <p>El archivo de configuracion</p>
     * @param string $lang <p>El archivo de lenguaje</p>
     * @param string $data <p>El motor de base de datos</p>
     * @param string $name <p>El nombre de la aplicacion</p>
     * @return una nueva aplicacion
     */
    public function setSessionParam($name, $value) {
        $_SESSION[$name] = $value;
    }

    /** Filtra una variable segun el tipo que debiese ser
     * Tambien parsea caracteres que intervengan en el lenguaje de consulta de base de datos
     * @param object $value <p>La variable a verificar</p>
     * @param string $type <p>El tipo que debiese ser la variable int, double, string, flat, letters, alphanumeric, boolean, int-array, string-array</p>
     * @return el valor de la variable filtrado, o null si no corresponde al tipo
     */
    public function filter($value, $type) {
        switch ($type) {
            case "int" : {
                    if ($value !== "" && $value >= 0) {
                        $vals = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "+", ".", ",");
                        $trimed = str_replace($vals, "", $value);
                        if (is_numeric($value) && $trimed === "") {
                            $dotpos = strpos($value, ".");
                            if ($dotpos !== FALSE) {
                                $value = substr($value, 0, $dotpos);
                            }
                            $dotpos = strpos($value, ",");
                            if ($dotpos !== FALSE) {
                                $value = substr($value, 0, $dotpos);
                            }
                            return $value;
                        }
                    }
                    break;
                }
            case "long" : {
                    if ($value !== "" && $value >= 0) {
                        $vals = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "+");
                        $trimed = str_replace($vals, "", $value);
                        if (is_numeric($value) && $trimed === "") {
                            return $value;
                        }
                    }
                    break;
                }
            case "numeric" : {
                    if ($value !== "") {
                        $trimed = str_replace(array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "+", ",", ".", "$", "(", ")", "%"), "", $value);
                        if ($trimed === "") {
                            $value = str_replace(array(".", "$", "(", ")", "%"), "", $value);
                            $value = strtok($value, ",");
                            return $value;
                        }
                    }
                    break;
                }
            case "double" : {
                    if ($value !== "") {
                        $trimed = str_replace(array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "+", ",", ".", "$", "(", ")", "%"), "", $value);
                        if ($trimed === "") {
                            $value = str_replace(array("$", "(", ")", "%"), "", $value);
                            $value = str_replace(array(","), ".", $value);
                            return doubleval($value);
                        }
                    }
                    break;
                }
            case "percentage" : {
                    if ($value !== "") {
                        $trimed = str_replace(array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "+", ",", ".", "$", "(", ")", "%"), "", $value);
                        if ($trimed === "") {
                            $value = str_replace(array("$", "(", ")", "%"), "", $value);
                            $value = str_replace(array(","), ".", $value);
                            return $value * 100;
                        }
                    }
                    break;
                }
            case "string" : {
                    if ($value) {
                        $value = trim($value);
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string2048" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 2048));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string1024" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 1024));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string256" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 256));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string128" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 128));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string64" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 64));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string32" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 32));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string24" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 24));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string16" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 16));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string8" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 8));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string4" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 4));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string2" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 2));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "string1" : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 1));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case "flat" : {
                    return ($value);
                    break;
                }
            case "letters" : {
                    if ($value !== "") {
                        if (preg_match("/^[a-zA-Z]*$/", $value)) {
                            return ($value);
                        }
                    }
                    break;
                }
            case "alphanumeric" : {
                    if ($value !== "") {
                        if (preg_match("/^[a-zA-Z0-9]*$/", $value)) {
                            return ($value);
                        }
                    }
                    break;
                }
            case "alpha32" : {
                    if ($value !== "") {
                        if (preg_match("/^[a-zA-Z]*$/", $value)) {
                            return (substr($value, 0, 32));
                        }
                    }
                    break;
                }
            case "date" : {
                    if ($value !== "") {
                        if (preg_match("/^[0-9\-]*$/", $value)) {
                            $numbers = explode("-", $value);
                            if (count($numbers) == 3) {
                                $time = strtotime("$numbers[1]/$numbers[2]/$numbers[0]");
                                if ($time)
                                    return $time;
                            }
                        }
                        if (preg_match("/^[0-9\/]*$/", $value)) {
                            $numbers = explode("/", $value);
                            if (count($numbers) == 3) {
                                $time = strtotime("$numbers[1]/$numbers[2]/$numbers[0]");
                                if ($time)
                                    return $time;
                            }
                        }
                    }
                    break;
                }
            case "boolean" : {
                    if ($value !== "") {
                        $vals = array("f", "t", "0", "1");
                        $trimed = str_replace($vals, "", $value);
                        if (empty($trimed)) {
                            return substr($value, 0, 1);
                        }
                    }
                    break;
                }
            case "int-array" : {
                    $newvalue = array();
                    if (!is_array($value)) {
                        $value = explode(",", str_replace(array("(", ")"), "", $value));
                    }
                    foreach ($value as $element) {
                        if ($element === "") {
                            continue;
                        }
                        $vals = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "+");
                        $trimed = str_replace($vals, "", $element);
                        if ((!is_numeric($element) || !empty($trimed))) {
                            continue;
                        }
                        $element = str_replace(array("-", "+"), "", $element);
                        $newvalue [] = $element;
                    }
                    return $newvalue;
                    break;
                }
            case "double-array" : {
                    $newvalue = array();
                    if (!is_array($value)) {
                        $value = explode(",", str_replace(array("(", ")"), "", $value));
                    }
                    foreach ($value as $key => $element) {
                        if ($element === "") {
                            continue;
                        }
                        $trimed = str_replace(array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "+", ",", ".", "$", "(", ")", "%"), "", $element);
                        if (!empty($trimed)) {
                            continue;
                        }
                        $element = str_replace(array("$", "(", ")", "%"), "", $element);
                        $element = str_replace(array(","), ".", $element);
                        $newvalue [] = $element;
                    }
                    return $newvalue;
                    break;
                }
            case "string-array" : {
                    if (!is_array($value)) {
                        $value = explode(",", str_replace(array("(", ")"), "", $value));
                    }
                    foreach ($value as $element) {
                        if ($element) {
                            $element = trim($element);
                            if (!$element) {
                                $correct = false;
                                break;
                            }
                        } else {
                            $correct = false;
                            break;
                        }
                    }
                    return $value;
                    break;
                }
            case "json" : {
                    $object = json_decode(($value));
                    if ($object)
                        return $object;
                    else
                        return null;
                    break;
                }
            default : {
                    return null;
                }
        }
        return null;
    }

    /**
     * Verifica que un string sea de formato UTF8
     * @param string $string <p>El string a verificar</p>
     * @return true si el string es UTF8 false si no
     */
    public function is_utf8($string) {
        if (strlen($string) > 5000) {
            for ($i = 0, $s = 5000, $j = ceil(strlen($string) / 5000); $i < $j; $i++, $s+=5000) {
                if (is_utf8(substr($string, $s, 5000)))
                    return true;
            }
            return false;
        } else {
            return preg_match('%^(?:
                [\x09\x0A\x0D\x20-\x7E]            # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )*$%xs', $string);
        }
    }

    /**
     * Transforma un string UTF-8 a formato HTML
     * @param string $utf8 <p>El string a convertir a HTML de formato UTF8</p>
     * @param bool $encodeTags <p>True si se desean convertir los caracteres a elementos de HTML</p>
     * @return el string resultante de la operacion
     */
    public function utf8tohtml($utf8, $encodeTags) {
        $result = '';
        for ($i = 0; $i < strlen($utf8); $i++) {
            $char = $utf8[$i];
            $ascii = ord($char);
            if ($ascii < 128) {
                // one-byte character
                $result .= ( $encodeTags) ? htmlentities($char) : $char;
            } else if ($ascii < 192) {
                // non-utf8 character or not a start byte
            } else if ($ascii < 224) {
                // two-byte character
                $result .= htmlentities(substr($utf8, $i, 2), ENT_QUOTES, 'UTF-8');
                $i++;
            } else if ($ascii < 240) {
                // three-byte character
                $ascii1 = ord($utf8[$i + 1]);
                $ascii2 = ord($utf8[$i + 2]);
                $unicode = (15 & $ascii) * 4096 +
                        (63 & $ascii1) * 64 +
                        (63 & $ascii2);
                $result .= "&#$unicode;";
                $i += 2;
            } else if ($ascii < 248) {
                // four-byte character
                $ascii1 = ord($utf8[$i + 1]);
                $ascii2 = ord($utf8[$i + 2]);
                $ascii3 = ord($utf8[$i + 3]);
                $unicode = (15 & $ascii) * 262144 +
                        (63 & $ascii1) * 4096 +
                        (63 & $ascii2) * 64 +
                        (63 & $ascii3);
                $result .= "&#$unicode;";
                $i += 3;
            }
        }
        return $result;
    }

    /**
     * Verifica que exista un session de login activa
     * @return El usuario de la session activa
     */
    public function loginCheck() {
        //Check Params
        $login = $this->getSessionParam("BAClogin");
        $user = $this->getSessionParam("BACuser");
        $rememberme = $this->getSessionParam("BACrememberme");
        if (!isset($login)) {
            return false;
        }
        if (!isset($user)) {
            return false;
        }
        if (!$login) {
            return false;
        }
        //Check time out
        if (!$rememberme && $this->config('web_time_out') > 0) {
            $time = $this->getSessionParam("BACtime");
            if (!isset($time)) {
                return false;
            }
            if ($time + $this->config('web_time_out') < time()) {
                return false;
            }
            $this->setSessionParam("time", time());
        }

        if ($user !== false) {
            if ($this->data != null) {
                $data = new TransactionManager($this->data);
                $dataBaseUser = $data->getRecord($user);
                if (isset($dataBaseUser) && $user->getToken() === $dataBaseUser->getToken()) {
                    return $user;
                }
            } else {
                return $user;
            }
        }
        return false;
    }

    /**
     * Setea los datos de session del login
     * @param SystemUser $user <p>El usuario de session</p>
     * @return Nada
     */
    public function loginSet($user, $rememberme = false) {
        $this->setSessionParam("BAClogin", true);
        $this->setSessionParam("BACuser", $user);
        $this->setSessionParam("BACtime", time());
        $this->setSessionParam("BACrememberme", $rememberme);
    }

    /**
     * Elimina los datos de session del login
     * @return Nada
     */
    public function loginUnset() {
        $this->killSessionParam("BAClogin");
        $this->killSessionParam("BACuser");
        $this->killSessionParam("BACtime");
        $this->killSessionParam("BACrememberme");

        session_destroy();
    }

    /**
     * Arroja un mensaje de error y detiene la ejecucion
     * @param string $message <p>El mensaje de error a mostrar</p>
     * @param string $link <p>Una URL para incluir en el link</p>
     * @param string $linkText <p>El texto del link</p>
     * @return Nada
     */
    public function error($message) {
        $this->name = "error";
        $this->navigation = "error";
        $this->setMainTemplate("message", "error");
        $this->setHTMLVariableTemplate('MESSAGE-TEXT', $this->lang($message));
        $this->render();
    }

    public function message($message) {
        $this->name = "message";
        $this->navigation = "message";
        $this->setMainTemplate("message", "message");
        $this->setHTMLVariableTemplate('MESSAGE-TEXT', $this->lang($message));
        $this->render();
    }

    /**
     * Setea la navegacion y carga el template general
     * @param string $navigationFolder <p>La carpeta de la vista</p>
     * @param string $navigationFile <p>El nombre del archivo de vista (sin la extension)</p>
     * @return Nada
     */
    public function setMainTemplate($navigationFolder, $navigationFile, $title="", $priority="html") {
        if ($priority != "html") {
            if ($this->tpl->loadTemplateFile($this->config('folder_template') . "$this->name/template." . $priority) === SIGMA_OK) {
                $this->tpl->addBlockfile("TEMPLATE-CONTENT", $this->name, $this->config('folder_navigation') . "$navigationFolder/$navigationFile." . $priority);
            }
        } else if ($this->tpl->loadTemplateFile($this->config('folder_template') . "$this->name/template.html") === SIGMA_OK) {
            $this->tpl->addBlockfile("TEMPLATE-CONTENT", $this->name, $this->config('folder_navigation') . "$navigationFolder/$navigationFile.html");
            $this->tpl->addBlockfile("TEMPLATE-JAVASCRIPT", $this->name . "_javascript", $this->config('folder_navigation') . "$navigationFolder/$navigationFile.js");
            $this->tpl->addBlockfile("TEMPLATE-CSS", $this->name . "_css", $this->config('folder_navigation') . "$navigationFolder/$navigationFile.css");
        } else if ($this->tpl->loadTemplateFile($this->config('folder_template') . "$this->name/template.xml") === SIGMA_OK) {
            $this->tpl->addBlockfile("TEMPLATE-CONTENT", $this->name, $this->config('folder_navigation') . "$navigationFolder/$navigationFile.xml");
        } else if ($this->tpl->loadTemplateFile($this->config('folder_template') . "$this->name/template.json") === SIGMA_OK) {
            $this->tpl->addBlockfile("TEMPLATE-CONTENT", $this->name, $this->config('folder_navigation') . "$navigationFolder/$navigationFile.json");
        }

        $this->setHTMLVariableTemplate("TEMPLATE-TITLE", $title);
    }

    public function setCustomTemplate($navigationFolder, $navigationFile) {
        $file = $this->config('folder_navigation') . "$navigationFolder/$navigationFile." . "html";
        $tpl = new HTML_Template_Sigma();
        if ($tpl->loadTemplateFile($file) === SIGMA_OK) {
            return $tpl;
        }
        return null;
    }

    public function setHTMLVariableCustomTemplate($tpl, $name, $value) {
        $value = strval($value);
        $var = $this->utf8tohtml($value, true);
        $var = str_replace("\r\n", "<br />", $var);
        $var = str_replace("\n", "<br />", $var);
        $tpl->setVariable($name, $var);
    }

    public function setHTMLArrayCustomTemplate($tpl, $array) {
        foreach ($array as &$value) {
            $value = strval($value);
            $value = $this->utf8tohtml($value, true);
            $value = str_replace("\r\n", "<br />", $value);
            $value = str_replace("\n", "<br />", $value);
        }
        $tpl->setVariable($array);
    }

    public function parseCustomTemplate($tpl, $name) {
        $this->setArrayLangCustomItems($tpl, $name);
        $tpl->parse($name);
    }
    
    private function setArrayLangCustomItems($tpl, $blockName) {
        $prefix = "TEXT";
        $placeholders = $tpl->getPlaceholderList($blockName);
        foreach ($placeholders as $placeholder) {
            if (is_string($placeholder) && strpos($placeholder, "$prefix-") !== false) {
                $var = substr($placeholder, 5);
                $name = strtolower($var);
                $this->setHTMLVariableCustomTemplate($tpl, "$prefix-$var", $this->lang("lang_$name"));
            }
        }
    }

    public function renderCustomTemplate($tpl) {
        $this->setLangCustomItems($tpl);
        return $tpl->get();
    }
    
    private function setLangCustomItems($tpl) {
        $prefix = "LANG";
        $placeholders = $tpl->getPlaceholderList();
        foreach ($placeholders as $placeholder) {
            if (strpos($placeholder, "$prefix-") !== false) {
                $var = substr($placeholder, 5);
                $name = strtolower($var);
                $this->setHTMLVariableCustomTemplate($tpl, "$prefix-$var", $this->lang("lang_$name"));
            }
        }
    }

    /**
     * Llena los elementos de idioma de los templates cargados
     * @param string $navigationFolder <p>La carpeta de la vista</p>
     * @param string $navigationFile <p>El nombre del archivo de vista (sin la extension)</p>
     * @return Nada
     */
    private function setLangItems($blockName) {
        $prefix = "LANG";
        $placeholders = $this->tpl->getPlaceholderList();

        foreach ($placeholders as $placeholder) {
            if (strpos($placeholder, "$prefix-") !== false) {
                $var = substr($placeholder, 5);
                $name = strtolower($var);
                $this->setVariableTemplate("$prefix-$var", $this->lang("lang_$name"));
            }
        }

        $placeholders = $this->tpl->getPlaceholderList($blockName);

        foreach ($placeholders as $placeholder) {
            if (is_string($placeholder) && strpos($placeholder, "$prefix-") !== false) {
                $var = substr($placeholder, 5);
                $name = strtolower($var);
                $this->setVariableTemplate("$prefix-$var", $this->lang("lang_$name"));
            }
        }
    }

    /**
     * Llena los elementos de idioma de los templates cargados
     * @param string $navigationFolder <p>La carpeta de la vista</p>
     * @param string $navigationFile <p>El nombre del archivo de vista (sin la extension)</p>
     * @return Nada
     */
    private function setArrayLangItems($blockName) {
        $prefix = "TEXT";
        $placeholders = $this->tpl->getPlaceholderList($blockName);
        foreach ($placeholders as $placeholder) {
            if (is_string($placeholder) && strpos($placeholder, "$prefix-") !== false) {
                $var = substr($placeholder, 5);
                $name = strtolower($var);
                $this->setVariableTemplate("$prefix-$var", $this->lang("lang_$name"));
            }
        }
    }

    /**
     * Construye un formulario
     * @param string $name el nombre del formulario
     * @param array $params la lista de parametros u objetos del formulario
     * @param string $application el nombre de la aplicacion de destino
     * @param string $navigation el nombre de la navegacion de destino
     * @param boolean $secure si el formulario debe enviarse de forma segura
     */
    public function setFormTemplate($name, array $params, $application, $navigation, $secure = false, $urlparams = null) {
        $name = strtoupper($name);
        $this->setVariableTemplate("$name-ID", $this->navigation . "$name");
        if ($secure) {
            $this->setVariableTemplate("$name-ACTION", $this->getSecureAppUrl($application, $navigation, $urlparams));
        } else {
            $this->setVariableTemplate("$name-ACTION", $this->getAppUrl($application, $navigation, $urlparams));
        }
        foreach ($params as $param) {
            if (get_class($param) == "Param") {
                $this->setFormParam($param, $name);
            } else if ($param instanceof DataObject) {
                $this->setFormObject($param, $name);
            }
        }
    }



    /**
     * Setea un objeto dentro de un formulario
     * @param DataObject $object el objeto del formulario
     * @param string $formName el nombre del formulario
     */
    private function setFormObject(DataObject $object, $formName) {
        $objectName = get_class($object);
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        $objectFormName = strtoupper($objectName);
        foreach ($properties as $property) {
            $fieldname = $property["name"];
            $paramName = strtoupper($fieldname);
            $cammelName = strtoupper(substr($fieldname, 0, 1)) . substr($fieldname, 1);
            $getter = "get$cammelName";
            if($this->item($property, "list", false)) {
                $listgetter = "get".$cammelName."List";
                $propertyName = $property["name"];
                $paramName = strtoupper($propertyName);
                $values = $object->$listgetter();
                $selected = $object->$getter();
                foreach ($values as $value => $text) {
                    $this->setHTMLArrayTemplate(array(
                        "$formName-LISTNAME-$objectFormName-$paramName" => "$objectName" . "_" . "$propertyName",
                        "$formName-LISTVALUE-$objectFormName-$paramName" => $value,
                        "$formName-LISTTEXT-$objectFormName-$paramName" => $this->lang($text),
                        "$formName-LISTSELECTED-$objectFormName-$paramName" => ($value == $selected) ? "selected" : ""
                    ));
                    $this->parseTemplate($paramName);
                }
            } else if ($this->item($property, "shortlist", false)) {
                $listgetter = "get".$cammelName."List";
                $propertyName = $property["name"];
                $paramName = strtoupper($propertyName);
                $values = $object->$listgetter();
                $selected = $object->$getter();
                foreach ($values as $value => $text) {
                    $this->setHTMLArrayTemplate(array(
                        "$formName-LISTNAME-$objectFormName-$paramName" => "$objectName" . "_" . "$propertyName",
                        "$formName-LISTVALUE-$objectFormName-$paramName" => $value,
                        "$formName-LISTTEXT-$objectFormName-$paramName" => $this->lang($text),
                        "$formName-LISTSELECTED-$objectFormName-$paramName" => ($value == $selected) ? "checked" : ""
                    ));
                    $this->parseTemplate($paramName);
                }
            }
            else {
                $value = $object->$getter();
                $this->setVariableTemplate("$formName-NAME-$objectFormName-$paramName", "$objectName" . "_" . "$fieldname");
                if ($property["type"] == "date") {
                    $value = $this->formatWiredDate($value);
                } else {
                    $value = $this->utf8tohtml(strval($value), true);
                }
                $this->setVariableTemplate("$formName-VALUE-$objectFormName-$paramName", $value);
            }
        }
    }

    /**
     * Setea una variable de formulario
     * @param Param $param <p>El parametro de formulario</p>
     * @param string $formName <p>El nombre del formulario</p>
     * @return Nada
     */
    private function setFormParam(Param $param, $formName) {
        $viewParam = strtoupper($param->name);
        $this->setVariableTemplate("$formName-NAME-" . $viewParam, $param->name);
        $this->setVariableTemplate("$formName-VALUE-" . $viewParam, $this->utf8tohtml(strval($param->value), true));
    }

    /**
     * Parsea un Tag y le asigna el valor correspondiente
     * @param string $name <p>El nombre del TAG</p>
     * @param string $value <p>El valor a asignar</p>
     * @return Nada
     */
    public function unescapeJsonVariable($value) {
        $value = strval($value);
        return $value;
    }

    /**
     * Parsea un Tag y le asigna el valor correspondiente
     * @param string $name <p>El nombre del TAG</p>
     * @param string $value <p>El valor a asignar</p>
     * @return Nada
     */
    public function setVariableTemplate($name, $value) {
        $value = strval($value);
        $this->tpl->setVariable($name, $value);
    }

    /**
     * Parsea un Tag y le asigna el valor correspondiente en formato HTML
     * @param string $name <p>El nombre del TAG</p>
     * @param string $value <p>El valor a asignar</p>
     * @return Nada
     */
    public function setHTMLVariableTemplate($name, $value) {
        $value = strval($value);
        $var = $this->utf8tohtml($value, true);
        $var = str_replace("\r\n", "<br />", $var);
        $var = str_replace("\n", "<br />", $var);
        $this->tpl->setVariable($name, $var);
    }

    /**
     * Parsea Tags de un bloque BEGIN END
     * @param array $array <p>El array con los tags como llaves y su valor correspondiente </p>
     * @return Nada
     */
    public function setArrayTemplate($array) {
        foreach ($array as &$value) {
            $value = strval($value);
        }
        $this->tpl->setVariable($array);
    }

    /**
     * Parsea Tags de un bloque BEGIN END en formato HTML
     * @param array $array <p>El array con los tags como llaves y su valor correspondiente </p>
     * @return Nada
     */
    public function setHTMLArrayTemplate($array) {
        foreach ($array as &$value) {
            $value = strval($value);
            $value = $this->utf8tohtml($value, true);
            $value = str_replace("\r\n", "<br />", $value);
            $value = str_replace("\n", "<br />", $value);
        }
        $this->tpl->setVariable($array);
    }

    /**
     * Parsea un bloque BEGIN END
     * @param string $name <p>El nombre del bloque a parsear</p>
     * @return Nada
     */
    public function parseTemplate($name) {
        $this->setArrayLangItems($name);
        $this->tpl->parse($name);
    }

    /**
     * Termina la aplicación y cierra los canales de base de datos
     * @return Nada
     */
    public function endApp() {
        if ($this->data) {
            $this->data->close();
        }
        exit();
    }

    /**
     * Parsea todo el template y arroja el HTML resultante al browser
     * @return NADA
     */
    public function render() {
        $this->setLangItems($this->name);
        $this->tpl->touchBlock($this->name);
        $this->tpl->touchBlock($this->name . "_onload");
        $this->tpl->touchBlock($this->name . "_javascript");
        $this->tpl->touchBlock($this->name . "_css");
        $this->tpl->touchBlock($this->navigation . "_onload");
        $this->tpl->touchBlock($this->navigation . "_javascript");
        $this->tpl->show();
        $this->endApp();
    }

    public function renderToJson($jsonObject) {
        //unsescape vars
        $jsonObject = $this->unescapeJsonObject($jsonObject);

        header('Content-type: application/json;charset=UTF8;');
        $str = json_encode($jsonObject);
        $compress = $this->getFormParam("cp", "string", false);
        if (isset($compress)) {
            if ($compress == "zlib") {
                //header("Content-Encoding: gzip"); 
                $str = gzcompress($str, 9);
            } else if ($compress == "gzip") {
                header("Content-Encoding: gzip");
                $str = gzencode($str, 9, FORCE_GZIP);
            }
        }
        echo $str;
        $this->endApp();
    }

    private function unescapeJsonObject($object) {
        if (is_string($object)) {
            $object = $this->unescapeJsonVariable($object);
            return $object;
        }
        if (is_array($object)) {
            foreach ($object as $key => $value) {
                $object[$key] = $this->unescapeJsonObject($value);
            }
            return $object;
        }
        if (is_object($object)) {
            $vars = get_object_vars($object);
            foreach ($vars as $key => $value) {
                $object->$key = $this->unescapeJsonObject($value);
            }
            return $object;
        }
        return $object;
    }

    /**
     * Parsea todo el template y arroja el HTML resultante al browser
     * @return NADA
     */
    public function renderToCss() {
        $this->tpl->touchBlock($this->name);
        $this->tpl->touchBlock($this->name . "_onload");
        $this->tpl->touchBlock($this->name . "_javascript");
        $this->tpl->touchBlock($this->name . "_css");
        $this->tpl->touchBlock($this->navigation . "_onload");
        $this->tpl->touchBlock($this->navigation . "_javascript");
        header('Content-type: text/css');
        $this->tpl->show();
        $this->endApp();
    }

    /**
     * Parsea todo el template y arroja el HTML resultante al browser
     * @return NADA
     */
    public function renderToJavascript() {
        $this->tpl->touchBlock($this->name);
        $this->tpl->touchBlock($this->name . "_onload");
        $this->tpl->touchBlock($this->name . "_javascript");
        $this->tpl->touchBlock($this->name . "_css");
        $this->tpl->touchBlock($this->navigation . "_onload");
        $this->tpl->touchBlock($this->navigation . "_javascript");
        header('Content-type: text/javascript');
        $this->tpl->show();
        $this->endApp();
    }

    /**
     * Parsea todo el template y lo imprimir en un PDF que es luego arrojado al browser
     * @return Nada
     */
    public function renderToPdf() {
        $this->tpl->touchBlock($this->name);
        $this->tpl->touchBlock($this->name . "_onload");
        $this->tpl->touchBlock($this->name . "_javascript");
        $this->tpl->touchBlock($this->navigation . "_onload");
        $this->tpl->touchBlock($this->navigation . "_javascript");
        $html = $this->tpl->get();
        $pdf = new TCPDF($this->config('pdf_page_orientation'), $this->config('pdf_unite'), $this->config('pdf_page_format'), true, 'UTF-8', false);
        $pdf->SetCreator($this->config('pdf_creator'));
        $pdf->SetAuthor($this->config('pdf_author'));
        $pdf->SetHeaderData(/* logo */ '', /* ancho logo */ '', /* título */ $this->lang('lang_title'), '');
        $pdf->setHeaderFont(Array($this->config('pdf_font_name_main'), '', $this->config('pdf_font_size_main')));
        $pdf->setFooterFont(Array($this->config('pdf_font_name_data'), '', $this->config('pdf_font_size_data')));
        $pdf->SetDefaultMonospacedFont($this->config('pdf_font_monospace'));
        $pdf->SetMargins($this->config('pdf_margin_left'), $this->config('pdf_margin_top'), $this->config('pdf_margin_right'));
        $pdf->SetHeaderMargin($this->config('pdf_margin_header'));
        $pdf->SetFooterMargin($this->config('pdf_margin_footer'));
        $pdf->SetAutoPageBreak(TRUE, $this->config('pdf_margin_bottom'));
        $pdf->setImageScale($this->config('pdf_image_scale_ratio'));
        $pdf->setFontSubsetting(true);
        $pdf->SetFont($this->config('pdf_font_name_data'), '', $this->config('pdf_font_size_data'), '', true);
        $pdf->AddPage();
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output($this->lang('lang_file'), 'I');
        $this->endApp();
    }

    public function renderToXls($contents, $filename) {
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Encoding: UTF-8');
        echo $contents;
    }

    public function renderToFile($contents, $filename) {
        file_put_contents($filename, $contents);
    }

    /**
     * Esconde un bloque BEGIN END
     * Esta funcion debe ser llamada antes de hacer render del template
     * @param string $name <p>El nombre del bloque</p>
     * @return Nada
     */
    public function hideBlockTemplate($name) {
        $this->tpl->parse($name);
        $this->tpl->hideBlock($name);
    }

    /**
     * Redirije a una navegacion
     * @param string $app <p>El nombre de la aplicacion a redirigir</p>
     * @param string $navigation <p>El nombre de la navegacion a redirigir</p>
     * @return Nada
     */
    public function redirect($app, $navigation, $params = null) {
        //Try redirect
        header(sprintf("Location: %s", $this->getAppUrl($app, $navigation, $params)));
        $this->endApp();
    }

    /**
     * Redirije a una URL
     * @param string $url <p>La URL a la cual redirigir</p>
     * @return Nada
     */
    public function redirectToUrl($url) {
        //Try redirect
        header(sprintf("Location: %s", $url));
        $this->endApp();
    }

    /**
     * Redirije a una navegacion de manera segura
     * @param string $app <p>El nombre de la aplicacion a redirigir</p>
     * @param string $navigation <p>El nombre de la navegacion a redirigir</p>
     * @return Nada
     */
    public function secureRedirect($app, $navigation, $params = null) {
        //Try redirect
        header(sprintf("Location: %s", $this->getSecureAppUrl($app, $navigation, $params)));
        $this->endApp();
    }

    /**
     * Descarga un archivo y lo arroja al browser para descarga
     * En caso de falla se arroja el error de la aplicacion
     * @param string $fileName <p>El nombre del archivo</p>
     * @param string $filepath <p>La ruta completa al archivo incluyendo el nombre</p>
     * @return Nada
     */
    public function download($fileName = null, $filepath = null) {
        if (!isset($fileName)) {
            $fileName = $this->getUrlParam($this->config('param_file_name'), "string");
            if (!isset($fileName)) {
                $this->error($this->lang('lang_filenotfound'));
            }
            $filepath = $this->config('server_down_folder') . $fileName;
        }
        if (!file_exists($filepath)) {
            $this->error($this->lang('lang_filenotfound'));
        }
        $fsize = filesize($filepath);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/force-download");
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Transfer-Encoding: binary");
        ob_clean();
        flush();
        readfile($filepath);
        $this->endApp();
    }

    /**
     * Carga un archivo pasado como parametro de un formulario
     * El formulario debe ser de enctype="multipart/form-data"
     * En caso de error se arroja el error de la aplicacion
     * @param string $fileParam <p>El nombre del parametro del campo file del formulario</p>
     * @param string $destFolder <p>La carpeta en la cual dejar el archivo</p>
     * @param bool $override <p>True si se quiere reemplazar el archivo existente, false si se quiere arrojr un error</p>
     * @param string $destname <p>El nombre del archivo de destino</p>
     * @param string $extensions_list <p>Extensiones MIME validas, separadas por coma</p>
     * @return Nada
     */
    public function upload($fileParam, $destFolder, $override, $destname, $extensions_list = null, $optional = false) {

        if (!isset($_FILES[$fileParam])) {
            if (!$optional) {
                $this->error($this->lang('lang_filenotfound'));
            } else {
                return;
            }
        }
        if ($_FILES[$fileParam]['error'] == UPLOAD_ERR_INI_SIZE) {
            if (!$optional) {
                $this->error($this->lang('lang_filesize'));
            } else {
                return;
            }
        }
        if (!is_uploaded_file($_FILES[$fileParam]['tmp_name'])) {
            if (!$optional) {
                $this->error($this->lang('lang_filenotuploaded'));
            } else {
                return;
            }
        }
        //TODO calidate extensions
        if (!$override && file_exists($destFolder . $destname)) {
            $this->error($this->lang('lang_fileexist'));
        }
        if (!move_uploaded_file($_FILES[$fileParam]['tmp_name'], $destFolder . $destname)) {
            $this->error($this->lang('lang_filenotmoved'));
        }

        return $destFolder . $destname;
    }

    /**
     * Carga un archivo pasado como parametro de un formulario
     * El formulario debe ser de enctype="multipart/form-data"
     * En caso de error se arroja el error de la aplicacion
     * @param string $fileParam <p>El nombre del parametro del campo file del formulario</p>
     * @param string $destFolder <p>La carpeta en la cual dejar el archivo</p>
     * @param string $destname <p>El nombre del archivo de destino</p>
     * @param string $extensions_list <p>Extensiones MIME validas, separadas por coma</p>
     * @return Nada
     */
    public function uploadGS($fileParam, $destFolder, $destname, $mandatory = false, $public = true, $image = false, $width = 256, $height = 256) {

        if (!isset($_FILES[$fileParam])) {
            if ($mandatory) {
                $this->error($this->lang('lang_filenotfound'));
            } else {
                return null;
            }
        }
        if ($_FILES[$fileParam]['error'] == UPLOAD_ERR_INI_SIZE) {
            if ($mandatory) {
                $this->error($this->lang('lang_filesize'));
            } else {
                return null;
            }
        }
        if (!is_uploaded_file($_FILES[$fileParam]['tmp_name'])) {
            if ($mandatory) {
                $this->error($this->lang('lang_filenotuploaded') . " " . $_FILES[$fileParam]['error']);
            } else {
                return null;
            }
        }


        //validate extensions
        if ($image) {
            $localPath = $_FILES[$fileParam]['tmp_name'];
            if (exif_imagetype($localPath) != IMAGETYPE_PNG) {
                if (exif_imagetype($localPath) != IMAGETYPE_JPEG) {
                    if ($mandatory) {
                        $this->error($this->lang('lang_filenotjpg'));
                    } else {
                        return null;
                    }
                }
            }

            if (isset($width) && isset($height) && $width == $height) {
                $imageData = getimagesize($localPath);
                $origin_w = $imageData[0];
                $origin_h = $imageData[1];
                if ($origin_w != $width || $origin_h != $height) {
                    $image_p = imagecreatetruecolor($width, $height);
                    if (!imagealphablending($image_p, false)) {
                        $this->error($this->lang('lang_imageblend'));
                    }
                    if (!imagesavealpha($image_p, true)) {
                        $this->error($this->lang('lang_imagealpha'));
                    }
                    $originalimage = FALSE;
                    if (exif_imagetype($localPath) == IMAGETYPE_PNG) {
                        $originalimage = imagecreatefrompng($localPath);
                    } else if (exif_imagetype($localPath) == IMAGETYPE_JPEG) {
                        $originalimage = imagecreatefromjpeg($localPath);
                    }
                    if ($originalimage === FALSE) {
                        $this->error($this->lang('lang_imagecreate'));
                    }
                    $bg = imagecolorallocate($image_p, 255, 255, 255);
                    if ($bg === FALSE) {
                        $this->error($this->lang('lang_imagecolor'));
                    }
                    if (!imagefill($image_p, 0, 0, $bg)) {
                        $this->error($this->lang('lang_imagefill'));
                    }

                    $x = 0;
                    $y = 0;
                    $w = $width;
                    $h = $height;

                    if ($origin_w <= $width && $origin_h <= $height) {
                        $x = ($width - $origin_w) / 2;
                        $y = ($height - $origin_h) / 2;
                        $w = $origin_w;
                        $h = $origin_h;
                    } else if ($origin_w > $width && $origin_h <= $height) {
                        $p = ($width) / $origin_w;
                        $x = 0;
                        $y = ($height - ($p * $origin_h)) / 2;
                        $w = $width;
                        $h = $p * $origin_h;
                    } else if ($origin_w <= $width && $origin_h > $height) {
                        $p = ($height) / $origin_h;
                        $x = ($width - ($p * $origin_w)) / 2;
                        $y = 0;
                        $w = $p * $origin_w;
                        $h = $height;
                    } else if ($origin_w > $width && $origin_h > $height) {
                        if ($origin_w == $origin_h) {
                            $x = 0;
                            $y = 0;
                            $w = $width;
                            $h = $height;
                        } else if ($origin_w > $origin_h) {
                            $p = ($width) / $origin_w;
                            $x = 0;
                            $y = ($height - ($p * $origin_h)) / 2;
                            $w = $width;
                            $h = $p * $origin_h;
                        } else if ($origin_w < $origin_h) {
                            $p = ($height) / $origin_h;
                            $x = ($width - ($p * $origin_w)) / 2;
                            $y = 0;
                            $w = $p * $origin_w;
                            $h = $height;
                        }
                    }

                    if (!imagecopyresampled($image_p, $originalimage, $x, $y, 0, 0, $w, $h, $origin_w, $origin_h)) {
                        $this->error($this->lang('lang_resample'));
                    }
                    if (!imagepng($image_p, $localPath, 9)) {
                        $this->error($this->lang('lang_save'));
                    }
                }
            }
        }

        //save object to GS account
        $localPath = $_FILES[$fileParam]['tmp_name'];
        $permission = "";
        if ($public) {
            $permission = "-a public-read";
        }

        $gspath = $this->config("gsutil") . " cp $permission $localPath " . $this->config('gsbucket') . $destFolder . $destname;
        $ouput;
        $result;
        exec($gspath, $ouput, $result);
//        foreach ($ouput as $logoutput) {
//            $arraylog = array();
//            $arraylog["gs"] = $logoutput;
//            $this->log($logoutput);
//        }
        if ($result === 0) {
            return $destFolder . $destname;
        } else {
            return null;
        }
    }

    public function uploadLocalGS($localPath, $destFolder, $destname, $public = false) {

        //save object to GS account
        $permission = "";
        if ($public) {
            $permission = "-a public-read";
        }

        $gspath = $this->config("gsutil") . " cp $permission $localPath " . $this->config('gsbucketprivate') . $destFolder . $destname;
        $ouput;
        $result;
        exec($gspath, $ouput, $result);
//        foreach ($ouput as $logoutput) {
//            $arraylog = array();
//            $arraylog["gs"] = $logoutput;
//            $this->log($logoutput);
//        }
        if ($result === 0) {
            return $destFolder . $destname;
        } else {
            return null;
        }
    }

    public function downloadLocalGS($gspathPath, $destFolder, $destname) {

        $gspath = $this->config("gsutil") . " cp gs://" . $gspathPath . " " . $destFolder . $destname;
        $ouput;
        $result;
        exec($gspath, $ouput, $result);
//        foreach ($ouput as $logoutput) {
//            $arraylog = array();
//            $arraylog["gs"] = $logoutput;
//            $this->log($logoutput);
//        }
        if ($result === 0) {
            return $destFolder . $destname;
        } else {
            return null;
        }
    }

    /**
     * Crea un string aleatoreo
     * @param int $lenght <p>El largo del string a crear</p>
     * @return una nuevo string aleatorio del largo indicado
     */
    public function createRandomString($lenght) {
        $chars = "abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-.";
        srand((double) microtime() * 1000000);
        $i = 1;
        $pass = '';
        while ($i <= $lenght) {
            $num = rand() % 33;
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }

        return $pass;
    }

    /**
     * Crea un string aleatoreo
     * @param int $lenght <p>El largo del string a crear</p>
     * @return una nuevo string aleatorio del largo indicado
     */
    public function createRandomNumber() {
        srand((double) microtime() * 1000000);
        $num = rand();

        return $num;
    }

    /**
     * Obtiene el numero total de paginas a mostrar en una navegacion con paginamiento
     * @param int $totalItems <p>El numero total de items a mostrar en una pagina</p>
     * @return el numero total de paginas
     */
    public function getTotalPages($totalItems) {
        $totalPages = ceil($totalItems / $this->config('web_page_items'));
        if ($totalPages <= 0) {
            $totalPages = 1;
        }
        return $totalPages;
    }

    /**
     * Obtiene la página actual de navegación con paginamiento
     * Las páginas comienzan en 0
     * @return El numero de la pagina actual
     */
    public function getCurrentPage() {
        $page = $this->getUrlParam($this->config('param_page'), "int", false);
        if (!isset($page)) {
            $page = 0;
        }
        if ($page < 0) {
            $this->error($this->lang('lang_pagnotfound'));
        }
        return $page;
    }

    /**
     * Setea la navegación con paginación
     * @param int $currentPage <p>La página actual</p>
     * @param int $totalPages <p>El número total de páginas</p>
     * @param string $app <p>La Aplicación a la cual redirigir</p>
     * @param string $nav <p>LA navegación a la cual redirigir</p>
     * @return Nada
     */
    public function setPageNavigation($currentPage, $totalItems, $app, $nav) {
        $totalPages = $this->getTotalPages($totalItems);
        $this->setHTMLVariableTemplate("PAGE-NUMBER", $currentPage + 1);
        $this->setHTMLVariableTemplate("PAGE-TOTAL", $totalPages);
        $linkback = $this->getSecureAppUrl($app, $nav, array(new Param($this->config('param_page'), $currentPage - 1)));
        $linkfordware = $this->getSecureAppUrl($app, $nav, array(new Param($this->config('param_page'), $currentPage + 1)));
        if ($currentPage + 1 > 1) {
            $this->setHTMLVariableTemplate("PAGE-LINK-BACK", $linkback);
            $this->setHTMLVariableTemplate("PAGE-TEXT-BACK", array_key_exists('lang_back', $this->lang) ? $this->lang('lang_back') : 'lang_back');
        }
        if ($currentPage + 1 < $totalPages) {
            $this->setHTMLVariableTemplate("PAGE-LINK-NEXT", $linkfordware);
            $this->setHTMLVariableTemplate("PAGE-TEXT-NEXT", array_key_exists('lang_next', $this->lang) ? $this->lang('lang_next') : 'lang_next');
        }
    }

    /**
     * formatea un booleano a texto
     * @param boolean $boolean
     * @return string si si el booleano es true, no si el booleano es false
     */
    public function formatBoolean($boolean) {
        return $boolean ? $this->lang('text_yes') : $this->lang('text_no');
    }

    /**
     * Formatea un monto de dinero
     * @param long $mount el monto
     * @return string el monto formateado
     */
    public function formatMount($mount) {
        if (isset($mount)) {
            return '$' . number_format($mount, 2, ",", ".");
        } else {
            return '';
        }
    }

    /**
     * Formatea un numero
     * @param long $number el numero
     * @return string el numero formateado
     */
    public function formatNumber($number) {
        if (isset($number)) {
            return number_format($number, 0, ",", ".");
        } else {
            return '';
        }
    }

    /**
     * Formatea un porcentaje
     * @param float $percentage el porcentaje
     * @return string el porcentaje formateado
     */
    public function formatPercentage($percentage) {
        if (isset($percentage) && $percentage > 0) {
            return number_format($percentage, 2, ",", ".") . ' %';
        } else {
            return '';
        }
    }

    /**
     * Formatea una fecha
     * @param long $date la fecha en timestamp
     * @return string la fecha formateada
     */
    public function formatDate($date) {
        if (isset($date) && $date != "") {
            return date('d/m/Y', $date);
        } else {
            return '';
        }
    }

    /**
     * Formatea una fecha
     * @param long $date la fecha en timestamp
     * @return string la fecha formateada
     */
    public function formatWiredDate($date) {
        if (isset($date) && $date != "") {
            return date('Y-m-d', $date);
        } else {
            return '';
        }
    }

    function blowfishCrypt($password, $cost) {
        $chars = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $salt = sprintf('$2a$%02d$', $cost);
        for ($i = 0; $i < 22; $i++)
            $salt.=$chars[rand(0, 63)];
        return crypt($password, $salt);
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {

            return ($miles * 1.609344);
        } else if ($unit == "N") {

            return ($miles * 0.8684);
        } else {

            return $miles;
        }
    }

    public function lang($string, $langstr = null) {

        if ($langstr && !array_key_exists($langstr, Lang::$_ENUM)) {
            $langstr = Lang::$_DEFAULT;
        }

        if ($langstr && $langstr != $this->config("lang")) {
            $lang = null;

            if (@require("lang/lang.$langstr.php")) {
                if (array_key_exists($string, $lang)) {
                    return $lang[$string];
                } else {
                    if (array_key_exists($string, $this->lang)) {
                        return $this->lang[$string];
                    } else {
                        return $string;
                    }
                }
            }
        }

        if (array_key_exists($string, $this->lang)) {
            return $this->lang[$string];
        } else {
            return $string;
        }
    }

    public function config($string) {
        if (array_key_exists($string, $this->config)) {
            return $this->config[$string];
        } else {
            return $string;
        }
    }

    public function photoUrl($baseUrl) {
        if (!isset($baseUrl)) {
            return null;
        }
        if (strstr($baseUrl, "http") === false) {
            return $this->config("storage_folder") . $baseUrl;
        } else {
            return $baseUrl;
        }
    }

    public function normalize($string) {
        return ucwords(strtolower(trim($string)));
    }

    public function item($array, $key, $default = null, $langstr = null) {
        if (array_key_exists($key, $array)) {
            $string = $array[$key];
            if (is_string($string) && stripos($string, "lang_") !== false) {
                return $this->lang($string, $langstr);
            } else if (is_string($string) && strpos($string, "db_") !== false) {
                return $this->lang($string, $langstr);
            } else {
                return $string;
            }
        }
        return $default;
    }

}

?>
