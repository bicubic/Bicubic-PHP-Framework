<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
interface DataObject {

    /**
     * Propiedades de BD
     * @return array las propiedades a serializar en la BD
     */
    public function __getProperties();
    
    /**
     * Tipos de las Propiedades de BD
     * @return array de los tipos de las propiedades a serializar en la BD
     */
    public function __getTypes();
    
    /**
     * Verifica que Contenga todos las propiedades necesarias para ingresar a BD
     * @return true si contiene todas las propiedades necesarias, false si no
     */
    public function __isComplete();
    
}
?>
