<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
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

    public function sortByLang($array) {
        uasort($array, array("Navigation", "compareLangStrings"));
        return $array;
    }

    public function sortByValue($array) {
        uasort($array, array("Navigation", "compareStrings"));
        return $array;
    }

    public function sortByKey($array) {
        ksort($array, SORT_STRING);
        return $array;
    }

    public function compareLangStrings($a, $b) {
        return strcasecmp($this->lang($a), $this->lang($b));
    }

    public function compareStrings($a, $b) {
        return strcmp($a, $b);
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

    public function objectFormElement(DataObject $object, $property) {
        $objectName = get_class($object);
        $getter = "get" . strtoupper(substr($property["name"], 0, 1)) . substr($property["name"], 1);
        $value = $object->$getter();
        if ($this->item($property, "private", false)) {
            return "";
        } else if ($this->item($property, "hidden", false)) {
            $result = $this->application->setCustomTemplate("bicubic", "hidden");
            $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME", $objectName . "_" . $property["name"]);
            $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-VALUE", $value);
            return $this->application->renderCustomTemplate($result);
        } else {
            switch ($property["type"]) {
                case PropertyTypes::$_ALPHANUMERIC :
                case PropertyTypes::$_DOUBLE :
                case PropertyTypes::$_EMAIL :
                case PropertyTypes::$_RUT :
                case PropertyTypes::$_INT :
                case PropertyTypes::$_LETTERS :
                case PropertyTypes::$_LONG :
                case PropertyTypes::$_PASSWORD :
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
                case PropertyTypes::$_STRING2048 : {
                        $result = $this->application->setCustomTemplate("bicubic", $property["type"]);
                        $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-LABEL", $this->lang($property["lang"]));
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME", $objectName . "_" . $property["name"]);
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-VALUE", $value);
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-REQUIRED", $property["required"] ? "required" : "");
                        return $this->application->renderCustomTemplate($result);
                    }
                case PropertyTypes::$_FILE :
                case PropertyTypes::$_IMAGE256 : {
                        $result = $this->application->setCustomTemplate("bicubic", $property["type"]);
                        $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-LABEL", $this->lang($property["lang"]));
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME", $objectName . "_" . $property["name"]);
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-REQUIRED", $property["required"] ? "required" : "");
                        return $this->application->renderCustomTemplate($result);
                    }
                case PropertyTypes::$_DATE : {
                        $result = $this->application->setCustomTemplate("bicubic", $property["type"]);
                        $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-LABEL", $this->lang($property["lang"])); //date('d/m/Y', $date)
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME-DAY", $objectName . "_" . $property["name"] . "-day");
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-VALUE-DAY", ($value ? date('d', $value) : ""));
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME-MONTH", $objectName . "_" . $property["name"] . "-month");
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-VALUE-MONTH", ($value ? date('m', $value) : ""));
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME-YEAR", $objectName . "_" . $property["name"] . "-year");
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-VALUE-YEAR", ($value ? date('Y', $value) : ""));
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-REQUIRED", $property["required"] ? "required" : "");
                        return $this->application->renderCustomTemplate($result);
                    }
                case PropertyTypes::$_BOOLEAN : {
                        $result = $this->application->setCustomTemplate("bicubic", $property["type"]);
                        $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-LABEL", $this->lang($property["lang"]));
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME", $objectName . "_" . $property["name"]);
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-REQUIRED", $property["required"] ? "required" : "");
                        if ($value) {
                            $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-SELECTED-YES", "selected");
                        } else {
                            $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-SELECTED-NO", "selected");
                        }
                        return $this->application->renderCustomTemplate($result);
                    }
                case PropertyTypes::$_LIST : {
                        $result = $this->application->setCustomTemplate("bicubic", "list");
                        $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-LABEL", $this->lang($property["lang"]));
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-NAME", $objectName . "_" . $property["name"]);
                        $this->application->setHTMLVariableCustomTemplate($result, "OBJECT-NAME-PROPERTY-REQUIRED", $property["required"] ? "required" : "");
                        $items = $object->__getList($property["name"], $this->application);
                        foreach ($items as $item=> $text) {
                            $this->application->setHTMLArrayCustomTemplate($result, array(
                                "CATEGORY-VALUE"=>$item,
                                "CATEGORY-NAME"=>$this->lang($text),
                                "CATEGORY-SELECTED"=>($item == $value) ? "selected" : "",
                            ));
                            $this->application->parseCustomTemplate($result, "CATEGORIES");
                        }
                        return $this->application->renderCustomTemplate($result);
                    }
                case PropertyTypes::$_SHORTLIST : {
                        $result = $this->application->setCustomTemplate("bicubic", "shortlist");
                        $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-LABEL", $this->lang($property["lang"]));
                        $items = $object->__getList($property["name"], $this->application);
                        foreach ($items as $item=> $text) {
                            $this->application->setHTMLArrayCustomTemplate($result, array(
                                "OBJECT-NAME-PROPERTY-NAME"=>$objectName . "_" . $property["name"],
                                "OPTION-VALUE"=>$item,
                                "OPTION-NAME"=>$this->lang($text),
                                "OPTION-SELECTED"=>($item == $value) ? "checked" : "",
                                "OBJECT-NAME-PROPERTY-REQUIRED"=>$property["required"] ? "required" : "",
                            ));
                            $this->application->parseCustomTemplate($result, "CATEGORIES");
                        }
                        return $this->application->renderCustomTemplate($result);
                    }
            }
        }

        return "";
    }

    public function objectFormContent(DataObject $object) {
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        $formContent = "";
        foreach ($properties as $property) {
            $formContent .= $this->objectFormElement($object, $property);
        }
        return $formContent;
    }

    public function objectForm(DataObject $object, $callback) {
        $id = $this->application->getUrlParam($this->config("param_id"), PropertyTypes::$_INT);
        if ($id) {
            $data = new TransactionManager($this->application->data);
            $object->setId($id);
            $object = $data->getRecord($object);
            if (!$object) {
                $this->error('lang_notvalid');
            }
        }
        $this->application->setMainTemplate("bicubic", "form");
        $objectName = get_class($object);
        $this->application->setVariableTemplate("FORM-ID", $this->application->navigation . "$objectName");
        $this->application->setVariableTemplate("FORM-ACTION", $this->application->getAppUrl($this->application->name, $callback));
        $this->application->setVariableTemplate("FORM-CONTENT", $this->objectFormContent($object));
        $this->application->render();
    }

    public function objectFormSubmit(DataObject $object, $callback) {
        $object = $this->application->getFormObject($object);
        $data = new TransactionManager($this->application->data);
        $data->data->begin();
        if (!$object->getId()) {
            if (!$data->insertRecord($object)) {
                $data->data->rollback();
                $this->error('lang_errordatabase');
            }
        } else {
            if (!$data->updateRecord($object)) {
                $data->data->rollback();
                $this->error('lang_errordatabase');
            }
        }
        $data->data->commit();
        $this->application->redirectToUrl($this->application->getAppUrl($this->application->name, $callback));
    }

    public function objectTable(DataObject $object, $viewCallbak = null, $editCallbak = null, $deleteCallback = null) {
        $result = $this->application->setCustomTemplate("bicubic", "table");
        $this->application->setVariableCustomTemplate($result, "TABLE-CONTENT", $this->objectTableContent($object, $viewCallbak, $editCallbak, $deleteCallback));
        $content = $this->application->renderCustomTemplate($result);
        return $content;
    }

    public function objectTableContent(DataObject $object, $viewCallbak = null, $editCallbak = null, $deleteCallback = null) {
        $data = new AtomManager($this->application->data);
        $objects = $data->getAllPaged($object, "id", "DESC", 100, 0);
        $rowscontent = "";
        foreach ($objects as $object) {
            $rowscontent .= $this->objectTableRow($object, $viewCallbak, $editCallbak, $deleteCallback);
        }
        $content = $this->objectTableHeader($object) . $rowscontent;
        return $content;
    }

    public function objectTableHeader(DataObject $object) {
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        $headercontent = "";
        foreach ($properties as $property) {
            if ($property["hidden"] || !$property["serializable"]) {
                if ($property["name"] != "id") {
                    continue;
                }
            }
            $headercontent .= $this->objectTableHeaderValue($object, $property);
        }
        $headercontent .= $this->objectTableHeaderActions();

        $result = $this->application->setCustomTemplate("bicubic", "tabletr");
        $this->application->setVariableCustomTemplate($result, "TR-CONTENT", $headercontent);
        $content = $this->application->renderCustomTemplate($result);
        return $content;
    }

    public function objectTableHeaderValue(DataObject $object, $property) {
        $result = $this->application->setCustomTemplate("bicubic", "tableth");
        $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-HEADER", $this->lang($property["lang"]));
        $content = $this->application->renderCustomTemplate($result);
        return $content;
    }

    public function objectTableHeaderActions() {
        $result = $this->application->setCustomTemplate("bicubic", "tableth");
        $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-HEADER", $this->lang("lang_actions"));
        $content = $this->application->renderCustomTemplate($result);
        return $content;
    }

    public function objectTableRow(DataObject $object, $viewCallbak = null, $editCallbak = null, $deleteCallback = null) {
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        $rowcontent = "";
        foreach ($properties as $property) {
            if ($property["hidden"] || !$property["serializable"]) {
                if ($property["name"] != "id") {
                    continue;
                }
            }
            $rowcontent .= $this->objectTableRowValue($object, $property);
        }
        $rowcontent .= $this->objectTableRowActions($object, $viewCallbak, $editCallbak, $deleteCallback);

        $result = $this->application->setCustomTemplate("bicubic", "tabletr");
        $this->application->setVariableCustomTemplate($result, "TR-CONTENT", $rowcontent);
        $content = $this->application->renderCustomTemplate($result);
        return $content;
    }

    public function objectTableRowValue(DataObject $object, $property) {
        $result = $this->application->setCustomTemplate("bicubic", "tabletd");
        $this->application->setHTMLVariableCustomTemplate($result, "PROPERTY-VALUE", $this->application->formatProperty($object, $property));
        $content = $this->application->renderCustomTemplate($result);
        return $content;
    }

    public function objectTableRowActions(DataObject $object, $viewCallbak = null, $editCallbak = null, $deleteCallback = null) {
        $result = $this->application->setCustomTemplate("bicubic", "tableactions");
        if ($viewCallbak) {
            $this->application->setHTMLVariableCustomTemplate($result, 'LINK-ACTIONVIEW', $this->application->getAppUrl($this->application->name, $viewCallbak, [new Param("id", $object->getId())]));
            $this->application->parseCustomTemplate($result, "ACTIONVIEW");
        }
        if ($editCallbak) {
            $this->application->setHTMLVariableCustomTemplate($result, 'LINK-ACTIONEDIT', $this->application->getAppUrl($this->application->name, $editCallbak, [new Param("id", $object->getId())]));
            $this->application->parseCustomTemplate($result, "ACTIONEDIT");
        }
        if ($deleteCallback) {
            $this->application->setHTMLVariableCustomTemplate($result, 'LINK-ACTIONDELETE', $this->application->getAppUrl($this->application->name, $deleteCallback, [new Param("id", $object->getId())]));
            $this->application->parseCustomTemplate($result, "ACTIONDELETE");
        }
        $content = $this->application->renderCustomTemplate($result);
        return $content;
    }

}

?>
