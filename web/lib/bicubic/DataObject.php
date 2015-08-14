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
