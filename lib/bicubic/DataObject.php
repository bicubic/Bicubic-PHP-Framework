<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
abstract class DataObject {

    /**
     * Propiedades de BD
     * @return array las propiedades a serializar en la BD
     */
    public abstract function __getProperties();

    public function __isComplete() {
        $class = strtolower(get_class($this));
        $properties = $this->__getProperties();
        foreach ($properties as $property) {
            if ($property["required"]) {
                $pname = $property["name"];
                $getter = "get$pname";
                $value = $this->$getter();
                if($value === null) {
                    return false;
                }
            }
        }
        return true;
    }

    public function __isChild() {
        return false;
    }

    public function __getParentProperties() {
        if ($this->__isChild()) {
            return $this->__getParentObject()->__getProperties();
        }
        return null;
    }

    public function __getParentObject() {
        if ($this->__isChild()) {
            $parentClassName = get_parent_class($this);
            $parentObject = new $parentClassName();
            $properties = $parentObject->__getProperties();
            foreach ($properties as $property) {
                $pname = $property["name"];
                $setter = "set$pname";
                $getter = "get$pname";
                $parentObject->$setter($this->$getter());
            }
            return $parentObject;
        }
        return null;
    }

    public function fillFromDB(array $row) {
        $class = strtolower(get_class($this));
        $properties = $this->__getProperties();
        foreach ($properties as $property) {
            $key = $property["name"];
            $cammel = strtoupper(substr($key, 0, 1)) . substr($key, 1);
            $setter = "set$cammel";
            $dbprop1 = $key;
            $dbprop2 = $class . $key;
            $dbprop3 = $key . "id";
            if (array_key_exists($dbprop1, $row)) {
                $this->$setter($row[$dbprop1]);
            } else if (array_key_exists($dbprop2, $row)) {
                $this->$setter($row[$dbprop2]);
            } else if (array_key_exists($dbprop3, $row)) {
                $this->$setter($row[$dbprop3]);
            }
        }
    }

    public function fillFromJson($row) {
        $class = strtolower(get_class($this));
        $properties = $this->__getProperties();
        foreach ($properties as $property) {
            $key = $property["name"];
            $cammel = strtoupper(substr($key, 0, 1)) . substr($key, 1);
            $setter = "set$cammel";
            $dbprop1 = $key;
            $dbprop2 = $class . $key;
            $dbprop3 = $key . "id";
            if (property_exists($row, $dbprop1)) {
                $this->$setter($row->$dbprop1);
            } else if (property_exists($row, $dbprop2)) {
                $this->$setter($row->$dbprop2);
            } else if (property_exists($row, $dbprop3)) {
                $this->$setter($row->$dbprop3);
            }
        }
    }

}

?>