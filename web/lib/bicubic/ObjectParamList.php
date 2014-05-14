<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class ObjectParamList {

    private $dataobject;
    private $values;
    private $selected;

    function __construct(DataObject $dataobject, $values, $selected = array()) {
        $this->dataobject = $dataobject;
        $this->values = $values;
        $this->selected = $selected;
    }

    function getValues() {
        return $this->values;
    }

    function setValues($value) {
        $this->values = $value;
    }

    function getDataObject() {
        return $this->dataobject;
    }

    function setDataObject($value) {
        $this->dataobject = $value;
    }

    public function getSelected() {
        return $this->selected;
    }

    public function setSelected($selected) {
        $this->selected = $selected;
    }

}

?>