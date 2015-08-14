<?php

/*
 * Copyright (C)  Juan Francisco Rodríguez
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
