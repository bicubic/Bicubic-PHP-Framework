<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
abstract class SQLData extends Data {

    public $totalRows;
    
    //Perform a data insert process
    //@param $object the object data to insert
    //@returns the id inserted if the operation was succesfully, false if not
    public function insert(DataObject $object, $idname = "id"){
        $class = get_class($object);
        $table = ($class);
        $params = array();
        $getters = array();
        //query preparation
        $query = "INSERT INTO \"" . $table . "\" (";
        $i = 0;
        $j = 0;
        $a = 0;
        $properties = $object->__getProperties();
        foreach ($properties as $property) {
            $key = $property;
            $obj = strpos($key, "_object");
            $arr = strpos($key, "_array");
            if(!$arr){
                if ($obj > 0) {
                    $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                    $getter = "get$cammel";
                    $setter = "set$cammel";
                    $value = $object->$getter();
                    
                    if ($value->getId() != null) {
                        if ($i == 0) {
                            $query .= "\"" .substr($key, 0, $obj) . "\" ";
                            $i++;
                        } else {
                            $query .= ", \"" . substr($key, 0, $obj) . "\" ";
                        }
                        
                        $params[substr($key, 0, $obj)] = $value->getId();
                        
                    } else {
                        $l = 0;
                        $properties1 = $value->__getProperties();
                        foreach ($properties1 as $property1){
                            $key1 = $property1;
                            $obj1 = strpos($key1, "_object");
                            $arr1 = strpos($key1, "_array");
                            if ($obj1 == 0 && $arr1 == 0){
                                $cammel = strtoupper(substr($property1, 0, 1)) . substr($property1, 1);
                                $getter = "get$cammel";
                                $setter = "set$cammel";
                                if ($value->$getter() != null){
                                    $l++;
                                }
                            }
                        }
                        if($l != 0){
                            $value->setId($this->insert($value));
                            if($value->getId() != -1){
                                if ($i == 0) {
                                    $query .= "\"" .substr($key, 0, $obj) . "\" ";
                                    $i++;
                                } else {
                                    $query .= ", \"" . substr($key, 0, $obj) . "\" ";
                                }
                                $params[substr($key, 0, $obj)] = $value->getId();
                            }
                        }
                    } 
                } else {
                    $cammel = strtoupper(substr($property, 0, 1)) . substr($property, 1);
                    $getter = "get$cammel";
                    $setter = "set$cammel";
                    $value = $object->$getter();
                    if($object->getId() == null){
                        if ($value != null && $key != $idname ) {
                            if ($i == 0) {
                                $query .= "\"$key\"";
                                $i++;
                            } else {
                                $query .= ",\"$key\"";
                            }
                            $params[$j] = $value;
                            $j++;
                        }
                    } else {
                        return false;
                    }
                }
            } else {
                $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $arr)  ;
                $getters[$a] = $cammel;
                $a++;
            }
        }
        $query .= ") VALUES (";
        $i = 0;
        foreach ($params as $key => $value) {
            $value = $this->escapeChars($value);
            if ($i == 0) {
                $query .= "'$value'";
                $i++;
            } else {
                $query .= ",'$value'";
            }
        }
        $query .= ")";
        //echo "$query";
        //query execution
        if (!$this->performWrite($query)) {
            return false;
        }
        $id = $this->lastInsertId($table);
        
        if(count($getters) > 0){
            foreach ($getters as $arrayName){
                $getter = "get$arrayName";
                $objects = $object->$getter();
                if(count($objects) > 0){
                    foreach ($objects as $subObject){
                        $setter = "set$arrayName" . $class;
                        $subObject->$setter($id);
                        $this->insert($subObject);
                    }
                }
            }
        }
        
