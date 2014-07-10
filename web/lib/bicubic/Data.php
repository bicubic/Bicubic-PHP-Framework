<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
abstract class Data {

    public abstract function begin();

    public abstract function commit();

    public abstract function rollback();

    public abstract function close();

    public abstract function performWrite($query, $params = array());

    public abstract function performRead($query);

    public abstract function readNext($result);

    public abstract function rowsNumber($result);

    public abstract function getError();

    public abstract function escapeChars($value);

    public abstract function unEscapeChars($value);

    public abstract function freeMemory($result);

    public abstract function lastInsertId($table);

    public abstract function select(DataObject $object, OrderParam $orderParam = null, $limit = null, $lastIndex = null);

    public abstract function selectOne(DataObject $object);

    public abstract function insert(DataObject $object);

    public abstract function update(DataObject $object);

    public abstract function delete(DataObject $object);
}

