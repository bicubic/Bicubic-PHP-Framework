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
    private $tableLastId;
    private $tableMaxSize;
    private $tableSize;

    function __construct(Application $application) {
        $this->application = $application;
        $this->tableLastId = PHP_INT_MAX;
        $this->tableMaxSize = $this->config('web_table_size');
        $this->tableSize = 0;
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

    public function objectForm(DataObject $object, $callBackNav) {
        $result = $this->application->setCustomTemplate("bicubic", "form");
        $this->application->setVariableCustomTemplate($result, "FORM-ID", $this->application->navigation . get_class($object));
        $this->application->setVariableCustomTemplate($result, "FORM-ACTION", $this->application->getAppUrl($this->application->name, $callBackNav));
        $this->application->setVariableCustomTemplate($result, "FORM-CONTENT", $this->objectFormContent($object));
        $content = $this->application->renderCustomTemplate($result);
        return $content;
    }

    public function objectTable(DataObject $object, $callBackParams = array(), $featureParams = array(), $loadMoreCallBackUrl = null) {
        $result = $this->application->setCustomTemplate("bicubic", "table");
        foreach ($featureParams as $featureParam) {
            if (is_a($featureParam, "LinkParam")) {
                $this->application->setHTMLArrayCustomTemplate($result, [
                    'FEATURELINK-LINK'=>$this->application->getAppUrl($this->application->name, $featureParam->name),
                    'FEATURELINK-NAME'=>$this->lang($featureParam->value),
                ]);
                $this->application->parseCustomTemplate($result, "FEATURELINK");
            } else if (is_a($featureParam, "ImageParam")) {
                $this->application->setHTMLArrayCustomTemplate($result, [
                    'FEATUREIMAGE-LINK'=>$this->application->getAppUrl($this->application->name, $featureParam->name),
                    'FEATUREIMAGE-SRC'=>$this->photoUrl($featureParam->value),
                ]);
                $this->application->parseCustomTemplate($result, "FEATUREIMAGE");
            }
        }
        if ($loadMoreCallBackUrl) {
            $this->application->setVariableCustomTemplate($result, "TABLE-CONTENT", $this->objectTableHeader($object, $callBackParams) . $this->objectTableContentMore($object, $callBackParams));
            $this->application->setVariableCustomTemplate($result, "TABLE-LASTID", $this->tableLastId);
            $this->application->setVariableCustomTemplate($result, "TABLE-SIZE", $this->tableSize);
            $this->application->setVariableCustomTemplate($result, "TABLE-MAXSIZE", $this->tableMaxSize);
            $this->application->setVariableCustomTemplate($result, "TABLE-URL", $loadMoreCallBackUrl);
        } else {
            $this->application->setVariableCustomTemplate($result, "TABLE-CONTENT", $this->objectTableContent($object, $callBackParams));
        }

        $content = $this->application->renderCustomTemplate($result);
        return $content;
    }

    public function objectTableJson(DataObject $object, $callBackParams) {
        $lastid = $this->application->getFormParam("lastid", PropertyTypes::$_INT);
        $json = new SuccessJson();
        $json->data = $this->objectTableContentMore($object, $callBackParams, $lastid);
        $json->lastid = $this->tableLastId;
        $json->size = $this->tableSize;
        $this->application->renderToJson($json);
    }

    public function objectTableContent(DataObject $object, $callBackParams = array()) {
        $content = $this->objectTableHeader($object, $callBackParams) . $this->objectTableRows($object, $callBackParams);
        return $content;
    }

    public function objectTableContentMore(DataObject $object, $callBackParams = array(), $lastid = PHP_INT_MAX) {
        $content = $this->objectTableRows($object, $callBackParams, $this->tableMaxSize, $lastid);
        return $content;
    }

    public function objectTableRows(DataObject $object, $callBackParams = array(), $size = PHP_INT_MAX, $lastid = PHP_INT_MAX) {
        $data = new AtomManager($this->application->data);
        $objects = $data->getAllPaged($object, "id", "DESC", $size, $lastid);
        $rowscontent = "";
        foreach ($objects as $object) {
            $this->tableLastId = $object->getId();
            $this->tableSize++;
            $rowscontent .= $this->objectTableRow($object, $callBackParams);
        }
        return $rowscontent;
    }

    public function objectTableHeader(DataObject $object, $callBackParams = array()) {
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        $headercontent = "";
        foreach ($properties as $property) {
            if (!$property["table"]) {
                continue;
            }
            $headercontent .= $this->objectTableHeaderValue($object, $property);
        }
        if ($callBackParams) {
            $headercontent .= $this->objectTableHeaderActions();
        }


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

    public function objectTableRow(DataObject $object, $callBackParams = array()) {
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        $rowcontent = "";
        foreach ($properties as $property) {
            if (!$property["table"]) {
                continue;
            }
            $rowcontent .= $this->objectTableRowValue($object, $property);
        }
        if ($callBackParams) {
            $rowcontent .= $this->objectTableRowActions($object, $callBackParams);
        }


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

    public function objectTableRowActions(DataObject $object, $callBackParams = array()) {
        $result = $this->application->setCustomTemplate("bicubic", "tableactions");
        foreach ($callBackParams as $callBackParam) {
            if (is_a($callBackParam, "LinkParam")) {
                $this->application->setHTMLArrayCustomTemplate($result, [
                    'ACTIONLINK-LINK'=>$this->application->getAppUrl($this->application->name, $callBackParam->name, [new Param("id", $object->getId())]),
                    'ACTIONLINK-NAME'=>$this->lang($callBackParam->value),
                ]);
                $this->application->parseCustomTemplate($result, "ACTIONLINK");
            } else if (is_a($callBackParam, "ImageParam")) {
                $this->application->setHTMLArrayCustomTemplate($result, [
                    'ACTIONIMAGE-LINK'=>$this->application->getAppUrl($this->application->name, $callBackParam->name, [new Param("id", $object->getId())]),
                    'ACTIONIMAGE-SRC'=>$this->photoUrl($callBackParam->value),
                ]);
                $this->application->parseCustomTemplate($result, "ACTIONIMAGE");
            }
        }
        $content = $this->application->renderCustomTemplate($result);
        return $content;
    }

    public function objectExport(DataObject $object) {
        require_once "lib/ext/PHPExcel/PHPExcel.php";
        $excel = new PHPExcel();
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize '=>'512MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        $excel->setActiveSheetIndex(0);
        $properties = $object->__getProperties();
        if ($object->__isChild()) {
            $properties = array_merge($properties, $object->__getParentProperties());
        }
        $column = "A";
        $rowNumber = 1;
        foreach ($properties as $property) {
            if (!$property["table"]) {
                continue;
            }
            $excel->getActiveSheet()->setCellValue($column . $rowNumber, $this->lang($property["lang"]));
            $column++;
        }
        $data = new AtomManager($this->application->data);
        $lastid = 0;
        $items = 100;
        $objects = $data->getAllPaged($object, "id", "ASC", $items, $lastid);
        $rowNumber = 2;
        while ($objects) {
            foreach ($objects as $object) {
                $lastid = $object->getId();
                $column = "A";
                foreach ($properties as $property) {
                    if (!$property["table"]) {
                        continue;
                    }
                    $excel->getActiveSheet()->setCellValue($column . $rowNumber, $this->application->formatProperty($object, $property));
                    $column++;
                }
                $rowNumber++;
            }
            $objects = $data->getAllPaged($object, "id", "ASC", $items, $lastid);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function objectImport(DataObject $object, $callBack) {
        
    }

    public function objectImportSubmit(DataObject $object, $callBack) {
        
    }

}

?>
