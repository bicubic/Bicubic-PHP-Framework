<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */
class PostgreSQLData extends SQLData {

    //Connection object
    private $connection;
    private $debug = false;

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
        if ($this->debug) {
            echo $query;
            var_dump($params);
        }
        if (count($params) > 1) {
            if (pg_query_params($this->connection, $query, $params) === FALSE) {
                return false;
            }
        } else {
            if (pg_query($this->connection, $query) === FALSE) {
                return false;
            }
        }
        return true;
    }

    //Performs a read query inside a transaction
    //@param $query a query to execute
    //@returns the result object
    public function performRead($query) {
        if ($this->debug) {
            echo $query;
            $result = pg_query($this->connection, $query) or die(pg_last_error($this->connection));
        } else {
            $result = pg_query($this->connection, $query) or die("data connection error");
        }
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
        $error = pg_last_error($this->connection);
        if ($this->localError) {
            $error .= " " . $this->localError;
        }
        return $error;
    }

    public function escapeChars($value) {
        if ($value === false) {
            $value = 'f';
        }
        if ($value === true) {
            $value = 't';
        }
        if ($this->debug) {
            var_dump($value);
        }
        return pg_escape_string($this->connection, $value);
    }

    public function freeMemory($result) {
        pg_free_result($result);
    }

    public function lastInsertId($table) {
        $query = "BEGIN";
        $result = $this->performRead("SELECT currval('" . strtolower($table) . "_id_seq') as lastid");
        $row = $this->readNext($result);
        if ($row !== false) {
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
