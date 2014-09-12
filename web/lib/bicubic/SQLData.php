<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
abstract class SQLData extends Data {

	public $localError;
	protected $idname = "id";
	protected $connection;
	public $debug = false;

	public function insert(DataObject $object) {
		if (!$object->__isComplete()) {
			$this->localError = "incomplete object";
			return false;
		}
		if (!($object->__isChild() || $object->getId() == null)) {
			return false;
		}
		$class = strtolower(get_class($object));
		$table = ($class);
		$params = array();
		$query = "INSERT INTO " . $table . " (";
		$i = 0;
		$j = 0;
		$properties = $object->__getProperties();
		foreach ($properties as $property) {
			if (!$property["serializable"]) {
				continue;
			}
			$key = $property["name"];
			$cammel = strtoupper(substr($key, 0, 1)) . substr($key, 1);
			$getter = "get$cammel";
			$value = $object->$getter();
			if (isset($value) && ($object->__isChild() || $key != $this->idname)) {
				if ($i == 0) {
					$query .= "$key";
					$i++;
				} else {
					$query .= ",$key";
				}
				$params[$j] = $value;
				$j++;
			}
		}
		$query .= ") VALUES (";
		$i = 0;
		foreach ($params as $key=> $value) {
			$value = $this->escapeChars($value);
			if ($i == 0) {
				$query .= "'$value'";
				$i++;
			} else {
				$query .= ",'$value'";
			}
		}
		$query .= ")";
		if (!$this->performWrite($query)) {
			return false;
		}
		$id = 0;
		if (!$object->__isChild()) {
			$id = $this->lastInsertId($table);
		} else {
			$getter = "get$this->idname";
			$id = $object->$getter();
		}
		return $id;
	}

	public function select(DataObject $object, OrderParam $orderParam = null, $limit = null, $lastIndex = null) {
		$class = strtolower(get_class($object));
		$data = array();
		$query = "SELECT * FROM  " . ($class) . " ";
		$i = 0;
		$properties = $object->__getProperties();
		foreach ($properties as $property) {
			if (!$property["serializable"]) {
				continue;
			}
			$key = $property["name"];
			$cammel = strtoupper(substr($key, 0, 1)) . substr($key, 1);
			$getter = "get$cammel";
			$value = $object->$getter();
			if (isset($value)) {
				$value = $this->escapeChars($value);
				if ($i == 0) {
					$query .= "WHERE  ";
				} else {
					$query .= "AND  ";
				}
				switch ($property["type"]) {
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
							$query .= " $key ~* '.*$value.*' ";
							break;
						}
					case PropertyTypes::$_ALPHANUMERIC :
					case PropertyTypes::$_DOUBLE :
					case PropertyTypes::$_URL :
					case PropertyTypes::$_EMAIL :
					case PropertyTypes::$_RUT :
					case PropertyTypes::$_INT :
					case PropertyTypes::$_LETTERS :
					case PropertyTypes::$_LONG :
					case PropertyTypes::$_PASSWORD :
					case PropertyTypes::$_FILE :
					case PropertyTypes::$_IMAGE256 :
					case PropertyTypes::$_IMAGE512 :
					case PropertyTypes::$_IMAGE1024 :
					case PropertyTypes::$_DATE :
					case PropertyTypes::$_TIME :
					case PropertyTypes::$_BOOLEAN :
					case PropertyTypes::$_LIST :
					case PropertyTypes::$_STRINGLIST :
					case PropertyTypes::$_SHORTLIST : {
							$query .= " $key = '$value' ";
							break;
						}
				}
				$i++;
			}
		}

		$lastIndex = $this->escapeChars($lastIndex);
		if (!$orderParam) {
			$orderParam = new OrderParam("id", ObjectOrder::$_DESC);
		}
		if (!$lastIndex) {
			$lastIndex = 0;
		}

		$query .= "ORDER BY $orderParam->property  " . ObjectOrder::$_VALUE[$orderParam->order] . " ";
		$query .= "OFFSET $lastIndex ";

		if ($limit) {
			if ($limit < 0) {
				$limit = 0;
			}
			$query .= "LIMIT $limit ";
		}
		$result = $this->performRead($query);
		while ($row = $this->readNext($result)) {
			$object = new $class();
			$object->fillFromDB($row);
			$data[$object->getId()] = $object;
		}
		$this->freeMemory($result);
		return $data;
	}

	public function selectOne(DataObject $object) {
		$class = strtolower(get_class($object));
		$query = "SELECT * FROM  " . ($class) . " ";
		$i = 0;
		$properties = $object->__getProperties();
		foreach ($properties as $property) {
			if (!$property["serializable"]) {
				continue;
			}
			$key = $property["name"];
			$cammel = strtoupper(substr($key, 0, 1)) . substr($key, 1);
			$getter = "get$cammel";
			$value = $object->$getter();
			if (isset($value)) {
				$value = $this->escapeChars($value);
				if ($i == 0) {
					$query .= "WHERE $key = '$value' ";
				} else {
					$query .= "AND $key = '$value' ";
				}
				$i++;
			}
		}
		$query .= " ORDER BY id DESC ";
		$query .= " LIMIT 1 ";
		$result = $this->performRead($query);
		$row = $this->readNext($result);
		$object = null;
		if ($row) {
			$object = new $class();
			$object->fillFromDB($row);
		}
		$this->freeMemory($result);
		return $object;
	}

	public function update(DataObject $object) {
		if ($object->getId() == null) {
			return false;
		}
		$class = strtolower(get_class($object));
		$table = ($class);
		$query = "UPDATE " . $table . " ";
		$i = 0;
		$properties = $object->__getProperties();
		foreach ($properties as $property) {
			if (!$property["serializable"]) {
				continue;
			}
			$key = $property["name"];
			$cammel = strtoupper(substr($key, 0, 1)) . substr($key, 1);
			$getter = "get$cammel";
			$value = $object->$getter();
			if ($key != $this->idname) {
				if (isset($value)) {
					$value = $this->escapeChars($value);
					if ($i == 0) {
						$query .= "SET $key='$value' ";
						$i++;
					} else {
						$query .= ", $key='$value' ";
					}
				} else {
					if ($property["updatenull"]) {
						if ($i == 0) {
							$query .= "SET $key=NULL ";
							$i++;
						} else {
							$query .= ", $key=NULL ";
						}
					}
				}
			}
		}
		$idoftheobject = $this->escapeChars($object->getId());
		$query .= "WHERE $this->idname = '" . $idoftheobject . "'";
		if (!$this->performWrite($query)) {
			return false;
		}
		return true;
	}

	public function delete(DataObject $object) {
		if ($object->getId() == null) {
			return false;
		}
		$class = strtolower(get_class($object));
		$idoftheobject = $this->escapeChars($object->getId());
		$query = "DELETE FROM $class WHERE $this->idname = '" . $idoftheobject . "'";
		if (!$this->performWrite($query)) {
			return false;
		}
		return true;
	}

}
