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

    public $user;
    //Datos de Configuracion
    public $config;
    //language data
    public $lang;
    //data acces of the controlling $application
    public $data;
    //name of the controlling $application
    public $name;
    //template
    protected $tpl;
    //navigation
    public $navigation;

    /**
     * Construye una nueva aplicacion
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
        $link = $this->config['web_secure_url'] . "?" . $this->config['param_app'] . "=" . $app;
        $link .= "&" . $this->config['param_navigation'] . "=" . $navigation;
        if ($params) {
            foreach ($params as $param) {
                $link .= "&" . $param->name . "=" . $param->value;
            }
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
    public function getAppUrl($app, $navigation, $params = null) {
        $link = $this->config['web_url'] . "?" . $this->config['param_app'] . "=" . $app;
        $link .= "&" . $this->config['param_navigation'] . "=" . $navigation;
        if ($params) {
            foreach ($params as $param) {
                $link .= "&" . $param->name . "=" . $param->value;
            }
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
                $this->error($this->lang['error_notvalid'] . " : " . (array_key_exists($name, $this->lang) ? $this->lang[$name] : $name));
            }
            return $value;
        }
        if ($force) {
            $this->error($this->lang['error_notvalid'] . " : " . (array_key_exists($name, $this->lang) ? $this->lang[$name] : $name));
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
                $this->error($this->lang['error_notvalid'] . " : " . (array_key_exists($name, $this->lang) ? $this->lang[$name] : $name));
            }
            return $value;
        }
        if ($force) {
            $this->error($this->lang['error_notvalid'] . " : " . (array_key_exists($name, $this->lang) ? $this->lang[$name] : $name));
        }
        return null;
    }

    public function getJsonParam($force = false) {
        $value = file_get_contents("php://input");
        $json = json_decode($value);
        if (isset($json)) {
            $json = get_object_vars($json);
            return $json;
        }
        if ($force) {
            $this->error($this->lang['error_jsonnotvalid']);
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
            return $this->filter($value, $type);
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
                            return $value;
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
                    if ($value !== "") {
                        if (!get_magic_quotes_gpc()) {
                            if (isset($this->data)) {
                                $value = $this->data->escapeChars($value);
                            } else {
                                $value = addslashes($value);
                            }
                        }
                        return substr($value, 0, 1024);
                    }
                    break;
                }
            case "string1024" : {
                    if ($value !== "") {
                        if (!get_magic_quotes_gpc()) {
                            if (isset($this->data)) {
                                $value = $this->data->escapeChars($value);
                            } else {
                                $value = addslashes($value);
                            }
                        }
                        return (substr($value, 0, 1024));
                    }
                    break;
                }
            case "string64" : {
                    if ($value !== "") {
                        if (!get_magic_quotes_gpc()) {
                            if (isset($this->data)) {
                                $value = $this->data->escapeChars($value);
                            } else {
                                $value = addslashes($value);
                            }
                        }
                        return (substr($value, 0, 64));
                    }
                    break;
                }
            case "string32" : {
                    if ($value !== "") {
                        if (!get_magic_quotes_gpc()) {
                            if (isset($this->data)) {
                                $value = $this->data->escapeChars($value);
                            } else {
                                $value = addslashes($value);
                            }
                        }
                        return (substr($value, 0, 32));
                    }
                    break;
                }
            case "string16" : {
                    if ($value !== "") {
                        if (!get_magic_quotes_gpc()) {
                            if (isset($this->data)) {
                                $value = $this->data->escapeChars($value);
                            } else {
                                $value = addslashes($value);
                            }
                        }
                        return (substr($value, 0, 16));
                    }
                    break;
                }
            case "string1024excel" : {
                    $value = strval($value);
                    if ($value !== "") {
                        if (!$this->is_utf8($value)) {
                            $value = utf8_encode($value);
                        }
                        if (!get_magic_quotes_gpc()) {
                            if (isset($this->data)) {
                                $value = $this->data->escapeChars($value);
                            } else {
                                $value = addslashes($value);
                            }
                        }
                        return (substr($value, 0, 1024));
                    }
                    break;
                }
            case "string64excel" : {
                    $value = strval($value);
                    if ($value !== "") {
                        if (!$this->is_utf8($value)) {
                            $value = utf8_encode($value);
                        }
                        if (!get_magic_quotes_gpc()) {
                            if (isset($this->data)) {
                                $value = $this->data->escapeChars($value);
                            } else {
                                $value = addslashes($value);
                            }
                        }
                        return (substr($value, 0, 64));
                    }
                    break;
                }
            case "string32excel" : {
                    $value = strval($value);
                    if ($value !== "") {
                        if (!$this->is_utf8($value)) {
                            $value = utf8_encode($value);
                        }
                        if (!get_magic_quotes_gpc()) {
                            if (isset($this->data)) {
                                $value = $this->data->escapeChars($value);
                            } else {
                                $value = addslashes($value);
                            }
                        }
                        return (substr($value, 0, 32));
                    }
                    break;
                }
            case "string16excel" : {
                    $value = strval($value);
                    if ($value !== "") {
                        if (!$this->is_utf8($value)) {
                            $value = utf8_encode($value);
                        }
                        if (!get_magic_quotes_gpc()) {
                            if (isset($this->data)) {
                                $value = $this->data->escapeChars($value);
                            } else {
                                $value = addslashes($value);
                            }
                        }
                        return (substr($value, 0, 16));
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
                                return strtotime("$numbers[1]/$numbers[0]/$numbers[2]");
                            }
                        }
                        if (preg_match("/^[0-9\/]*$/", $value)) {
                            $numbers = explode("/", $value);
                            if (count($numbers) == 3) {
                                return strtotime("$numbers[1]/$numbers[0]/$numbers[2]");
                            }
                        }
                    }
                    break;
                }
            case "boolean" : {
                    if ($value !== "") {
                        $vals = array("0", "1");
                        $trimed = str_replace($vals, "", $value);
                        if (strlen($value) == 1 && empty($trimed)) {
                            return $value;
                        }
                    }
                    break;
                }
            case "int-array" : {
                    $correct = true;
                    foreach ($value as $element) {
                        if ($element === "") {
                            $correct = false;
                            break;
                        }
                        $vals = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "+");
                        $trimed = str_replace($vals, "", $element);
                        if ((!is_numeric($element) || !empty($trimed))) {
                            $correct = false;
                            break;
                        }
                    }
                    if ($correct) {
                        return $value;
                    }
                    break;
                }
            case "double-array" : {
                    $correct = true;
                    foreach ($value as $key => $element) {
                        if ($element === "") {
                            $correct = false;
                            break;
                        }
                        $trimed = str_replace(array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "+", ",", ".", "$", "(", ")", "%"), "", $element);
                        if (!empty($trimed)) {
                            $correct = false;
                            break;
                        }
                        $element = str_replace(array("$", "(", ")", "%"), "", $element);
                        $value[$key] = str_replace(array(","), ".", $element);
                    }
                    if ($correct) {
                        return $value;
                    }
                    break;
                }
            case "string-array" : {
                    foreach ($value as $element) {
                        if ($element === "") {
                            $correct = false;
                            break;
                        }
                        if (!get_magic_quotes_gpc()) {
                            if (isset($this->data)) {
                                $element = $this->data->escapeChars($value);
                            } else {
                                $element = addslashes($value);
                            }
                        }
                    }
                    return $value;
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
        $login = $this->getSessionParam("login");
        $user = $this->getSessionParam("user");
        $rememberme = $this->getSessionParam("rememberme");
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
        if (!$rememberme && $this->config['web_time_out'] > 0) {
            $time = $this->getSessionParam("time");
            if (!isset($time)) {
                return false;
            }
            if ($time + $this->config['web_time_out'] < time()) {
                return false;
            }
            $this->setSessionParam("time", time());
        }

        if ($user !== false) {
            if ($this->data != null) {
                $data = new SystemUserData($this->data);
                $dataBaseUser = $data->getSystemUser($user);
                if ($user->getToken() === $dataBaseUser->getToken()) {
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
        $this->setSessionParam("login", true);
        $this->setSessionParam("user", $user);
        $this->setSessionParam("time", time());
        $this->setSessionParam("rememberme", $rememberme);
    }

    /**
     * Elimina los datos de session del login
     * @return Nada
     */
    public function loginUnset() {
        $this->killSessionParam("login");
        $this->killSessionParam("user");
        $this->killSessionParam("time");
        $this->killSessionParam("rememberme");

        session_destroy();
    }

    /**
     * Arroja un mensaje de error y detiene la ejecucion
     * @param string $message <p>El mensaje de error a mostrar</p>
     * @param string $link <p>Una URL para incluir en el link</p>
     * @param string $linkText <p>El texto del link</p>
     * @return Nada
     */
    public function error($message, $link="", $linkText="") {
        $this->name = "error";
        $this->navigation = "error";
        $this->setMainTemplate("message", "error");
        if (is_array($message)) {
            foreach ($message as $text) {
                $this->setHTMLVariableTemplate('MESSAGE-TEXT', $text);
                $this->parseTemplate('MESSAGES');
            }
        } else {
            $this->setHTMLVariableTemplate('MESSAGE-TEXT', $message);
            $this->parseTemplate('MESSAGES');
        }
        $this->setHTMLVariableTemplate("MESSAGE-LINK", $link);
        $this->setHTMLVariableTemplate("MESSAGE-LINK-TEXT", $linkText);
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
            if ($this->tpl->loadTemplateFile($this->config['folder_template'] . "$this->name/template." . $priority) === SIGMA_OK) {
                $this->tpl->addBlockfile("TEMPLATE-CONTENT", $this->name, $this->config['folder_navigation'] . "$navigationFolder/$navigationFile." . $priority);
            }
        }
        else if ($this->tpl->loadTemplateFile($this->config['folder_template'] . "$this->name/template.html") === SIGMA_OK) {
            $this->tpl->addBlockfile("TEMPLATE-CONTENT", $this->name, $this->config['folder_navigation'] . "$navigationFolder/$navigationFile.html");
            $this->setLangItems($this->name);
            $this->tpl->addBlockfile("TEMPLATE-JAVASCRIPT", $this->name . "_javascript", $this->config['folder_navigation'] . "$navigationFolder/$navigationFile.js");
            $this->setLangItems($this->name . "_javascript");
            $this->tpl->addBlockfile("TEMPLATE-CSS", $this->name . "_css", $this->config['folder_navigation'] . "$navigationFolder/$navigationFile.css");
        } else if ($this->tpl->loadTemplateFile($this->config['folder_template'] . "$this->name/template.xml") === SIGMA_OK) {
            $this->tpl->addBlockfile("TEMPLATE-CONTENT", $this->name, $this->config['folder_navigation'] . "$navigationFolder/$navigationFile.xml");
        } else if ($this->tpl->loadTemplateFile($this->config['folder_template'] . "$this->name/template.json") === SIGMA_OK) {
            $this->tpl->addBlockfile("TEMPLATE-CONTENT", $this->name, $this->config['folder_navigation'] . "$navigationFolder/$navigationFile.json");
        }

        $this->setHTMLVariableTemplate("TEMPLATE-TITLE", $title);
    }

    /**
     * Llena los elementos de idioma de los templates cargados
     * @param string $navigationFolder <p>La carpeta de la vista</p>
     * @param string $navigationFile <p>El nombre del archivo de vista (sin la extension)</p>
     * @return Nada
     */
    private function setLangItems($blockName) {
        $placeholders = $this->tpl->getPlaceholderList();

        foreach ($placeholders as $placeholder) {
            if (strpos($placeholder, "LANG-") !== false) {
                $var = substr($placeholder, 5);
                $name = strtolower($var);
                $this->setVariableTemplate("LANG-$var", $this->lang["lang_$name"]);
            }
        }

        $placeholders = $this->tpl->getPlaceholderList($blockName);

        foreach ($placeholders as $placeholder) {
            if (is_string($placeholder) && strpos($placeholder, "LANG-") !== false) {
                $var = substr($placeholder, 5);
                $name = strtolower($var);
                $this->setVariableTemplate("LANG-$var", $this->lang["lang_$name"]);
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
    public function setFormTemplate($name, array $params, $application, $navigation, $secure = false) {
        $name = strtoupper($name);
        $this->setVariableTemplate("$name-ID", $this->navigation . "$name");
        if ($secure) {
            $this->setVariableTemplate("$name-ACTION", $this->getSecureAppUrl($application, $navigation));
        } else {
            $this->setVariableTemplate("$name-ACTION", $this->getAppUrl($application, $navigation));
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
        $objectFormName = strtoupper($objectName);
        foreach ($properties as $property) {
            $obj = strpos($property, "_object");
            $arr = strpos($property, "_array");
            if (!$arr) {
                if ($obj > 0) {
                    $fieldname = substr($property, 0, $obj);
                    $paramName = strtoupper($fieldname);
                    $cammelName = strtoupper(substr($fieldname, 0, 1)) . substr($fieldname, 1);
                    $getter = "get$cammelName";
                    $value = $object->$getter();
                    $this->setVariableTemplate("$formName-PARAM-$objectFormName-$paramName", "$objectName" . "_" . "$fieldname");
                    $this->setVariableTemplate("$formName-DATA-$objectFormName-$paramName", $this->utf8tohtml(strval($value->getId()), true));
                } else {
                    $fieldname = $property;
                    $paramName = strtoupper($fieldname);
                    $cammelName = strtoupper(substr($fieldname, 0, 1)) . substr($fieldname, 1);
                    $getter = "get$cammelName";
                    $value = $object->$getter();
                    $this->setVariableTemplate("$formName-PARAM-$objectFormName-$paramName", "$objectName" . "_" . "$fieldname");
                    $this->setVariableTemplate("$formName-DATA-$objectFormName-$paramName", $this->utf8tohtml(strval($value), true));
                }
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
        $this->setVariableTemplate("$formName-PARAM-" . $viewParam, $param->name);
        $this->setVariableTemplate("$formName-DATA-" . $viewParam, $this->utf8tohtml(strval($param->value), true));
    }

    /**
     * Parsea un Tag y le asigna el valor correspondiente
     * @param string $name <p>El nombre del TAG</p>
     * @param string $value <p>El valor a asignar</p>
     * @return Nada
     */
    public function setVariableTemplate($name, $value) {
        if (isset($this->data)) {
            $value = $this->data->unEscapeChars($value);
        }
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
        if (isset($this->data)) {
            $value = $this->data->unEscapeChars($value);
        }
        $var = $this->utf8tohtml($value, true);
        $var = str_replace("\r\n", "<br />", $var);
        $var = str_replace("\n", "<br />", $var);
        $this->tpl->setVariable($name, $var);
    }

    /**
     * Parsea un Tag y le asigna el valor correspondiente en formato HTML
     * @param string $name <p>El nombre del TAG</p>
     * @param string $value <p>El valor a asignar</p>
     * @return Nada
     */
    public function setJsonVariableTemplate($name, $value) {
        $value = strval($value);
        if (isset($this->data)) {
            $value = $this->data->unEscapeChars($value);
        }
        $var = str_replace("\r\n", " ", $value);
        $var = str_replace("\n", " ", $var);
        $this->tpl->setVariable($name, $var);
    }

    /**
     * Parsea Tags de un bloque BEGIN END
     * @param array $array <p>El array con los tags como llaves y su valor correspondiente </p>
     * @return Nada
     */
    public function setArrayTemplate($array) {
        foreach ($array as &$value) {
            if (isset($this->data)) {
                $value = $this->data->unEscapeChars($value);
            }
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
            if (isset($this->data)) {
                $value = $this->data->unEscapeChars($value);
            }
            $value = $this->utf8tohtml($value, true);
            $value = str_replace("\r\n", "<br />", $value);
            $value = str_replace("\n", "<br />", $value);
        }
        $this->tpl->setVariable($array);
    }

    /**
     * Parsea Tags de un bloque BEGIN END en formato JSON
     * @param array $array <p>El array con los tags como llaves y su valor correspondiente </p>
     * @return Nada
     */
    public function setJsonArrayTemplate($array) {
        foreach ($array as &$value) {
            $value = strval($value);
            if (isset($this->data)) {
                $value = $this->data->unEscapeChars($value);
            }
            $value = str_replace("\r\n", " ", $value);
            $value = str_replace("\n", " ", $value);
        }
        $this->tpl->setVariable($array);
    }

    /**
     * Parsea un bloque BEGIN END
     * @param string $name <p>El nombre del bloque a parsear</p>
     * @return Nada
     */
    public function parseTemplate($name) {
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
        $this->tpl->touchBlock($this->name);
        $this->tpl->touchBlock($this->name . "_onload");
        $this->tpl->touchBlock($this->name . "_javascript");
        $this->tpl->touchBlock($this->name . "_css");
        $this->tpl->touchBlock($this->navigation . "_onload");
        $this->tpl->touchBlock($this->navigation . "_javascript");
        $this->tpl->show();
        $this->log();
        $this->endApp();
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
        $this->log();
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
        $this->log();
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
        $pdf = new TCPDF($this->config['pdf_page_orientation'], $this->config['pdf_unite'], $this->config['pdf_page_format'], true, 'UTF-8', false);
        $pdf->SetCreator($this->config['pdf_creator']);
        $pdf->SetAuthor($this->config['pdf_author']);
        $pdf->SetHeaderData(/* logo */ '', /* ancho logo */ '', /* título */ $this->lang['text_header'], /* $this->config['pdf_header_string'] */ '');
        $pdf->setHeaderFont(Array($this->config['pdf_font_name_main'], '', $this->config['pdf_font_size_main']));
        $pdf->setFooterFont(Array($this->config['pdf_font_name_data'], '', $this->config['pdf_font_size_data']));
        $pdf->SetDefaultMonospacedFont($this->config['pdf_font_monospace']);
        $pdf->SetMargins($this->config['pdf_margin_left'], $this->config['pdf_margin_top'], $this->config['pdf_margin_right']);
        $pdf->SetHeaderMargin($this->config['pdf_margin_header']);
        $pdf->SetFooterMargin($this->config['pdf_margin_footer']);
        $pdf->SetAutoPageBreak(TRUE, $this->config['pdf_margin_bottom']);
        $pdf->setImageScale($this->config['pdf_image_scale_ratio']);
        $pdf->setFontSubsetting(true);
        $pdf->SetFont($this->config['pdf_font_name_data'], '', $this->config['pdf_font_size_data'], '', true);
        $pdf->AddPage();
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output($this->lang['pdf_filename'], 'I');
        $this->log();
        $this->endApp();
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
    public function redirect($app, $navigation) {
        $this->log();
        //Try redirect
        header(sprintf("Location: %s", $this->getAppUrl($app, $navigation)));
        $this->endApp();
    }

    /**
     * Redirije a una URL
     * @param string $url <p>La URL a la cual redirigir</p>
     * @return Nada
     */
    public function redirectToUrl($url) {
        $this->log();
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
    public function secureRedirect($app, $navigation) {
        $this->log();
        //Try redirect
        header(sprintf("Location: %s", $this->getSecureAppUrl($app, $navigation)));
        $this->endApp();
    }

    /**
     * Crea una entrada de log en el archivo de log de la aplicacion
     * @param array $elements <p>Un array de pares (llave, valor) para agregar en el log en formatio XML</p>
     * @return Nada
     */
    protected function log($elements = array()) {
        //adds user information to the log
        if (isset($this->user) && $this->user !== false) {
            $elements["user"] = $this->user->getUsername();
        }

        $doc = new DOMDocument();
        $logentry = $doc->createElement("entry");
        foreach ($elements as $key => $value) {
            $element = $doc->createElement($key, $value);
            $logentry->appendChild($element);
        }
        $element = $doc->createElement("application", $this->name);
        $logentry->appendChild($element);
        $element = $doc->createElement("navigation", $this->navigation);
        $logentry->appendChild($element);
        $element = $doc->createElement("time", date('d-m-Y G:i:s'));
        $logentry->appendChild($element);
        $element = $doc->createElement("ip", isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "localdaemon");
        $logentry->appendChild($element);
        $element = $doc->createElement("host", $_SERVER['SERVER_NAME']);
        $logentry->appendChild($element);
        $element = $doc->createElement("file", $_SERVER['PHP_SELF']);
        $logentry->appendChild($element);
        $doc->appendChild($logentry);
        $log = $doc->saveXML($logentry) . "\n";
        $data = new LogData($this->config['server_logg_file']);
        $data->insertLog($log);
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
            $fileName = $this->getUrlParam($this->config['param_file_name'], "string");
            if (!isset($fileName)) {
                $this->error($this->lang['error_filenotfound']);
            }
            $filepath = $this->config['server_down_folder'] . $fileName;
        }
        if (!file_exists($filepath)) {
            $this->error($this->lang['error_filenotfound']);
        }
        $fsize = filesize($filepath);
        if ($this->config['system'] == "windows") {
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Content-Type: application/force-download");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Transfer-Encoding: binary");
            ob_clean();
            flush();
            readfile($filepath);
        } elseif ($this->config['system'] == "linux") {
//            header("Content-Disposition: attachment; filename=$fileName");
//            header("Content-Location: $filepath");
//            header("Content-Type: application/force-download");
//            header("Content-Type: application/octet-stream");
//            header("Content-Type: application/download");
//            header("Content-Length: $fsize");
//            header("Expires: 0");
//            $fp = fopen("$filepath", "r");
//            while (!feof($fp)) {
//                echo fread($fp, 1024 * 1024);
//                flush();
//            }
//            fclose($fp);
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Content-Type: application/force-download");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Transfer-Encoding: binary");
            ob_clean();
            flush();
            readfile($filepath);
        }
        $this->log();
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
    public function upload($fileParam, $destFolder, $override, $destname, $extensions_list = null) {
        if (!isset($_FILES[$fileParam])) {
            $this->error($this->lang['error_filesize']);
        }
        if ($_FILES[$fileParam]['error'] == UPLOAD_ERR_INI_SIZE) {
            $this->error($this->lang['error_filesize']);
        }
        if (!is_uploaded_file($_FILES[$fileParam]['tmp_name'])) {
            $this->error($this->lang['error_filenotuploaded']);
        }
//        if ($extensions_list  null) {
//            $extensions = explode(",", $extensions_list);
//            $format_ok = false;
//            foreach ($extensions as $ext) {
//                if ($ext === $_FILES[$fileParam]['type']) {
//                    $format_ok = true;
//                }
//            }
//            if (!$format_ok) {
//                $this->error($this->lang['error_fileformatnotvalid'] . " " . implode(", ", $extensions) . " - " . $_FILES[$fileParam]['type']);
//            }
//        }
        if (!$override && file_exists($destFolder . $destname)) {
            $this->error($this->lang['error_fileexist']);
        }
        if (!move_uploaded_file($_FILES[$fileParam]['tmp_name'], $destFolder . $destname)) {
            $this->error($this->lang['error_filenotmoved']);
        }
    }

    /**
     * Crea un string aleatoreo
     * @param int $lenght <p>El largo del string a crear</p>
     * @return una nuevo string aleatorio del largo indicado
     */
    public function createRandomString($lenght) {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
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
     * Obtiene el numero total de paginas a mostrar en una navegacion con paginamiento
     * @param int $totalItems <p>El numero total de items a mostrar en una pagina</p>
     * @return el numero total de paginas
     */
    public function getTotalPages($totalItems) {
        $totalPages = ceil($totalItems / $this->config['web_page_items']);
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
        $page = $this->getUrlParam($this->config['param_page'], "int", false);
        if (!isset($page)) {
            $page = 0;
        }
        if ($page < 0) {
            $this->error($this->lang['error_pagnotfound']);
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
    public function setPageNavigation($currentPage, $totalPages, $app, $nav) {
        $this->setHTMLVariableTemplate("PAGE-NUMBER", $currentPage + 1);
        $this->setHTMLVariableTemplate("PAGE-TOTAL", $totalPages);
        $linkback = $this->getSecureAppUrl($app, $nav, array(new Param($this->config['param_page'], $currentPage - 1)));
        $linkfordware = $this->getSecureAppUrl($app, $nav, array(new Param($this->config['param_page'], $currentPage + 1)));
        if ($currentPage + 1 > 1) {
            $this->setHTMLVariableTemplate("PAGE-LINK-BACK", $linkback);
            $this->setHTMLVariableTemplate("PAGE-TEXT-BACK", $this->lang['word_back']);
        }
        if ($currentPage + 1 < $totalPages) {
            $this->setHTMLVariableTemplate("PAGE-LINK-NEXT", $linkfordware);
            $this->setHTMLVariableTemplate("PAGE-TEXT-NEXT", $this->lang['word_next']);
        }
    }

    /**
     * formatea un booleano a texto
     * @param boolean $boolean
     * @return string si si el booleano es true, no si el booleano es false
     */
    public function formatBoolean($boolean) {
        return $boolean ? $this->lang['text_yes'] : $this->lang['text_no'];
    }

    /**
     * Formatea un monto de dinero
     * @param long $mount el monto
     * @return string el monto formateado
     */
    public function formatMount($mount) {
        if (isset($mount)) {
            return '$' . number_format($mount, 0, ",", ".");
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
        if (isset($percentage)) {
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
        if (isset($date)) {
            return date('d/m/Y', $date);
        } else {
            return '';
        }
    }

}

?>
