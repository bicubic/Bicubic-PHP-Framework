<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
class Navigation {

    public $application;

    function __construct(Application $application) {
        $this->application = $application;
    }

    public function lang($string, $langstr = null) {
        return $this->application->lang($string,$langstr);
    }

    public function config($string) {
        return $this->application->config($string);
    }

    public function item($array, $key, $default = null, $langstr = null) {
        if (array_key_exists($key, $array)) {
            $string = $array[$key];
            if (stripos($string, "lang_") !== false) {
                return $this->lang($string, $langstr);
            } else if (strpos($string, "db_") !== false) {
                return $this->lang($string, $langstr);
            } else {
                return $string;
            }
        }
        return $default;
    }

    public function photoUrl($baseUrl) {
        return $this->application->photoUrl($baseUrl);
    }

    public function compareLangStrings($a, $b) {
        return strcmp($this->lang($a), $this->lang($b));
    }

    public function compareObjectsByName($a, $b) {
        return strcmp($a->getName(), $b->getName());
    }

    public function compareObjectsByLangName($a, $b) {
        return strcmp($this->lang($a->getName()), $this->lang($b->getName()));
    }

    public function compareObjectsByDistance($a, $b) {
        if ($a->getDistance() < $b->getDistance()) {
            return -1;
        }
        if ($a->getDistance() == $b->getDistance()) {
            return 0;
        }
        if ($a->getDistance() > $b->getDistance()) {
            return 1;
        }
    }

    public function normalize($string) {
        return ucwords(strtolower(trim($string)));
    }
    

}

?>
