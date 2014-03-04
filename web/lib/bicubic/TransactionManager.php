<?php

/**
 * Description of TransactionManager
 *
 * @author zenilt
 */
class TransactionManager {

    public $data;
    public $error;
    public $totalrows;

    /**
     * Constructor
     * @param Data $data <p>El valor de la propiedad</p>
     */
    function __construct(Data $data) {
        $this->data = $data;
    }

    /**
     * Realiza un select con los datos enviados. Retorna una lista de DataObject
     * @param DataObject $dataObject
     * @return array
     */
    public function getAll(DataObject $dataObject, $orderIndex = 'id', $orderDirection = 'DESC') {
        $data = $this->data->select($dataObject, $orderIndex, $orderDirection);
        return $data;
    }

    /**
     * Realiza un select con los datos enviados. Retorna una lista de DataObject
     * @param DataObject $dataObject
     * @return array
     */
    public function getAllPaged(DataObject $dataObject, $orderIndex = 'id', $orderDirection = 'DESC', $itemsPerPage = 10, $lastIndex = 0, $keyword = null, $keywordfield = null) {
        $data = $this->data->select($dataObject, $orderIndex, $orderDirection, $itemsPerPage, $lastIndex, $keyword, $keywordfield);
        return $data;
    }

    /**
     * Realiza un select con los datos enviados sobre la tabla DataObject 
     * @param DataObject $dataObject 
     * @return DataObject
     */
    public function getRecord(DataObject $dataObject) {
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
        if ($empty) {
            return null;
        }

        $dataObject = $this->data->selectOne($dataObject);
        if (!$dataObject->getId()) {
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

    /**
     * Realiza un insert con los datos enviados. 
     * Retorna true si tubo éxito y false si no.
     * @param DataObject $dataObject
     * @return Boolean
     */
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

    /**
     * Realiza un  update con los datos enviados. 
     * Retorna true si tubo éxito y false si no.
     * @param DataObject $dataObject
     * @return Boolean 
     */
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

    /**
     * Realiza un delete con los datos enviados.
     * Retorna true si tubo éxito y false si no 
     * @param DataObject $dataObject
     * @return Boolean 
     */
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

}

?>
