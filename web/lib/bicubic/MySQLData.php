<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
class MySQLData extends SQLData {

    //Connection object
    private $connection;

    //Constructor
    //@param $config the base configuration
    public function __construct($host, $user, $password, $database) {
        $this->connection = mysqli_connect($host, $user, $password, $database);
        mysqli_set_charset($this->connection, 'utf8');
    }

    //Begins a transaction ;

    public function begin() {
        mysqli_autocommit($this->connection, false);
    }

    public function rollback() {
        mysqli_rollback($this->connection);
        mysqli_autocommit($this->connection, true);
        //echo "rollback <br>";
    }

    //Commits a transaction
    public function commit() {
        mysqli_commit($this->connection);
        mysqli_autocommit($this->connection, true);
        //echo "commit <br>";
    }

    public function close() {
        mysqli_close($this->connection);
    }

    //Performs a writing query inside a transaction
    //@param $query a query to execute
    //@returns true if writting is performed or false if not
    public function performWrite($query, $params = array()) {
        if (!mysqli_query($this->connection, $query)) {
            return false;
        }
        return true;
    }

    //Performs a read query inside a transaction
    //@param $query a query to execute
    //@returns the result object
    public function performRead($query) {
        $result = mysqli_query($this->connection, $query) or die(mysqli_error($this->connection));
        return $result;
    }

    //Read next Result
    //@param $result a result of reading perform
    //@returns a row of data
    public function readNext($result) {
        $row = mysqli_fetch_assoc($result);
        return $row;
    }

    //Read the numbers of rows in a reading result
    //@param $result a result of reading perform
    //@returns the number of rows
    public function rowsNumber($result) {
        $n = mysqli_num_rows($result);
        return $n;
    }


    public function getError() {
        return mysqli_error($this->connection);
    }

    public function escapeChars($value) {
        return mysqli_real_escape_string($this->connection, $value);
    }

    public function freeMemory($result) {
        mysqli_free_result($result);
    }

    public function lastInsertId($table) {
        return mysqli_insert_id($this->connection);
    }


    public function unEscapeChars($value) {
        return $value;
    }

}
?>
