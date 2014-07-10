<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class Application {

    //configuration data
    public $config;
    //languaje data
    public $lang;
    //data acces
    public $data;
    //name
    public $name;
    //HTML template manager
    public $tpl;
    //navigation
    public $navigation;

    /**
     * generates a new application with HTLM parser
     * @param string $config <p>configuration params</p>
     * @param string $lang <p>languaje params</p>
     * @param string $data <p>data conection</p>
     * @param string $name <p>name of the application</p>
     * @return Application a new application
     */
    function __construct($config, $lang, Data $data = null, $name = null) {
        $this->config = $config;
        $this->lang = $lang;
        $this->name = $name;
        $this->data = $data;
        $this->tpl = new HTML_Template_Sigma();
    }

    /**
     * Executes the application
     * @return void
     */
    public function execute() {
        session_start();
        if ($this->config('maintenance')) {
            $this->error($this->lang('lang_maintenance'));
        }
    }

    /**
     * Builds a secure app URL
     * @param string $app <p>The application to send</p>
     * @param string $navigation <p>The application to send</p>
     * @param string $params <p>extra Param objects for the URL</p>
     * @return String the corresponding URL
     */
    public function getAppUrl($app, $navigation, $params = null) {
        if ($this->config('urlforms')) {
            return $this->getAppFlatUrl($this->config('urlbase'), $app, $navigation, $params);
        } else {
            $link = $this->config('web_secure_url') . "?" . $this->config('param_app') . "=" . $app;
            $link .= "&" . $this->config('param_navigation') . "=" . $navigation;
            $hasLang = false;
            if ($params) {
                foreach ($params as $param) {
                    $link .= "&" . $param->name . "=" . $param->value;
                    if ($param->name == $this->config('param_lang')) {
                        $hasLang = true;
                    }
                }
            }
            if (!$hasLang) {
                $link .= "&" . $this->config('param_lang') . "=" . $this->config('lang');
            }
            return $link;
        }
    }

    public function getAppFlatUrl($urlbase, $app, $nav, $params) {
        $id = "";
        $lang = false;
        $linkParams = array();
        if ($params) {
            foreach ($params as $param) {
                if ($param->name == $this->config('param_id')) {
                    $id = $param->value;
                } else {
                    if ($param->name == $this->config('param_lang')) {
                        $lang = true;
                    }
                    $linkParams [] = "$param->name=$param->value";
                }
            }
        }
        if (!$lang) {
            $linkParams [] = $this->config('param_lang') . "=" . $this->config('lang');
        }
        if ($app && $nav) {
            return $urlbase . "$app/$nav/$id?" . implode("&", $linkParams);
        } else if ($nav) {
            return $urlbase . "$nav/$id?" . implode("&", $linkParams);
        } else {
            return $urlbase . "$id?" . implode("&", $linkParams);
        }
    }

    public function getHomeUrl() {
        $linkParams = array();
        if ($this->config('lang') != LangFactory::getDefaultLang()) {
            $linkParams [] = $this->config('param_lang') . "=" . $this->config('lang');
        }
        if ($this->config('urlforms')) {
            if ($linkParams) {
                return $this->config('urlbase') . "?" . implode("&", $linkParams);
            }
            return $this->config('urlbase');
        } else {
            if ($linkParams) {
                return $this->config('web_secure_url') . "?" . implode("&", $linkParams);
            }
            return $this->config('web_secure_url');
        }
    }

    /**
     * Gets a variable from the GET array, filtered by type, escapes for prevent SQL injection
     * @param string $name <p>The name of the variable</p>
     * @param string $type <p>The type of the property</p>
     * @param string $force <p>If is forced trows an error on null value</p>
     * @return the value of the param, null if does not exist or does not fir the type
     */
    public function getUrlParam($name, $type, $force = true) {
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
     * Gets a variable from the POST array, filtered by type, escapes for prevent SQL injection
     * @param string $name <p>The name of the variable</p>
     * @param string $type <p>The type of the property</p>
     * @param string $force <p>If is forced trows an error on null value</p>
     * @return the value of the param, null if does not exist or does not fir the type
     */
    public function getFormParam($name, $type, $force = true) {
        $value = null;
        if (isset($_POST[$name])) {
            $value = $_POST[$name];
            $value = $this->filter($value, $type);
        }
        if ($type == PropertyTypes::$_DATE && !isset($value)) {
            $year = $this->getFormParam("$name-year", PropertyTypes::$_INT, false);
            $month = $this->getFormParam("$name-month", PropertyTypes::$_INT, false);
            $day = $this->getFormParam("$name-day", PropertyTypes::$_INT, false);
            if (isset($year) && $month && $day) {
                $value = "$year-$month-$day";
                $value = $this->filter($value, PropertyTypes::$_DATE);
            }
        }
        if ($type == PropertyTypes::$_TIME && !isset($value)) {
            $hour = $this->getFormParam("$name-hour", PropertyTypes::$_INT, false);
            $minutes = $this->getFormParam("$name-minutes", PropertyTypes::$_INT, false);
            if (isset($hour) && isset($minutes)) {
                $value = "$hour:$minutes";
                $value = $this->filter($value, PropertyTypes::$_TIME);
            }
        }
        if ($type == PropertyTypes::$_FILE && !isset($value)) {
            $value = $this->upload($name, true);
        }
        if ($type == PropertyTypes::$_IMAGE256 && !isset($value)) {
            $value = $this->uploadPhoto($name, true, 256, 256);
        }
        if ($force && !isset($value)) {
            $this->error($this->lang('lang_notvalid') . " : " . (array_key_exists($name, $this->lang) ? $this->lang($name) : $name));
        }
        return $value;
    }

    /**
     * Gets a an object from the POST array 
     * @param string $object <p>An empty object to fill</p>
     * @param string $force <p>If is forced trows an error on not completed object</p>
     * @return the filled object, empty if it does not exist
     */
    public function getFormObject(DataObject $object, $force = true) {
        $objectName = get_class($object);
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        foreach ($properties as $property) {
            $fieldname = $property["name"];
            $cammelName = strtoupper(substr($fieldname, 0, 1)) . substr($fieldname, 1);
            $setter = "set$cammelName";
            $object->$setter($this->getFormParam("$objectName" . "_" . "$fieldname", $property["type"], false));
        }
        if ($force && !$object->__isComplete()) {
            $this->error($this->lang('lang_notcomplete'));
        }
        return $object;
    }

    /**
     * Gets a a json object from the http body
     * @param string $force <p>If is forced trows an error on non json object</p>
     * @return the filled json object, null if it does not exist
     */
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
     * Gets a session param
     * @param string $name <p>the name of the variable</p>
     * @return the variable value, null if it does not exist
     */
    public function getSessionParam($name) {
        if (isset($_SESSION[$name])) {
            $value = $_SESSION[$name];
            return $value;
        }
        return null;
    }

    /**
     * Delets a variable from session
     * @param string $name <p>The name of the variable</p>
     * @return void
     */
    public function killSessionParam($name) {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * Sets a variable into session
     * @param string $name <p>the name of the variable</p>
     * @param string $value <p>the value of the variable</p>
     * @return void
     */
    public function setSessionParam($name, $value) {
        $_SESSION[$name] = $value;
    }

    /** Filters a variable value by a corresponding PropertyType
     * @param object $value <p>The variable to check</p>
     * @param string $type <p>The type of the variable. A Possible value of ProertyType</p>
     * @return The filtered value of the variable or null if the filter did not passed the validation
     */
    public function filter($value, $type) {
        switch ($type) {
            case PropertyTypes::$_INT :
            case PropertyTypes::$_LIST :
            case PropertyTypes::$_SHORTLIST :
            case PropertyTypes::$_LONG : {
                    if ($value !== "" && $value >= -1) {
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
                            return intval($value);
                        }
                    }
                    break;
                }
            case PropertyTypes::$_DOUBLE : {
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
            case PropertyTypes::$_URL : {
                    if (filter_var($value, FILTER_VALIDATE_URL)) {
                        return $value;
                    }
                    break;
                }
            case PropertyTypes::$_EMAIL : {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        return $value;
                    }
                    break;
                }
            case PropertyTypes::$_RUT : {
                    $rut = $this->valida_rut($value);
                    if ($rut) {
                        return $rut;
                    }
                    break;
                }
            case PropertyTypes::$_PASSWORD: {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 2048));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING : {
                    if ($value) {
                        $value = trim($value);
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING1 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 1));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING2 :
            case PropertyTypes::$_STRINGLIST : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 2));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING4 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 4));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING8 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 8));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING16 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 16));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING24 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 24));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING32 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 32));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING64 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 64));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING128 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 128));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING256 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 256));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING512 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 512));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING1024 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 1024));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_STRING2048 : {
                    if ($value) {
                        $value = trim($value);
                        $value = (substr($value, 0, 2048));
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_FILE : {
                    if ($value) {
                        $value = trim($value);
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_IMAGE256 : {
                    if ($value) {
                        $value = trim($value);
                        if ($value) {
                            return $value;
                        }
                    }
                    break;
                }
            case PropertyTypes::$_FLAT : {
                    return ($value);
                }
            case PropertyTypes::$_LETTERS : {
                    if ($value !== "") {
                        if (preg_match("/^[a-zA-Z]*$/", $value)) {
                            return ($value);
                        }
                    }
                    break;
                }
            case PropertyTypes::$_ALPHANUMERIC : {
                    if ($value !== "") {
                        if (preg_match("/^[a-zA-Z0-9]*$/", $value)) {
                            return ($value);
                        }
                    }
                    break;
                }
            case PropertyTypes::$_DATE : {
                    if ($value !== "") {
                        if (preg_match("/^[0-9\-]*$/", $value)) {
                            $numbers = explode("-", $value);
                            if (count($numbers) == 3) {
                                $time = strtotime("$numbers[1]/$numbers[2]/$numbers[0]");
                                if ($time) {
                                    return $time;
                                }
                            }
                        }
                        if (preg_match("/^[0-9\/]*$/", $value)) {
                            $numbers = explode("/", $value);
                            if (count($numbers) == 3) {
                                $time = strtotime("$numbers[1]/$numbers[2]/$numbers[0]");
                                if ($time) {
                                    return $time;
                                }
                            }
                        }
                    }
                    break;
                }
            case PropertyTypes::$_TIME : {
                    if ($value !== "") {
                        $numbers = explode(":", $value);
                        if (count($numbers) == 2) {
                            $time = strtotime("$numbers[0]:$numbers[1]:00");
                            if ($time) {
                                return $time;
                            }
                        }
                    }
                    break;
                }
            case PropertyTypes::$_BOOLEAN : {
                    if (strval($value) === "1") {
                        return ObjectBoolean::$_YES;
                    } else if (strval($value) === "0") {
                        return ObjectBoolean::$_NO;
                    }
                    break;
                }
            case PropertyTypes::$_INTARRAY : {
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
                }
            case PropertyTypes::$_DOUBLEARRAY : {
                    $newvalue = array();
                    if (!is_array($value)) {
                        $value = explode(",", str_replace(array("(", ")"), "", $value));
                    }
                    foreach ($value as $element) {
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
                }
            case PropertyTypes::$_STRINGARRAY : {
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
                }
            case PropertyTypes::$_JSON : {
                    $object = json_decode(($value));
                    if ($object) {
                        return $object;
                    } else {
                        return null;
                    }
                    break;
                }
            default : {
                    return null;
                }
        }
        return null;
    }

    /**
     * Checks that a string is from the encoding utf8
     * @param string $string <p>the string to check</p>
     * @return boolean true if is an utf8 string, false if not
     */
    protected function is_utf8($string) {
        if (strlen($string) > 5000) {
            for ($i = 0, $s = 5000, $j = ceil(strlen($string) / 5000); $i < $j; $i++, $s+=5000) {
                if (is_utf8(substr($string, $s, 5000))) {
                    return true;
                }
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
     * Transforms a utf8 string into an html compatible string
     * @param string $utf8 <p>the string to transform</p>
     * @param bool $encodeTags <p>true if the characters must be converted to html tags</p>
     * @return the resulting string from the operation
     */
    protected function utf8tohtml($utf8, $encodeTags = true) {
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
     * Shows an error message
     * @param string $message <p>the message to show</p>
     * @return void
     */
    public function error($message) {
        $this->setMainTemplate("bicubic", "error");
        $this->setHTMLVariableTemplate('MESSAGE-TEXT', $this->lang($message));
        $this->render();
    }

    /**
     * Shows a  message
     * @param string $message <p>the message to show</p>
     * @return void
     */
    public function message($message) {
        $this->setMainTemplate("bicubic", "message");
        $this->setHTMLVariableTemplate('MESSAGE-TEXT', $this->lang($message));
        $this->render();
    }

    public function setMainTemplate($navigationFolder, $navigationFile, $title = "") {
        if ($this->tpl->loadTemplateFile($this->config('folder_template') . "$this->name/template.html") === SIGMA_OK) {
            $this->tpl->addBlockfile("TEMPLATE-CONTENT", $this->name, $this->config('folder_navigation') . "$navigationFolder/$navigationFile.html");
            if (!$title) {
                $title = $this->config('web_name');
            }
            $this->setHTMLVariableTemplate("TEMPLATE-LANG", $this->config('lang'));
            $this->setHTMLVariableTemplate("TEMPLATE-TITLE", $title);
            $this->setHTMLVariableTemplate("TEMPLATE-COPY", $this->config('web_copyright'));
            $params = array();
            $app = "";
            $nav = "";
            foreach ($_GET as $key=> $value) {
                if ($key == $this->config('param_lang')) {
                    continue;
                }
                if ($key == $this->config('param_app')) {
                    $app = $value;
                    continue;
                }
                if ($key == $this->config('param_navigation')) {
                    $nav = $value;
                    continue;
                }
                $params [] = new Param($key, $value);
            }
            foreach (Lang::$_ENUM as $langKey=> $langname) {
                $langparams = $params;
                $langparams [] = new Param($this->config('param_lang'), $langKey);
                $this->setHtmlArrayTemplate(array(
                    'LANG-LINK'=>$this->getAppUrl($app, $nav, $langparams),
                    'LANG-TEXT'=>$this->lang($langname),
                ));
                $this->parseTemplate('LANGS');
            }
        }
    }

    public function setCustomTemplate($navigationFolder, $navigationFile) {
        $file = $this->config('folder_navigation') . "$navigationFolder/$navigationFile." . "html";
        $tpl = new HTML_Template_Sigma();
        if ($tpl->loadTemplateFile($file) === SIGMA_OK) {
            return $tpl;
        }
        return null;
    }

    private function setLangItems($blockName) {
        $prefix = "LANG";
        $placeholders = $this->tpl->getPlaceholderList();
        foreach ($placeholders as $placeholder) {
            if (strpos($placeholder, "$prefix-") !== false) {
                $var = substr($placeholder, 5);
                $name = strtolower($var);
                $this->setHtmlVariableTemplate("$prefix-$var", $this->lang("lang_$name"));
            }
        }
        $placeholders = $this->tpl->getPlaceholderList($blockName);
        foreach ($placeholders as $placeholder) {
            if (is_string($placeholder) && strpos($placeholder, "$prefix-") !== false) {
                $var = substr($placeholder, 5);
                $name = strtolower($var);
                $this->setHtmlVariableTemplate("$prefix-$var", $this->lang("lang_$name"));
            }
        }
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

    private function setArrayLangItems($blockName) {
        $prefix = "TEXT";
        $placeholders = $this->tpl->getPlaceholderList($blockName);
        foreach ($placeholders as $placeholder) {
            if (is_string($placeholder) && strpos($placeholder, "$prefix-") !== false) {
                $var = substr($placeholder, 5);
                $name = strtolower($var);
                $this->setHtmlVariableTemplate("$prefix-$var", $this->lang("lang_$name"));
            }
        }
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

    public function setFormTemplate($name, array $params, $application, $navigation, $urlparams = null) {
        $name = strtoupper($name);
        $this->setVariableTemplate("$name-ID", $this->navigation . "$name");
        $this->setVariableTemplate("$name-ACTION", $this->getAppUrl($application, $navigation, $urlparams));
        foreach ($params as $param) {
            if (get_class($param) == "Param") {
                $this->setFormParam($param, $name);
            } else if ($param instanceof DataObject) {
                $this->setFormObject($param, $name);
            }
        }
    }

    private function setFormObject(DataObject $object, $formName) {
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        $objectName = get_class($object);
        $objectFormName = strtoupper($objectName);
        foreach ($properties as $property) {
            $paramName = strtoupper($property["name"]);
            $getter = "get" . strtoupper(substr($property["name"], 0, 1)) . substr($property["name"], 1);
            $value = $object->$getter();
            if ($property["type"] == PropertyTypes::$_LIST || $property["type"] == PropertyTypes::$_STRINGLIST) {
                $this->setVariableTemplate("$formName-NAME-$objectFormName-$paramName", "$objectName" . "_" . $property["name"]);
                $items = $object->__getList($property["name"], $this);
                foreach ($items as $item=> $text) {
                    $this->setHTMLArrayTemplate(array(
                        "$formName-LISTVALUE-$objectFormName-$paramName"=>$this->utf8tohtml(strval($item), true),
                        "$formName-LISTTEXT-$objectFormName-$paramName"=>$this->lang($text),
                        "$formName-LISTSELECTED-$objectFormName-$paramName"=>($item == $value) ? "selected" : ""
                    ));
                    $this->parseTemplate($paramName);
                }
            } else if ($property["type"] == PropertyTypes::$_SHORTLIST) {
                $this->setVariableTemplate("$formName-NAME-$objectFormName-$paramName", "$objectName" . "_" . $property["name"]);
                $items = $object->__getList($property["name"], $this);
                foreach ($items as $item=> $text) {
                    $this->setHTMLArrayTemplate(array(
                        "$formName-LISTVALUE-$objectFormName-$paramName"=>$this->utf8tohtml(strval($item), true),
                        "$formName-LISTTEXT-$objectFormName-$paramName"=>$this->lang($text),
                        "$formName-LISTSELECTED-$objectFormName-$paramName"=>($item == $value) ? "checked" : ""
                    ));
                    $this->parseTemplate($paramName);
                }
            } else if ($property["type"] == PropertyTypes::$_BOOLEAN) {
                $this->setVariableTemplate("$formName-NAME-$objectFormName-$paramName", "$objectName" . "_" . $property["name"]);
                $this->setVariableTemplate("$formName-VALUE-$objectFormName-$paramName", ObjectBoolean::$_YES);
                if ($value) {
                    $this->setVariableTemplate("$formName-SELECTED-$objectFormName-$paramName", "checked");
                } else {
                    $this->setVariableTemplate("$formName-SELECTED-$objectFormName-$paramName", "");
                }
            } else {
                $this->setVariableTemplate("$formName-NAME-$objectFormName-$paramName", "$objectName" . "_" . $property["name"]);
                if ($property["type"] == PropertyTypes::$_DATE) {
                    $value = $this->formatWiredDate($value);
                } else if ($property["type"] == PropertyTypes::$_TIME) {
                    $value = $this->formatWiredTime($value);
                } else {
                    $value = $this->utf8tohtml(strval($value), true);
                }
                $this->setVariableTemplate("$formName-VALUE-$objectFormName-$paramName", $value);
            }
        }
    }

    public function setViewObject(DataObject $object) {
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        $objectName = get_class($object);
        $objectFormName = strtoupper($objectName);
        foreach ($properties as $property) {
            $paramName = strtoupper($property["name"]);
            $getter = "get" . strtoupper(substr($property["name"], 0, 1)) . substr($property["name"], 1);
            $value = $object->$getter();
            if ($property["type"] == PropertyTypes::$_LIST || $property["type"] == PropertyTypes::$_STRINGLIST) {
                $items = $object->__getList($property["name"], $this);
                $this->setVariableTemplate("$objectFormName-$paramName", $this->lang($this->item($items, $value)));
            } else if ($property["type"] == PropertyTypes::$_SHORTLIST) {
                $items = $object->__getList($property["name"], $this);
                $this->setVariableTemplate("$objectFormName-$paramName", $this->lang($this->item($items, $value)));
            } else {
                $this->setVariableTemplate("$objectFormName-$paramName", $this->formatProperty($object, $property));
            }
        }
    }

    private function setFormParam(Param $param, $formName) {
        $viewParam = strtoupper($param->name);
        $this->setVariableTemplate("$formName-NAME-" . $viewParam, $param->name);
        $this->setVariableTemplate("$formName-VALUE-" . $viewParam, $this->utf8tohtml(strval($param->value), true));
    }

    public function unescapeJsonVariable($value) {
        $value = strval($value);
        return $value;
    }

    public function setVariableTemplate($name, $value) {
        $value = strval($value);
        $this->tpl->setVariable($name, $value);
    }

    private function makeBreaks($value) {
        $value = str_replace('\r\n', "\r\n", $value);
        $value = str_replace('\n', "\n", $value);
        return $value;
    }

    public function setHTMLVariableTemplate($name, $value) {
        $value = strval($value);
        $var = $this->utf8tohtml($value, true);
        $var = $this->makeBreaks($var);
        $this->tpl->setVariable($name, $var);
    }

    public function setVariableCustomTemplate($tpl, $name, $value) {
        $value = strval($value);
        $tpl->setVariable($name, $value);
    }

    public function setHTMLArrayCustomTemplate($tpl, $array) {
        foreach ($array as &$value) {
            $value = strval($value);
            $value = $this->utf8tohtml($value, true);
            $value = $this->makeBreaks($value);
        }
        $tpl->setVariable($array);
    }

    public function setHTMLVariableCustomTemplate($tpl, $name, $value) {
        $value = strval($value);
        $var = $this->utf8tohtml($value, true);
        $var = $this->makeBreaks($var);
        $tpl->setVariable($name, $var);
    }

    public function setArrayTemplate($array) {
        foreach ($array as &$value) {
            $value = strval($value);
        }
        $this->tpl->setVariable($array);
    }

    public function setHTMLArrayTemplate($array) {
        foreach ($array as &$value) {
            $value = strval($value);
            $value = $this->utf8tohtml($value, true);
            $value = $this->makeBreaks($value);
        }
        $this->tpl->setVariable($array);
    }

    public function parseTemplate($name) {
        $this->setArrayLangItems($name);
        $this->tpl->parse($name);
    }

    public function parseCustomTemplate($tpl, $name) {
        $this->setArrayLangCustomItems($tpl, $name);
        $tpl->parse($name);
    }

    public function endApp() {
        if ($this->data) {
            $this->data->close();
        }
        exit();
    }

    public function render() {
        $this->setLangItems($this->name);
        $this->tpl->touchBlock($this->name);
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
            foreach ($object as $key=> $value) {
                $object[$key] = $this->unescapeJsonObject($value);
            }
            return $object;
        }
        if (is_object($object)) {
            $vars = get_object_vars($object);
            foreach ($vars as $key=> $value) {
                $object->$key = $this->unescapeJsonObject($value);
            }
            return $object;
        }
        return $object;
    }

    public function renderToPdf($creator, $author, $margin = 10, $scale = 1, $font = "helvetica", $fontsize = "12", $orientation = "P", $unit = "mm", $format = "LETTER") {
        $this->setLangItems($this->name);
        $this->tpl->touchBlock($this->name);
        $html = $this->tpl->get();
        $pdf = new TCPDF($orientation, $unit, $format, true, 'UTF-8', false);
        $pdf->SetCreator($creator);
        $pdf->SetAuthor($author);
        $pdf->setHeaderFont(Array($font, '', $fontsize));
        $pdf->setFooterFont(Array($font, '', $fontsize));
        $pdf->SetDefaultMonospacedFont($font);
        $pdf->SetMargins($margin, $margin, $margin, true);
        $pdf->SetHeaderMargin($margin);
        $pdf->SetFooterMargin($margin);
        $pdf->SetAutoPageBreak(TRUE, $margin);
        $pdf->setImageScale($scale);
        $pdf->setFontSubsetting(true);
        $pdf->SetFont($font, '', $fontsize, '', true);
        $pdf->AddPage();
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        $pdf->Output("file", 'I');
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

    public function renderCustomTemplate($tpl) {
        $this->setLangCustomItems($tpl);
        return $tpl->get();
    }

    public function hideBlockTemplate($name) {
        $this->tpl->parse($name);
        $this->tpl->hideBlock($name);
    }

    public function redirectToUrl($url) {
        //Try redirect
        header(sprintf("Location: %s", $url));
        $this->endApp();
    }

    public function redirect($app, $navigation, $params = null) {
        //Try redirect
        header(sprintf("Location: %s", $this->getAppUrl($app, $navigation, $params)));
        $this->endApp();
    }

    public function upload($fileParam, $optional = false, $full = true, $secure = true, $bucket = null) {
        if (!isset($_FILES[$fileParam])) {
            if (!$optional) {
                $this->error($this->lang('lang_filenotfound'));
            } else {
                return null;
            }
        }
        if ($_FILES[$fileParam]['error'] == UPLOAD_ERR_INI_SIZE) {
            if (!$optional) {
                $this->error($this->lang('lang_filesize'));
            } else {
                return null;
            }
        }
        if (!is_uploaded_file($_FILES[$fileParam]['tmp_name'])) {
            if (!$optional) {
                $this->error($this->lang('lang_filenotuploaded'));
            } else {
                return null;
            }
        }
        $cfile = curl_file_create($_FILES[$fileParam]['tmp_name'], null, 'file');
        if (!$cfile) {
            $this->error($this->lang('lang_filecreate'));
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mylodon.cl/index.php?app=json&nav=upload-file");
        curl_setopt($ch, CURLOPT_POST, 1);
        $post = array('file'=>$cfile, 'appsecret'=>$this->config('mylodon_apikey'));
        if ($bucket) {
            $post['bucket'] = $bucket;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if ($result) {
            $r = json_decode($result);
            if ($r->status == "error") {
                if (!$optional) {
                    $this->error($this->lang($r->error));
                } else {
                    return null;
                }
            } else if ($r->status == "success") {
                if ($full) {
                    if ($secure) {
                        return $r->secureurl;
                    } else {
                        return $r->fullurl;
                    }
                } else {
                    return $r->url;
                }
            }
        }
        curl_close($ch);
        if (!$optional) {
            $this->error($this->lang('lang_mylodonerror'));
        } else {
            return null;
        }
    }

    public function uploadPhoto($fileParam, $optional = false, $w = 256, $h = 256, $full = true, $secure = true, $bucket = null) {
        if (!isset($_FILES[$fileParam])) {
            if (!$optional) {
                $this->error($this->lang('lang_filenotfound'));
            } else {
                return null;
            }
        }
        if ($_FILES[$fileParam]['error'] == UPLOAD_ERR_INI_SIZE) {
            if (!$optional) {
                $this->error($this->lang('lang_filesize'));
            } else {
                return null;
            }
        }
        if (!is_uploaded_file($_FILES[$fileParam]['tmp_name'])) {
            if (!$optional) {
                $this->error($this->lang('lang_filenotuploaded'));
            } else {
                return null;
            }
        }
        $cfile = curl_file_create($_FILES[$fileParam]['tmp_name'], null, 'image');
        if (!$cfile) {
            $this->error($this->lang('lang_filecreate'));
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mylodon.cl/index.php?app=json&nav=upload-image");
        curl_setopt($ch, CURLOPT_POST, 1);
        $post = array('image'=>$cfile, 'appsecret'=>$this->config('mylodon_apikey'), 'w'=>$w, 'h'=>$h);
        if ($bucket) {
            $post['bucket'] = $bucket;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if ($result) {
            $r = json_decode($result);
            if ($r->status == "error") {
                if (!$optional) {
                    $this->error($this->lang($r->error));
                } else {
                    return null;
                }
            } else if ($r->status == "success") {
                if ($full) {
                    if ($secure) {
                        return $r->secureurl;
                    } else {
                        return $r->fullurl;
                    }
                } else {
                    return $r->url;
                }
            }
        }
        curl_close($ch);
        if (!$optional) {
            $this->error($this->lang('lang_mylodonerror'));
        } else {
            return null;
        }
    }

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

    public function createRandomNumber() {
        srand((double) microtime() * 1000000);
        $num = rand();
        return $num;
    }

    public function formatProperty(DataObject $object, $property) {
        $getter = "get" . strtoupper(substr($property["name"], 0, 1)) . substr($property["name"], 1);
        $value = $object->$getter();
        switch ($property["type"]) {
            case PropertyTypes::$_INT :
            case PropertyTypes::$_LONG : {
                    return $this->formatInteger($value);
                }
            case PropertyTypes::$_DOUBLE : {
                    return $this->formatDouble($value);
                }
            case PropertyTypes::$_INT :
            case PropertyTypes::$_LIST :
            case PropertyTypes::$_STRINGLIST :
            case PropertyTypes::$_SHORTLIST : {
                    return $this->lang($this->item($object->__getList($property["name"], $this), $value));
                }
            case PropertyTypes::$_URL :
            case PropertyTypes::$_EMAIL :
            case PropertyTypes::$_RUT :
            case PropertyTypes::$_STRING :
            case PropertyTypes::$_STRING1 :
            case PropertyTypes::$_STRING2 :
            case PropertyTypes::$_STRING4 :
            case PropertyTypes::$_STRING8 :
            case PropertyTypes::$_STRING16 :
            case PropertyTypes::$_STRING24 :
            case PropertyTypes::$_STRING32 :
            case PropertyTypes::$_STRING64 :
            case PropertyTypes::$_STRING128 :
            case PropertyTypes::$_STRING256 :
            case PropertyTypes::$_STRING512 :
            case PropertyTypes::$_STRING1024 :
            case PropertyTypes::$_STRING2048 :
            case PropertyTypes::$_LETTERS :
            case PropertyTypes::$_ALPHANUMERIC : {
                    return $value;
                }
            case PropertyTypes::$_PASSWORD: {
                    return "";
                }
            case PropertyTypes::$_FILE : {
                    return "";
                }
            case PropertyTypes::$_IMAGE256 : {
                    return "";
                }
            case PropertyTypes::$_FLAT :
            case PropertyTypes::$_JSON :
            case PropertyTypes::$_STRINGARRAY :
            case PropertyTypes::$_DOUBLEARRAY :
            case PropertyTypes::$_INTARRAY : {
                    return strval($value);
                }
            case PropertyTypes::$_TIME : {
                    return $this->formatTime($value);
                }
            case PropertyTypes::$_DATE : {
                    return $this->formatDate($value);
                }
            case PropertyTypes::$_BOOLEAN : {
                    return $this->formatBoolean($value);
                }
            default : {
                    return null;
                }
        }
    }

    public function formatBoolean($boolean) {
        return $boolean ? $this->lang('lang_yes') : $this->lang('lang_no');
    }

    public function formatMount($mount) {
        if (isset($mount)) {
            return '$' . number_format($mount, 2, ",", ".");
        } else {
            return '';
        }
    }

    public function formatNumber($number) {
        if (isset($number)) {
            return number_format($number, 0, ",", ".");
        } else {
            return '';
        }
    }

    public function formatInteger($number) {
        if (isset($number)) {
            return number_format($number, 0, ",", ".");
        } else {
            return '';
        }
    }

    public function formatDouble($number) {
        if (isset($number)) {
            return number_format($number, 2, ",", ".");
        } else {
            return '';
        }
    }

    public function formatPercentage($percentage) {
        if (isset($percentage) && $percentage > 0) {
            return number_format($percentage, 2, ",", ".") . ' %';
        } else {
            return '';
        }
    }

    public function formatDate($date) {
        if (isset($date) && $date != "") {
            return date('d/m/Y', $date);
        } else {
            return '';
        }
    }

    public function formatTime($date) {
        if (isset($date) && $date != "") {
            return date('H:i', $date);
        } else {
            return '';
        }
    }

    public function formatWiredDate($date) {
        if (isset($date) && $date != "") {
            return date('Y-m-d', $date);
        } else {
            return '';
        }
    }

    public function formatWiredTime($date) {
        if (isset($date) && $date != "") {
            return date('H:i', $date);
        } else {
            return '';
        }
    }

    function blowfishCrypt($password, $cost) {
        $chars = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $salt = sprintf('$2a$%02d$', $cost);
        for ($i = 0; $i < 22; $i++) {
            $salt.=$chars[rand(0, 63)];
        }
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
        $string = strval($string);
        if ($langstr && !array_key_exists($langstr, Lang::$_ENUM)) {
            $langstr = Lang::$_DEFAULT;
        }
        if ($langstr && $langstr != $this->config('lang')) {
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

    private function valida_rut($r) {
        $r = strtoupper(ereg_replace('\.|,|-', '', $r));
        $sub_rut = substr($r, 0, strlen($r) - 1);
        $sub_dv = substr($r, -1);
        $x = 2;
        $s = 0;
        for ($i = strlen($sub_rut) - 1; $i >= 0; $i--) {
            if ($x > 7) {
                $x = 2;
            }
            $s += $sub_rut[$i] * $x;
            $x++;
        }
        $dv = 11 - ($s % 11);
        if ($dv == 10) {
            $dv = 'K';
        }
        if ($dv == 11) {
            $dv = '0';
        }
        if ($dv == $sub_dv) {
            return $sub_rut . $sub_dv;
        } else {
            return false;
        }
    }

    public function alterLang($langstr) {
        if ($langstr && !array_key_exists($langstr, Lang::$_ENUM)) {
            $langstr = Lang::$_DEFAULT;
        }
        if ($langstr && $langstr != $this->config('lang')) {
            if (@require("lang/lang.$langstr.php")) {
                $this->lang = $lang;
                $this->config['lang'] = $langstr;
                return true;
            }
        }
        return false;
    }

    protected function script_generateBeans() {
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

    protected function script_generatePassword() {
        $clave = $this->navigation = $this->getUrlParam("password", PropertyTypes::$_STRING);
        echo $clave . " converted to " . $this->blowfishCrypt($clave, 10);
        echo "\n";
    }

    protected function script_generateLangFiles() {
        $langs = array();
        $langs = array_merge($langs, $this->script_scanPHPLangs('./app'));
        $langs = array_merge($langs, $this->script_scanPHPLangs('./beans'));
        $langs = array_merge($langs, $this->script_scanPHPLangs('./int'));
        $langs = array_merge($langs, $this->script_scanPHPLangs('./nav'));
        $langs = array_merge($langs, $this->script_scanHTMLLangs('./templates'));
        $langs = array_merge($langs, $this->script_scanHTMLLangs('./views'));
        $langs = array_merge($langs, $this->script_scanPHPLangs('./lib/bicubic'));
        $langs = array_merge($langs, $this->script_scanBeansLangs());

        $navigation = new Navigation($this);
        $langs = $navigation->sortByValue($langs);
        foreach (Lang::$_ENUM as $valueVal=> $langName) {
            if (!file_exists("./lang/lang.$valueVal.php")) {
                $str = "<?php\n";
                $str .= "\$lang = array();\n";
                foreach ($langs as $key=> $value) {
                    $str .= "\$lang['$value'] = '$value';\n";
                }
                $str .= "\n";
                file_put_contents("./lang/lang.$valueVal.php", $str);
            } else {
                include("lang/lang.$valueVal.php");
                foreach ($langs as $key=> $value) {
                    if (!array_key_exists($key, $lang)) {
                        $lang[$key] = $value;
                    }
                }
                foreach ($lang as $key=> $value) {
                    if (!array_key_exists($key, $langs)) {
                        unset($lang[$key]);
                        echo "unset $key \n";
                    }
                }
                $lang = $navigation->sortByKey($lang);
                $str = "<?php\n";
                $str .= "\$lang = array();\n";
                foreach ($lang as $key=> $value) {
                    if ($value) {
                        $str .= "\$lang['$key'] = '$value';\n";
                    }
                }
                $str .= "\n";
                file_put_contents("./lang/lang.$valueVal.php", $str);
            }
        }
    }

    protected function script_scanPHPLangs($dir) {
        $langs = array();
        $handle = opendir($dir);
        if ($handle) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && $entry != "ScriptApplication.php") {
                    echo "$dir/$entry\n";
                    if (is_dir("$dir/$entry")) {
                        $langs = array_merge($langs, $this->script_scanPHPLangs("$dir/$entry"));
                    } else {
                        $str = file_get_contents("$dir/$entry");
                        $lastPos = 0;
                        $needle = "'lang_";
                        while (($lastPos = strpos($str, $needle, $lastPos)) !== false) {
                            $brpos = strpos($str, "\n", $lastPos + strlen($needle));
                            $endpos = strpos($str, "'", $lastPos + strlen($needle));
                            if ($brpos !== FALSE && $endpos < $brpos) {
                                $lang = substr($str, $lastPos + 1, $endpos - $lastPos - 1);
                                echo "-      $lang\n";
                                $langs [$lang] = $lang;
                            }
                            $lastPos = $lastPos + strlen($needle);
                        }
                    }
                }
            }
            closedir($handle);
        }
        return $langs;
    }

    protected function script_scanHTMLLangs($dir) {
        $langs = array();
        $handle = opendir($dir);
        if ($handle) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    echo "$dir/$entry\n";
                    if (is_dir("$dir/$entry")) {
                        $langs = array_merge($langs, $this->script_scanHTMLLangs("$dir/$entry"));
                    } else {
                        $str = file_get_contents("$dir/$entry");
                        $lastPos = 0;
                        $needle = "{LANG-";
                        while (($lastPos = strpos($str, $needle, $lastPos)) !== false) {
                            $endpos = strpos($str, "}", $lastPos + strlen($needle));
                            $lang = substr($str, $lastPos + 1, $endpos - $lastPos - 1);
                            $lang = strtolower($lang);
                            $lang = str_replace("-", "_", $lang);
                            echo "-      $lang\n";
                            $langs [$lang] = $lang;
                            $lastPos = $lastPos + strlen($needle);
                        }
                        $needle = "{TEXT-";
                        while (($lastPos = strpos($str, $needle, $lastPos)) !== false) {
                            $endpos = strpos($str, "}", $lastPos + strlen($needle));
                            $lang = substr($str, $lastPos + 1, $endpos - $lastPos - 1);
                            $lang = strtolower($lang);
                            $lang = str_replace("-", "_", $lang);
                            $lang = str_replace("text_", "lang_", $lang);
                            echo "-      $lang\n";
                            $langs [$lang] = $lang;
                            $lastPos = $lastPos + strlen($needle);
                        }
                    }
                }
            }
            closedir($handle);
        }
        return $langs;
    }

    protected function script_scanBeansLangs() {
        echo "BEANS\n";
        $langs = array();
        foreach (get_declared_classes() as $classname) {
            if (is_subclass_of($classname, "DataObject")) {
                $object = new $classname();
                $name = strtolower($classname);
                $lang = "lang_$name";
                $langs[$lang] = $lang;
                echo "-      $lang\n";
                $properties = $object->__getProperties();
                foreach ($properties as $property) {
                    $lang = $property["lang"];
                    $langs[$lang] = $lang;
                    echo "-      $lang\n";
                }
            }
        }
        return $langs;
    }

    protected function script_generateDB($out = true) {
        $tablequery = "";
        $indexquery = "";
        $constraintquery = "";
        $dropquery = "";
        $tables = array();
        $data = new PostgreSQLData($this->config);
        $query = "SELECT table_name FROM information_schema.tables WHERE table_schema='public'";
        $result = $data->performRead($query);
        while ($row = $data->readNext($result)) {
            $class = $row['table_name'];
            $tables [] = $class;
        }
        //get existing bean names
        $beans = array();
        foreach (get_declared_classes() as $classname) {
            if (is_subclass_of($classname, "DataObject")) {
                $tablename = strtolower($classname);
                $beans[] = $tablename;
            }
        }
        //go
        foreach ($beans as $bean) {
            //check if table exist to create new
            $existbean = false;
            foreach ($tables as $table) {
                if ($table == $bean) {
                    $existbean = true;
                    break;
                }
            }
            if (!$existbean) {
                $tablequery .= $this->script_createTable($bean);
                $tablequery .= $this->script_createColumns($bean);
                $indexquery .= $this->script_createIndexes($bean);
                $constraintquery .= $this->script_createCosntraints($bean);
            } else {
                $columns = array();
                //get existing columns
                $query = "SELECT column_name, ordinal_position FROM information_schema.columns WHERE table_name = '$bean'";
                $result = $data->performRead($query);
                while ($row = $data->readNext($result)) {
                    $columns [] = $row;
                }
                //get object properties
                $object = new $bean();
                $properties = $object->__getProperties();
                foreach ($properties as $property) {
                    if (!$property["serializable"]) {
                        continue;
                    }
                    $existcol = false;
                    $colposition = 0;
                    foreach ($columns as $column) {

                        if ($column['column_name'] == $property["name"]) {
                            $existcol = true;
                            $colposition = $column['ordinal_position'];
                            break;
                        }
                    }
                    if (!$existcol) {

                        $tablequery .= $this->script_createColumn($object, $property);
                        $indexquery .= $this->script_createColumnIndex($object, $property);
                        $constraintquery .= $this->script_createColumnConstraint($object, $property);
                    } else {
                        $name = $property["name"];
                        $query = "select description from pg_catalog.pg_description where objoid = (select c.oid from pg_catalog.pg_class c where c.relname = '$bean') and objsubid = $colposition;";
                        $result = $data->performRead($query);
                        $description = "";
                        $row = $data->readNext($result);
                        if ($row) {
                            $description = $row["description"];
                        }
                        if ($description != $this->script_createColumnComment($object, $property)) {
                            $tablequery .= "ALTER TABLE $bean DROP COLUMN $name;";
                            $tablequery .= $this->script_createColumn($object, $property);
                            $indexquery .= $this->script_createColumnIndex($object, $property);
                            $constraintquery .= $this->script_createColumnConstraint($object, $property);
                        }
                    }
                }
                //look for columns to delete
                foreach ($columns as $column) {
                    $existcolumn = false;
                    foreach ($properties as $property) {
                        if ($column['column_name'] == $property["name"]) {
                            $existcolumn = true;
                            break;
                        }
                    }
                    if (!$existcolumn) {
                        $columnname = $column['column_name'];
                        $dropquery .= "ALTER TABLE $bean DROP COLUMN $columnname;";
                    }
                }
            }
        }
        foreach ($tables as $table) {
            $existtable = false;
            foreach ($beans as $bean) {
                if ($table == $bean) {
                    $existtable = true;
                    break;
                }
            }
            if (!$existtable) {
                $columnname = $column['column_name'];
                $dropquery .= "DROP TABLE $bean;";
            }
        }

        $sql = trim($dropquery . $tablequery . $indexquery . $constraintquery);
        if ($out) {
            echo "$sql\n";
        } else {
            if ($sql) {
                $result = $data->performWrite($sql);
                if ($result) {
                    echo "ok\n";
                } else {
                    echo "fail\n";
                }
            } else {
                echo "no update\n";
            }
        }
    }

    private function script_createTable($beanname) {
        $sql = "CREATE TABLE $beanname ();";
        return $sql;
    }

    private function script_createColumns($beanname) {
        $object = new $beanname();
        $sql = "";
        $properties = $object->__getProperties();
        foreach ($properties as $property) {
            if (!$property["serializable"]) {
                continue;
            }
            $sql .= $this->script_createColumn($object, $property);
        }
        return $sql;
    }

    private function script_createIndexes($beanname) {
        $object = new $beanname();
        $sql = "";
        $properties = $object->__getProperties();
        foreach ($properties as $property) {
            if (!$property["serializable"]) {
                continue;
            }
            $sql .= $this->script_createColumnIndex($object, $property);
        }
        return $sql;
    }

    private function script_createCosntraints($beanname) {
        $object = new $beanname();
        $sql = "";
        $properties = $object->__getProperties();
        foreach ($properties as $property) {
            if (!$property["serializable"]) {
                continue;
            }
            $sql .= $this->script_createColumnConstraint($object, $property);
        }

        return $sql;
    }

    private function script_createColumn(DataObject $object, $property) {
        $class = strtolower(get_class($object));
        $sql = "";
        $name = $property["name"];
        $type = PropertyTypes::$_POSTGRESQLTYPES[$property["type"]];
        $notnull = $property["required"] ? "NOT NULL" : "";
        $default = $property["default"] ? "DEFAULT '" . $property["default"] . "' " : "";
        if ($name == "id" && !$object->__isChild()) {
            $sql .= "ALTER TABLE $class ADD COLUMN id serial NOT NULL;";
        } else if ($name == "id" && $object->__isChild()) {
            $sql .= "ALTER TABLE $class ADD COLUMN id bigint NOT NULL;";
        } else {
            $sql .= "ALTER TABLE $class ADD COLUMN $name $type $notnull $default;";
        }

        $sql .= "COMMENT ON COLUMN $class.$name is '" . $this->script_createColumnComment($object, $property) . "';";

        return $sql;
    }

    private function script_createColumnComment(DataObject $object, $property) {
        $name = $property["name"];
        $type = PropertyTypes::$_POSTGRESQLTYPES[$property["type"]];
        $notnull = $property["required"] ? "NOT NULL" : "";
        $default = $property["default"] ? "DEFAULT " . $property["default"] : "";
        $index = $property["index"] ? "index" : "";
        $unique = $property["unique"] ? "unique" : "";
        $references = $property["reference"] ? $property["reference"] : "";
        return trim("$name $type $notnull $default $index $unique $references");
    }

    private function script_createColumnIndex(DataObject $object, $property) {
        $class = strtolower(get_class($object));
        $sql = "";
        $name = $property["name"];
        if ($property["index"]) {
            if (array_key_exists("unique", $property) && $property["unique"]) {
                $sql.= "CREATE UNIQUE INDEX $class" . "_" . "$name" . "_index ON $class USING btree ($name);";
            } else {
                $sql.= "CREATE INDEX $class" . "_" . "$name" . "_index ON $class USING btree ($name);";
            }
        }
        return $sql;
    }

    private function script_createColumnConstraint(DataObject $object, $property) {
        $class = strtolower(get_class($object));
        $sql = "";
        $name = $property["name"];
        if ($name == "id" && !$object->__isChild()) {
            $sql.= "ALTER TABLE ONLY $class ADD CONSTRAINT $class" . "_pk PRIMARY KEY (id);";
        } else if ($name == "id" && $object->__isChild()) {
            $sql.= "ALTER TABLE ONLY $class ADD CONSTRAINT $class" . "_pk PRIMARY KEY (id);";
        }
        if ($property["reference"] !== null) {
            $sql.= "ALTER TABLE ONLY $class ADD CONSTRAINT $class" . "_" . $property["name"] . "_fkey FOREIGN KEY (" . $property["name"] . ") REFERENCES " . $property["reference"] . "(id) MATCH FULL;";
        }
        return $sql;
    }

    public function sendEmail($to, $subject, $html, $text = "") {
        $mandrillEmail = new MandrillEmail($to, $this->config('web_contact_email'), $this->config('web_contact_name'), $subject);
        if (!$mandrillEmail->send($this->config('mandrill_key'), $html, $text)) {
            $this->error($this->lang('lang_erroremail') . " - " . $mandrillEmail->error);
        }
    }

    public function getLocationFromIP() {
        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $ip = $_SERVER['REMOTE_ADDR'];
            if ($ip) {
                $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
                if ($details && property_exists($details, "loc")) {
                    $latlong = explode(",", $details->loc);
                    if (count($latlong) == 2) {
                        return array("latitude"=>doubleval($latlong[0]), "longitude"=>doubleval($latlong[1]));
                    }
                }
            }
        }
        return array("latitude"=>doubleval(0), "longitude"=>doubleval(0));
    }

    public function maintainers(array $navigations) {
        foreach ($navigations as $className) {
            switch ($this->navigation) {
                case "bicubic-$className" : {
                        require_once("nav/$className.php");
                        $navigation = new $className($this);
                        $navigation->records();
                        break;
                    }
                case "bicubic-$className-search" : {
                        require_once("nav/$className.php");
                        $navigation = new $className($this);
                        $navigation->search();
                        break;
                    }
                case "bicubic-$className-reorder" : {
                         require_once("nav/$className.php");
                        $navigation = new $className($this);
                        $navigation->reorder();
                        break;
                    }
                case "bicubic-$className-json" : {
                        require_once("nav/$className.php");
                        $navigation = new $className($this);
                        $navigation->json();
                        break;
                    }
                case "bicubic-$className-add" : {
                         require_once("nav/$className.php");
                        $navigation = new $className($this);
                        $navigation->add();
                        break;
                    }
                case "bicubic-$className-addSubmit" : {
                        require_once("nav/$className.php");
                        $navigation = new $className($this);
                        $navigation->addSubmit();
                        break;
                    }
                case "bicubic-$className-edit" : {
                        require_once("nav/$className.php");
                        $navigation = new $className($this);
                        $navigation->edit();
                        break;
                    }
                case "bicubic-$className-editSubmit" : {
                         require_once("nav/$className.php");
                        $navigation = new $className($this);
                        $navigation->editSubmit();
                        break;
                    }
                case "bicubic-$className-delete" : {
                        require_once("nav/$className.php");
                        $navigation = new $className($this);
                        $navigation->delete();
                        break;
                    }
            }
        }
    }

}