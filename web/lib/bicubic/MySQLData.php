<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class MySQLData extends SQLData {


    public function __construct($config) {
        $host = $config["database_host"];
        $user = $config["database_user"];
        $password = $config["database_password"];
        $database = $config["database_database"];
	$this->debug = $config["debugdatabase"];
        $this->connection = mysqli_connect($host, $user, $password, $database) or die("Error " . mysqli_connect_error());
        mysqli_set_charset($this->connection, 'utf8');
    }

    public function begin() {
        mysqli_autocommit($this->connection, false);
    }

    public function rollback() {
        mysqli_rollback($this->connection);
        mysqli_autocommit($this->connection, true);
    }

    public function commit() {
        mysqli_commit($this->connection);
        mysqli_autocommit($this->connection, true);
    }

    public function close() {
        mysqli_close($this->connection);
    }

    public function performWrite($query, $params = array()) {
	if ($this->debug) {
            echo $query;
            var_dump($params);
        }
        if (!mysqli_query($this->connection, $query)) {
            return false;
        }
        return true;
    }

    public function performRead($query) {
	if ($this->debug) {
            echo $query;
	}
        $result = mysqli_query($this->connection, $query) or die(mysqli_error($this->connection));
        return $result;
    }

    public function readNext($result) {
        $row = mysqli_fetch_assoc($result);
        return $row;
    }

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

