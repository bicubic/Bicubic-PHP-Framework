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
        $data = array();
        $data = $this->data->select($dataObject, $orderIndex, $orderDirection);
        return $data;
    }

    /**
     * Realiza un select con los datos enviados. Retorna una lista de DataObject
     * @param DataObject $dataObject
     * @return array
     */
    public function getAllPaged(DataObject $dataObject, $orderIndex, $orderDirection, $page, $itemsPerPage, $keyword = null, $keywordfield = null) {
        $data = array();
        $data = $this->data->select($dataObject, $orderIndex, $orderDirection, $itemsPerPage, $itemsPerPage * $page, $keyword, $keywordfield);
        $this->totalrows = $this->data->getTotalRows();
        return $data;
    }

    /**
     * Realiza un select con los datos enviados sobre la tabla DataObject 
     * @param DataObject $dataObject 
     * @return DataObject
     */
    public function getRecord(DataObject $dataObject) {
        $dataObject = $this->data->selectOne($dataObject);
        if (!$dataObject) {
            return null;
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
        $id = $dataObject->getId();
        if (isset($id)) {
            $this->error = "id not null";
            return false;
        }
        $id = $this->data->insert($dataObject);
        if ($id === false) {
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
    public function updateRecord(DataObject $dataObject, $notNulls = false) {
        $id = $dataObject->getId();
        if (!isset($id)) {
            $this->error = "null id";
            return false;
        }
        if (!$notNulls) {
            if (!$this->data->update($dataObject)) {
                $this->error = $this->data->getError();
                return false;
            }
        } else {
            if (!$this->data->updateNotNulls($dataObject)) {
                $this->error = $this->data->getError();
                return false;
            }
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
        $id = $dataObject->getId();
        if (!isset($id)) {
            $this->error = "null id";
            return false;
        }
        if (!$this->data->delete($dataObject)) {
            $this->error = $this->data->getError();
            return false;
        }
        return true;
    }

}

?>
