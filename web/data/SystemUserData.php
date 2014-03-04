<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
class SystemUserData {

    private $data;
    public $error;

    /**
     * Constructor
     * @param Data $data <p>El valor de la propiedad</p>
     */
    function __construct(Data $data) {
        $this->data = $data;
    }

    /**
     * Realiza un select con los datos enviados. Retorna una lista de SystemUseres
     * @param SystemUser $systemUser
     * @return array
     */
    public function getSystemUsers(SystemUser $systemUser) {
        $data = array();
        $data = $this->data->select($systemUser);
        return $data;
    }

    /**
     * Realiza un select con los datos enviados sobre la tabla systemUser 
     * @param SystemUser $systemUser 
     * @return SystemUser
     */
    public function getSystemUser(SystemUser $systemUser) {
        $systemUser = $this->data->selectOne($systemUser);
        return $systemUser;
    }

    /**
     * Realiza un insert con los datos enviados. 
     * Retorna true si tubo éxito y false si no.
     * @param SystemUser $systemUser
     * @return Boolean
     */
    public function insertSystemUser(SystemUser $systemUser) {
        $this->data->begin();
        if (!$this->data->insert($systemUser)) {
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }

    /**
     * Realiza un  update con los datos enviados. 
     * Retorna true si tubo éxito y false si no.
     * @param SystemUser $systemUser
     * @return Boolean 
     */
    public function updateSystemUser(SystemUser $systemUser) {
        $this->data->begin();
        if (!$this->data->update($systemUser)) {
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }

    /**
     * Realiza un delete con los datos enviados.
     * Retorna true si tubo éxito y false si no 
     * @param SystemUser $systemUser
     * @return Boolean 
     */
    function deleteSystemUser(SystemUser $systemUser) {
        $this->data->begin();
        if (!$this->data->delete($systemUser)) {
            $this->error = $this->data->getError();
            $this->data->rollback();
            return false;
        }
        $this->data->commit();
        return true;
    }

}

?>
