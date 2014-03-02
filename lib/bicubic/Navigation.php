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
        return $this->application->lang($string, $langstr);
    }

    public function config($string) {
        return $this->application->config($string);
    }

    public function item($array, $key, $default = null, $langstr = null) {
        return $this->application->item($array, $key, $default, $langstr);
    }

    public function error($message) {
        return $this->application->error($message);
    }

    public function message($message) {
        return $this->application->message($message);
    }

    public function photoUrl($baseUrl) {
        return $this->application->photoUrl($baseUrl);
    }

    public function compareLangStrings($a, $b) {
        return strcasecmp($this->lang($a), $this->lang($b));
    }

    public function compareObjectsByLangName($a, $b) {
        return strcasecmp($a->getName(), $b->getName());
    }

    public function compareObjectsByComparer($a, $b) {
        if ($a->comparer < $b->comparer) {
            return -1;
        }
        if ($a->comparer == $b->comparer) {
            return 0;
        }
        if ($a->comparer > $b->comparer) {
            return 1;
        }
    }

    public function compareObjectsByTimeBack($a, $b) {
        if ($a->time > $b->time) {
            return -1;
        }
        if ($a->time == $b->time) {
            return 0;
        }
        if ($a->time < $b->time) {
            return 1;
        }
    }

    public function compareObjectsByComparerAndName($a, $b) {
        if ($a->comparer < $b->comparer) {
            return -1;
        }
        if ($a->comparer == $b->comparer) {
            return strcasecmp($a->getName(), $b->getName());
        }
        if ($a->comparer > $b->comparer) {
            return 1;
        }
    }

    public function compareObjectsByName($a, $b) {
        return strcasecmp($this->lang($a->getName()), $this->lang($b->getName()));
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

    public function compareJsonObjectsByDistance($a, $b) {
        if ($a->distance < $b->distance) {
            return -1;
        }
        if ($a->distance == $b->distance) {
            return 0;
        }
        if ($a->distance > $b->distance) {
            return 1;
        }
    }

    public function normalize($string) {
        return ucwords(strtolower(trim($string)));
    }

    public function objectForm(DataObject $object, $secure = true) {
        $id = $this->application->getUrlParam($this->config("param_id"), "int");
        if ($id) {
            $data = new TransactionManager($this->application->data);
            $object->setId($id);
            $object = $data->getRecord($object);
            if (!$object) {
                $this->error("lang_notvalid");
            }
        }
        $this->application->setMainTemplate("bicubic", "form");
        $name = get_class($object);
        $this->application->setVariableTemplate("FORM-ID", $this->application->navigation . "$name");
        if ($secure) {
            $this->application->setVariableTemplate("FORM-ACTION", $this->application->getSecureAppUrl($this->application->name, $this->application->navigation . "Submit"));
        } else {
            $this->application->setVariableTemplate("FORM-ACTION", $this->application->getAppUrl($this->application->name, $this->application->navigation . "Submit"));
        }
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        $formContent = "";
        foreach ($properties as $property) {
            $cammelName = strtoupper(substr($property["name"], 0, 1)) . substr($property["name"], 1);
            $getter = "get$cammelName";
            if (array_key_exists("private", $property) && $object->$getter()) {
                continue;
            }
            else if (array_key_exists("hidden", $property) && $object->$getter()) {
                $result = $this->application->setCustomTemplate("bicubic", "hidden");
                $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME", $name . "_" . $property["name"]);
                $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-VALUE", $object->$getter());
                $formContent .= $this->application->renderCustomTemplate($result);
            } else if (array_key_exists("category", $property)) {
                $result = $this->application->setCustomTemplate("bicubic", "category");
                $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-LABEL", $this->lang("lang_" . $property["name"]));
                $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME", $name . "_" . $property["name"]);
                $selected = $object->$getter();
                foreach ($property["category"] as $value => $text) {
                    $this->application->setHTMLArrayCustomTemplate($result, array(
                        "CATEGORY-VALUE" => $value,
                        "CATEGORY-NAME" => $this->lang($text),
                        "CATEGORY-SELECTED" => ($value == $selected) ? "selected" : ""
                    ));
                    $this->application->parseCustomTemplate($result, "CATEGORIES");
                }
                $formContent .= $this->application->renderCustomTemplate($result);
            } else if (array_key_exists("option", $property)) {
                $result = $this->application->setCustomTemplate("bicubic", "option");
                $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-LABEL", $this->lang("lang_" . $property["name"]));
                $selected = $object->$getter();
                foreach ($property["option"] as $value => $text) {
                    $this->application->setHTMLArrayCustomTemplate($result, array(
                        "OBJECT-NAME-PROPERTY-NAME" => $name . "_" . $property["name"],
                        "OPTION-VALUE" => $value,
                        "OPTION-NAME" => $this->lang($text),
                        "OPTION-SELECTED" => ($value == $selected) ? "checked" : ""
                    ));
                    $this->application->parseCustomTemplate($result, "CATEGORIES");
                }
                $formContent .= $this->application->renderCustomTemplate($result);
            }
            else {
                switch ($property["type"]) {
                    case "alpha32" :
                    case "alphanumeric" :
                    case "date" :
                    case "double" :
                    case "int" :
                    case "letters" :
                    case "long" :
                    case "numeric" :
                    case "percentage" :
                    case "string" :
                    case "string" :
                    case "string1" :
                    case "string2" :
                    case "string4" :
                    case "string8" :
                    case "string16" :
                    case "string24" :
                    case "string32" :
                    case "string64" :
                    case "string128" :
                    case "string256" :
                    case "string512" :
                    case "string1024" :
                    case "string2048": {
                            $result = $this->application->setCustomTemplate("bicubic", $property["type"]);
                            $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-LABEL", $this->lang("lang_" . $property["name"]));
                            $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-PLACEHOLDER", $this->lang("lang_" . $property["name"] . "placeholder"));
                            $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME", $name . "_" . $property["name"]);
                            $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-VALUE", $object->$getter());
                            $formContent .= $this->application->renderCustomTemplate($result);
                            break;
                        }
                    case "boolean" : {
                            $result = $this->application->setCustomTemplate("bicubic", $property["type"]);
                            $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-LABEL", $this->lang("lang_" . $property["name"]));
                            $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME", $name . "_" . $property["name"]);
                            if ($object->$getter()) {
                                $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-SELECTED-YES", "selected");
                            } else {
                                $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-SELECTED-NO", "selected");
                            }
                            $formContent .= $this->application->renderCustomTemplate($result);
                            break;
                        }
                }
            }
        }
        $this->application->setVariableTemplate("FORM-CONTENT", $formContent);
        $this->application->render();
    }

    public function objectFormSubmit(DataObject $object, $callbackUrl) {
        $object = $this->application->getFormObject($object);
        $data = new TransactionManager($this->application->data);
        if(!$object->getId()) {
            if(!$data->insertRecord($object)) {
                $this->error("lang_errordatabase");
            }
        }
        else {
            if(!$data->updateRecord($object)) {
                $this->error("lang_errordatabase");
            }
        }
        $this->application->redirectToUrl($callbackUrl);
    }

}

?>
