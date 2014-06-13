<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class TransactionManager {

    public $data;
    public $error;
    public $totalrows;

    function __construct(Data $data) {
        $this->data = $data;
    }

    public function getAll(DataObject $dataObject, $orderIndex = "id", $orderDirection = "DESC") {
        $data = $this->data->select($dataObject, $orderIndex, $orderDirection);
        return $data;
    }

    public function getAllPaged(DataObject $dataObject, $orderIndex = "id", $orderDirection = "DESC", $itemsPerPage = null, $lastIndex = PHP_INT_MAX, $keyword = null, $keywordfield = null) {
        $data = $this->data->select($dataObject, $orderIndex, $orderDirection, $itemsPerPage, $lastIndex, $keyword, $keywordfield);
        return $data;
    }

    public function getRecord(DataObject $dataObject, $checkempty = true) {
        $properties = $dataObject->__getProperties();
        $empty = true;
        foreach ($properties as $property) {
            $cammel = strtoupper(substr($property["name"], 0, 1)) . substr($property["name"], 1);
            $getter = "get$cammel";
            $value = $dataObject->$getter();
            if ($value) {
                $empty = false;
                break;
            }
        }
        if ($checkempty && $empty) {
            return null;
        }
        $dataObject = $this->data->selectOne($dataObject);
        if (!$dataObject || !$dataObject->getId()) {
            return null;
        }
        if ($dataObject->__isChild()) {
            $parent = $this->data->selectOne($dataObject->__getParentObject());
            if (!$parent->getId()) {
                return null;
            }
            $properties = $parent->__getProperties();
            foreach ($properties as $property) {
                $cammel = strtoupper(substr($property["name"], 0, 1)) . substr($property["name"], 1);
                $getter = "get$cammel";
                $setter = "set$cammel";
                $value = $dataObject->$getter();
                $dataObject->$setter($parent->$getter());
            }
        }
        return $dataObject;
    }

    public function insertRecord(DataObject $dataObject) {
        if (!$dataObject->__isChild() && $dataObject->getId()) {
            $this->error = "id not null";
            return false;
        }
        if ($dataObject->__isChild()) {
            $parent = $dataObject->__getParentObject();
            $id = $this->data->insert($parent);
            if (!$id) {
                $this->error = $this->data->getError();
                return false;
            }
            $dataObject->setId($id);
        }
        $id = $this->data->insert($dataObject);
        if (!$id) {
            $this->error = $this->data->getError();
            return false;
        }
        return $id;
    }

    public function updateRecord(DataObject $dataObject) {
        if (!$dataObject->getId()) {
            $this->error = "null id";
            return false;
        }
        if ($dataObject->__isChild()) {
            $parent = $dataObject->__getParentObject();
            if (!$this->data->update($parent)) {
                $this->error = $this->data->getError();
                return false;
            }
        }
        if (!$this->data->update($dataObject)) {
            $this->error = $this->data->getError();
            return false;
        }
        return true;
    }

    function deleteRecord(DataObject $dataObject) {
        if (!$dataObject->getId()) {
            $this->error = "null id";
            return false;
        }
        if (!$this->data->delete($dataObject)) {
            $this->error = $this->data->getError();
            return false;
        }
        if ($dataObject->__isChild()) {
            $parent = $dataObject->__getParentObject();
            if (!$this->data->delete($parent)) {
                $this->error = $this->data->getError();
                return false;
            }
        }
        return true;
    }

    public function executeCustomWriteSQL($sql) {
        if (!$this->data->performWrite($sql)) {
            $this->error = $this->data->getError();
            return false;
        }
        return true;
    }
    
    public function executeCustomReadSQL($sql) {
        $result = $this->data->performRead($sql);
        return $result;
    }

}

?>
