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

abstract class DataObject {

    public abstract function __getProperties();

    public function __isComplete() {
	$properties = $this->__getProperties();
	foreach ($properties as $key => $property) {
	    if ($property["required"]) {
		$getter = "get$key";
		$setter = "set$key";
		$value = $this->$getter();
		if ($value === null) {
		    if ($property["default"] !== null) {
			$this->$setter($property["default"]);
		    } else {
			return false;
		    }
		}
	    }
	}
	return true;
    }

    public function __isChild() {
	return false;
    }

    public function __getList($paramname, Application $application = null) {
	return [];
    }

    public function __getProperty($key) {
	$properties = $this->__getProperties();
	if (array_key_exists($key, $properties)) {
	    return $properties[$key];
	}
	return null;
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
	    foreach ($properties as $key => $property) {
		$setter = "set$key";
		$getter = "get$key";
		$parentObject->$setter($this->$getter());
	    }
	    return $parentObject;
	}
	return null;
    }

    public function __isEmpty() {
	$empty = true;
	$properties = $this->__getProperties();
	foreach ($properties as $key => $property) {
	    $getter = "get$key";
	    $value = $this->$getter();
	    if (isset($value)) {
		$empty = false;
		break;
	    }
	}
	return $empty;
    }

    public function fillFromDB(array $row) {
	$class = strtolower(get_class($this));
	$properties = $this->__getProperties();
	$this->setObjectProperties($properties, $class, $row);
	if ($this->__isChild()) {
	    $parantClass = strtolower(get_parent_class($this));
	    $parentProperties = $this->__getParentProperties();
	    $this->setObjectProperties($parentProperties, $parantClass, $row);
	}
    }

    private function setObjectProperties($properties, $classname, $row) {
	foreach ($properties as $key => $property) {
	    $setter = "set$key";
	    $dbprop1 = $property["name"];
	    $dbprop2 = $classname . $property["name"];
	    $dbprop3 = $property["name"] . "id";
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
	foreach ($properties as $key => $property) {
	    $setter = "set$key";
	    $dbprop1 = $property["name"];
	    $dbprop2 = $class . $property["name"];
	    $dbprop3 = $property["name"] . "id";
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
