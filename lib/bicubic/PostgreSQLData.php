<?php

/**
 * Bicubic PHP Framework
 * MySQLData Class
 *
 * @author     Juan Rodríguez-Covili <jrodriguez@bicubic.cl>
 * @copyright  2010 Bicubic Technology - http://www.bicubic.cl
 * @license    Bicubic Comercial Licence
 * @license    Licenced for ONEMI, Chile
 * @framework  2.1
 */
class PostgreSQLData extends SQLData {

    //Connection object
    private $connection;

    //Constructor
    //@param $config the base configuration
    public function __construct($host, $user, $password, $database) {
        $this->connection = pg_connect("host=$host dbname=$database user=$user password=$password");
        pg_set_client_encoding($this->connection, 'utf8');
    }

    public function begin() {
        $query = "BEGIN";
        $this->performWrite($query);
    }

    public function rollback() {
        $query = "ROLLBACK";
        $this->performWrite($query);
    }

    //Commits a transaction
    public function commit() {
        $query = "COMMIT";
        $this->performWrite($query);
    }

    public function close() {
        pg_close($this->connection);
    }

    //Performs a writing query inside a transaction
    //@param $query a query to execute
    //@returns true if writting is performed or false if not
    public function performWrite($query, $params = array()) {
        if (count($params) > 1) {
            if(pg_query_params($this->connection, $query, $params) === FALSE) {
                return false;
            }
        }
        else {
            if(pg_query($this->connection, $query) === FALSE) {
                return false;
            }
        }
        return true;
    }

    //Performs a read query inside a transaction
    //@param $query a query to execute
    //@returns the result object
    public function performRead($query) {
        $result = pg_query($this->connection, $query) or die(pg_last_error($this->connection));
        return $result;
    }

    //Read next Result
    //@param $result a result of reading perform
    //@returns a row of data
    public function readNext($result) {
        $row = pg_fetch_assoc($result);
        return $row;
    }

    //Read the numbers of rows in a reading result
    //@param $result a result of reading perform
    //@returns the number of rows
    public function rowsNumber($result) {
        $n = pg_num_rows($result);
        return $n;
    }

    public function getError() {
        return pg_last_error($this->connection);
    }

    public function escapeChars($value) {
        return pg_escape_string($this->connection, $value);
    }

    public function freeMemory($result) {
        pg_free_result($result);
    }

    public function lastInsertId($table) {
        $query = "BEGIN";
        $result = $this->performRead("SELECT currval('" . strtolower($table) . "_id_seq') as lastid");
        $row = $this->readNext($result);
        if($row !== false) {
            return $row['lastid'];
        }
        return -1;
    }

    public function getTotalRows() {
        return $this->totalRows;
    }

    public function unEscapeChars($value) {
        return $value;
    }

}

?>
