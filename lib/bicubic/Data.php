<?php

/**
 * Bicubic PHP Framework
 * Data Class
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license    
 * @framework  2.1
 */
abstract class Data {

    //Begins a transaction
    public abstract function begin();

    //Commits a transaction
    public abstract function commit();
    
    public abstract function rollback();

    public abstract function close();

    //Performs a writing query inside a transaction
    //@param $query a query to execute
    //@returns true if writting is performed or false if not
    public abstract function performWrite($query, $params = array());

    //Performs a read query inside a transaction
    //@param $query a query to execute
    //@returns the result object
    public abstract function performRead($query);

    //Read next Result
    //@param $result a result of reading perform
    //@returns a row of data
    public abstract function readNext($result);

    //Read the numbers of rows in a reading result
    //@param $result a result of reading perform
    //@returns the number of rows
    public abstract function rowsNumber($result);

    public abstract function getError();

    public abstract function escapeChars($value);

    public abstract function unEscapeChars($value);

    public abstract function freeMemory($result);

    public abstract function lastInsertId($table);

    //Performs a data selection process
    //@param $object the target object that filters the data
    //@returns an array containing objects of the type of $object
    public abstract function select(DataObject $object);

    //Performs a data unique selection process
    //@param $object the target object to copy the data
    //@returns a new object of the type of $object
    public abstract function selectOne(DataObject $object);

    //Perform a data insert process
    //@param $object the object data to insert
    //@returns true if the operation was succesfully, false if not
    public abstract function insert(DataObject $object);

    //Perform a data updte process
    //@param $object the object data to update
    //@returns true if the operation was succesfully, false if not
    public abstract function update(DataObject $object);

    //Perform a data delete process
    //@param $object the object data to delete
    //@returns true if the operation was succesfully, false if not
    public abstract function delete(DataObject $object);

    public abstract function getTotalRows();
}
?>