        return $id;
    }
    
    //Performs a data selection process
    //@param $object the target object that filters the data
    //@returns an array containing objects of the type of $object
    public function select(DataObject $object, $orderIndex = null, $orderDirection = null, $limit=null, $indexStart=null, $arrays=false) {
        $class = get_class($object);
        //query preparation
        $data = array();
        $query = "SELECT * FROM  \"" . ($class) . "\" ";
        $countQuery = "SELECT count(id) as total FROM \"" . ($class) . "\" ";
        $i = 0;
        $properties = $object->__getProperties();
        foreach ($properties as $property) {
            $key = $property;
            $obj = strpos($key, "_object");
            $arr = strpos($key, "_array");
            if (!$arr){
                if ($obj > 0) {
                    $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                    $getter = "get$cammel";
                    $setter = "set$cammel";
                    $value = $object->$getter();
                    if ($value->getId() != null) {
                        $value->setId($this->escapeChars($value->getId()));
                        if ($i == 0) {
                            $query .= "WHERE \"$key\" = '" . $value->getId() . "' ";
                            $countQuery .= "WHERE \"$key\" = '" . $value->getId() . "' ";
                        } else {
                            $query .= "AND \"$key\" = '" . $value->getId() . "' ";
                            $countQuery .= "AND \"$key\" = '" . $value->getId() . "' ";
                        }
                        $i++;
                    }
                } else {
                    $cammel = strtoupper(substr($property, 0, 1)) . substr($property, 1);
                    $getter = "get$cammel";
                    $setter = "set$cammel";
                    $value = $object->$getter();
                    if ($value != null) {
                        $value = $this->escapeChars($value);
                        if ($i == 0) {
                            $query .= "WHERE \"$key\" = '$value' ";
                            $countQuery .= "WHERE \"$key\" = '$value' ";
                        } else {
                            $query .= "AND \"$key\" = '$value' ";
                            $countQuery .= "AND \"$key\" = '$value' ";
                        }
                        $i++;
                    }
                }
            }
        }
        if (isset($orderIndex) && isset($orderDirection)) {
            $query .= "ORDER BY \"$orderIndex\" $orderDirection ";
        } else {
            $query .= "ORDER BY \"id\" ASC ";
        }
        if (isset($limit) && isset($indexStart)) {
            
            if ($limit < 0) {
                $limit = 0;
            }
            if ($indexStart < 0) {
                $indexStart = 0;
            }
            $query .= "LIMIT $limit OFFSET $indexStart";
        }
        //query execution
        //echo "$query<br />";
        $countresult = $this->performRead($countQuery);
        $countrow = $this->readNext($countresult);
        $this->totalRows = $countrow['total'];
        $result = $this->performRead($query);
        
        while ($row = $this->readNext($result)) {
            $object = new $class();
            $properties = $object->__getProperties();
            foreach ($properties as $property) {
                $key = $property;
                $obj = strpos($key, "_object");
                $arr = strpos($key, "_array");
                if(!$arr){
                    if ($obj > 0) {
                        $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                        $getter = "get$cammel";
                        $setter = "set$cammel";
                        if (isset($row[substr($key, 0, $obj) . "Id"])) {
                            $subObject = $object->$getter();
                            $subObject->setId($row[substr($key, 0, $obj) . "Id"]);
                        }
                    } else {
                        $cammel = strtoupper(substr($property, 0, 1)) . substr($property, 1);
                        $getter = "get$cammel";
                        $setter = "set$cammel";
                        if (isset($row[$key])) {
                            $object->$setter($row[$key]);
                        }
                    }
                } 
            }
            $data[$object->getId()] = $object;
        }
        
        if($arrays != false){
        foreach ($data as $arrobject){
            $arrproperties = $arrobject->__getProperties();
            foreach($arrproperties as $arrproperty){
                $key = $arrproperty;
                $arr = strpos($key, "_array");
                if($arr > 0){
                    $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $arr)  ;
                    $arr += 7;
                    $class = substr(substr($property, 0, 1) . substr($property, 1), $arr, ($arr . 10));
                    $getter = "get$cammel";
                    $setter = "set$cammel";
                    $subgetter = "get$cammel" . get_class($arrobject);
                    $subsetter = "set$cammel" . get_class($arrobject);
                    $subObject = new $class();
                    $subObject->$subsetter($arrobject->getId());
                    $subdata1 = $this->select($subObject, "id", "ASC");
                    $arrobject->$setter($subdata1);
                }
            }
        }
        }
        $this->freeMemory($result);
        return $data;
    }
    
    //Performs a data unique selection process
    //@param $object the target object to copy the data
    //@returns a new object of the type of $object
    public function selectOne(DataObject $object, $orderIndex = null, $orderDirection = null) {
        $class = get_class($object);
        $properties = $object->__getProperties();
        //query preparation
        $queryPrep = $this->getQuery($object);
        $query = "SELECT " . ($queryPrep[0]) . " FROM " . ($queryPrep[1]) . " WHERE " . ($queryPrep[2]) . ($queryPrep[4]);
        if (isset($orderIndex) && isset($orderDirection)) {
            $query .= "ORDER BY A.'$orderIndex' $orderDirection ";
        } else {
            $query .= "ORDER BY A.\"id\" DESC ";
        }
        
        //query execution
        //echo "$query<br />";
        $result = $this->performRead($query);
        $row = $this->readNext($result);
        $object2 = $this->fillObject($object, $row);
        $this->freeMemory($result);
        return $object2[0];
    }
    
   
    private function getQuery(DataObject $object, $j = 0, $l = "A", $a = 0){
        $properties = $object->__getProperties();
        $query = array();
        $query[0] = "";
        $query[1] = "";
        $query[2] = "";
        $query[4] = "";
        foreach ($properties as $property) {
            $key = $property;
            $obj = strpos($key, "_object");
            $arr = strpos($key, "_array");
            if($arr == 0){
                if($obj > 0) {
                   $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                   $getter = "get$cammel";
                   $setter = "set$cammel";
                   $value = $object->$getter();
                   $key = substr(substr($property, 0, 1) . substr($property, 1), 0, $obj);
                   if ($j == 0) {
                        $query[0] .= "$l.\"$key\" as \"" . "$l.$key\"";
                        if ($value->getId() != null) {
                            $value->setId($this->escapeChars($value->getId()));
                            $query[4] .= "AND $l.\"$key\" = '" . $value->getId() . "' ";
                        }
                    } else {
                        $query[0] .= ", $l.\"$key\" as \"" . "$l.$key\"";
                        if ($value->getId() != null) {
                            $value->setId($this->escapeChars($value->getId()));
                            $query[4] .= "AND $l.\"$key\" = '" . $value->getId() . "' ";
                        }
                    }
                    $j++;
               } else {
                   $cammel = strtoupper(substr($property, 0, 1)) . substr($property, 1);
                   $getter = "get$cammel";
                   $setter = "set$cammel";
                   $value = $object->$getter();
                   if($j == 0) {
                      $query[0] .= "$l.\"$key\" as \"". "$l.$key\" ";
                      if($value != null){
                         $value = $this->escapeChars($value);
                         $query[4] .= " $l.\"$key\" = '$value' ";
                      }
                   }else{
                       $query[0] .= ", $l.\"$key\" as \"". "$l.$key\" ";
                       if($value != null){
                          $value = $this->escapeChars($value);
                          $query[4] .= "AND $l.\"$key\" = '$value' ";
                       }
                   }
                   $j++;
                }
            }
        }
        if($l == "A") {
            $class = get_class($object);    
            $query[1] .= " \"$class\" $l";
            $i = $l;
            $l++;
            $e = $l;
            foreach ($properties as $property) {
                $key = $property;
                $obj = strpos($key, "_object");
                if ($obj > 0) {
                    $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                    $getter = "get$cammel";
                    $setter = "set$cammel";
                    $value = $object->$getter();
                    $class = substr(strtolower(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                    if($a == 0){
                        $query[2] .= " $i.\"$class\" = $e.id ";
                        $aux = $this->getQuery($value, $j, $e, $a);
                        $query[0] .= $aux[0];
                        $query[1] .= $aux[1];
                        $query[2] .= $aux[2];
                        $query[4] .= $aux[4];
                        $e = $aux[3];
                        $a++;

                    }else{
                        $query[2] .= "AND $i.\"$class\" = $e.id ";
                        $aux = $this->getQuery($value, $j, $e, $a);
                        $query[0] .= $aux[0];
                        $query[1] .= $aux[1];
                        $query[2] .= $aux[2];
                        $query[4] .= $aux[4];
                        $e = $aux[3];
                        $a++;
                    } 
                }
            }
        }else{
            $class = get_class($object);    
            $query[1] .= ", \"$class\" $l";
            $i = $l;
            $l++;
            $e = $l;
            foreach ($properties as $property) {
                $key = $property;
                $obj = strpos($key, "_object");
                if ($obj > 0) {
                    $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                    $getter = "get$cammel";
                    $value = $object->$getter();
                    $class = substr(strtolower(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                    $query[2] .= "AND $i.\"$class\" = $e.id ";
                    $aux = $this->getQuery($value, $j, $e, $a);
                    $query[0] .= $aux[0];
                    $query[1] .= $aux[1];
                    $query[2] .= $aux[2];
                    $e = $aux[3];
                    $query[4] .= $aux[4];
                }
            }
        }
        $query[3] = $e;
        return $query;
    }
    
    private function fillObject(DataObject $object, $row, $i = "A"){
        $fill = array();
        $class = get_class($object);
        $object = new $class();
        $fill[0] = $object;
        $e = $i;
        $properties = $object->__getProperties();
        if ($row) {
            foreach ($properties as $property) {
                $key = $property;
                $obj = strpos($key, "_object");
                $arr = strpos($key, "_array");
                if ($arr > 0) {
                   $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $arr)  ;
                    $arr += 7;
                    $class = substr(substr($property, 0, 1) . substr($property, 1), $arr, ($arr . 10));
                    $getter = "get$cammel";
                    $setter1 = "set$cammel";
                    $subgetter = "get$cammel" . get_class($object);
                    $subsetter = "set$cammel" . get_class($object);
                    $subObject1 = new $class();
                    $subObject1->$subsetter($object->getId());
                    $subdata1 = $this->select($subObject1, "id", "ASC");
                    $object->$setter1($subdata1);
                    
                } else if ($obj > 0) {
                    $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                    $key = $i . "." . substr(strtolower(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                    $getter = "get$cammel";
                    $setter = "set$cammel";
                    if (isset($row[$key])) {
                        $e++;
                        $subObject2 = new $cammel();
                        $aux = $this->fillObject($subObject2, $row, $e);
                        $object->$setter($aux[0]);
                        $e = $aux[1];
                    }
                } else {
                    $cammel = strtoupper(substr($property, 0, 1)) . substr($property, 1);
                    $getter = "get$cammel";
                    $setter = "set$cammel";
                    $key = "$i.$property";
                    if (isset($row[$key])) {
                        $object->$setter($row[$key]);
                    }
                }
                
                
            }
           
        }
        $fill[1] = $e;
        return $fill;
    }
    
    public function update(DataObject $object, $idname = "id") {
        $class = get_class($object);
        $table = ($class);
        //query preparation
        $query = "UPDATE \"" . $table . "\" ";
        $i = 0;
        $properties = $object->__getProperties();
        foreach ($properties as $property) {
            $key = $property;
            $obj = strpos($key, "_object");
            $arr = strpos($key, "_array");
            if ($arr > 0) {
                $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $arr);
                $getter = "get$cammel";
                $setter = "set$cammel";
                $objects = $object->$getter();
                $arr += 7;
                $class = substr(substr($property, 0, 1) . substr($property, 1), $arr, $arr);
                $object2 = new $class();
                $subSetter = $setter . $table;
                $object2->$subSetter($object->getId());
                if(count($objects)>0){
                    if($this->borrar($object2))
                    foreach ($objects as $subObject){
                        $subObject->$subSetter($object->getId());
                        $this->insert($subObject);
                    }
                }
     
            } else if ($obj > 0) {
                
                  $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                  $getter = "get$cammel";
                  $setter = "set$cammel";
                  $value = $object->$getter();
                  
                  if ($value->getId() != null) {
                      if ($i == 0) {
                          $query .= "SET " . substr($key, 0, $obj) .  " ='" . $value->getId() . "' ";
                          $this->actualizar($value);
                          $i++;
                      } else {
                          $query .= ", " . substr($key, 0, $obj) .  " ='" . $value->getId(). "' ";
                          $this->actualizar($value);
                      }
                  } else {
                      $properties1 = $value->__getProperties();
                      $j = 0;
                      foreach ($properties1 as $property1){
                          $key1 = $property1;
                          $obj1 = strpos($key1, "_object");
                          $arr = strpos($key1, "_array");
                          if ($key1 != null && $obj1 == 0 && $arr == 0){
                              $cammel = strtoupper(substr($property1, 0, 1)) . substr($property1, 1);
                              $getter = "get$cammel";
                              $setter = "set$cammel";
                              if ($value->$getter() != null){
                                  $j++;
                              }
                          }
                    }
                    if($j != 0){
                        $value->setId($this->insert($value));
                        if ($i == 0) {
                            $query .= "SET " . strtolower(substr(get_class($value), 0 , $obj)) .  " ='" . $value->getId() . "' ";
                            $i++;
                        } else {
                            $query .= ", " . strtolower(substr(get_class($value), 0 ,$obj))  .  " ='" . $value->getId(). "' ";
                            
                        }
                    }
                }
            } else {
                $cammel = strtoupper(substr($property, 0, 1)) . substr($property, 1);
                $getter = "get$cammel";
                $setter = "set$cammel";
                $value = $object->$getter();
                if ($key != $idname) {
                    if (isset($value)) {
                        $value = $this->escapeChars($value);
                        if ($i == 0) {
                            $query .= "SET \"$key\"='$value' ";
                            $i++;
                        } else {
                            $query .= ", \"$key\"='$value' ";
                        }
                    } else {
                        if ($i == 0) {
                            $query .= "SET \"$key\"=NULL ";
                            $i++;
                        } else {
                            $query .= ", \"$key\"=NULL ";
                        }
                    }
                }
            }
        }
        if ($object->getId() != null){
            $query .= "WHERE $idname = " . $object->getId();
        } else {
            return false;
        }
        
        echo "$query<br />";
//        exit();
        //query execution
        if (!$this->performWrite($query)) {
            return false;
        }
        //ending
        return true;
    }
    //Perform a data delete process
    //@param $object the object data to delete
    //@returns true if the operation was succesfully, false if not
    public function delete(DataObject $object) {
        $class = get_class($object);
        //query preparation
        $query = "DELETE FROM \"$class\" ";
        $i = 0;
        $properties = $object->__getProperties();
        foreach ($properties as $property) {
            $key = $property;
            $obj = strpos($key, "_object");
            $arr = strpos($key, "_array");
            if(!$arr){
                if ($obj > 0) {
                    $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $obj);
                    $getter = "get$cammel";
                    $setter = "set$cammel";
                    $value = $object->$getter();
                    if ($value->getId() != null) {
                        if ($i == 0) {
                            $query .= "WHERE " . substr($key, 0, $obj) . " = '" . $value->getId() . "' ";
                        } else {
                            $query .= "AND " . substr($key, 0, $obj) . " = '" . $value->getId() . "' ";
                        }
                        $i++;
                    }
                } else {
                    $cammel = strtoupper(substr($property, 0, 1)) . substr($property, 1);
                    $getter = "get$cammel";
                    $setter = "set$cammel";
                    $value = $object->$getter();
                    if (isset($value)) {
                        $value = $this->escapeChars($value);
                        if ($i == 0) {
                            $query .= "WHERE \"$key\" = '$value' ";
                        } else {
                            $query .= "AND \"$key\" = '$value' ";
                        }
                        $i++;
                    }
                }
            
            } else {
                $cammel = substr(strtoupper(substr($property, 0, 1)) . substr($property, 1), 0, $arr);
                $getter = "get$cammel";
                $setter = "set$cammel";
                $objects = $object->$getter();
                $arr += 7;
                $class2 = substr(substr($property, 0, 1) . substr($property, 1), $arr, $arr);
                $object2 = new $class2();
                $subSetter = $setter . $class;
                $object2->$subSetter($object->getId());
                if(count($objects)>0){
                    $this->borrar($object2);
                    
                }
            }
        }
        
        //query execution
        echo "$query<br />";
        if (!$this->performWrite($query)) {
            return false;
        }
        //ending
        return true;
    }
}


?>
