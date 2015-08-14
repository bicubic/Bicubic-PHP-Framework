<?php

/*
 * The MIT License
 *
 * Copyright 2015 Juan Francisco Rodríguez.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class TransactionManager {

    public $data;
    public $error;
    public $totalrows;

    function __construct(Data $data) {
	$this->data = $data;
    }

    public function getAll(DataObject $dataObject, OrderParam $orderParam = null) {
	$data = $this->data->select($dataObject, $orderParam);
	return $data;
    }

    public function getAllPaged(DataObject $dataObject, OrderParam $orderParam = null, $itemsPerPage = null, $lastIndex = null) {
	$data = $this->data->select($dataObject, $orderParam, $itemsPerPage, $lastIndex);
	return $data;
    }

    public function getRecord(DataObject $dataObject, $checkempty = true) {
	$properties = $dataObject->__getProperties();
	$empty = true;
	foreach ($properties as $key => $property) {
	    if (!$property["serializable"]) {
		continue;
	    }
	    $getter = "get$key";
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
	    foreach ($properties as $key => $property) {
		if (!$property["serializable"]) {
		    continue;
		}
		$getter = "get$key";
		$setter = "set$key";
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

    //$query = "INSERT INTO $tableName (getPropertiesInsert) "
    protected function getPropertiesInsert(DataObject $object) {
	$objectProperties = $object->__getProperties();
	foreach ($objectProperties as $property) {
	    $propertyName = $property['name'];
	    $queryArray[] = "$propertyName";
	}
	$array = implode(", ", $queryArray);
	return $array;
    }

    protected function getPropertiesSet(DataObject $object, array $properties) {
	$objectProperties = $object->__getProperties();
	foreach ($properties as $property) {
	    $propertyName = $objectProperties[$property]["name"];
	    $getter = "get$propertyName";
	    $value = $this->data->escapeChars($object->$getter());
	    if ($object->$getter() === null) {
		$queryArray[] = "$propertyName= null ";
	    } else {
		$queryArray[] = "$propertyName='$value' ";
	    }
	}
	$select = implode(", ", $queryArray);
	return $select;
    }

    //SELECT getPropertiesSelect
    protected function getPropertiesSelect(DataObject $object, $properties) {
	$className = strtolower(get_class($object));
	$objectProperties = $object->__getProperties();
	foreach ($properties as $property) {
	    $propertyName = $objectProperties[$property]["name"];
	    $queryArray[] = "$className.$propertyName as " . $className . $propertyName;
	}
	$select = implode(", ", $queryArray);
	return $select;
    }

    function updateTable(DataObject $object, array $properties) {
	$setProperties = $this->getPropertiesSet($object, $properties);
	$tableName = strtolower(get_class($object));
	$objectId = $object->getId();
	$query = "UPDATE $tableName "
		. "SET $setProperties "
		. "WHERE id='$objectId'";
	return $this->data->performWrite($query);
    }

}
