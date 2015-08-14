<?php

/*
 * The MIT License
 *
 * Copyright 2015 Juan Francisco Rodríguez.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class PostgreSQLData extends SQLData {

    public function __construct($config) {
	$host = $config["database_host"];
	$user = $config["database_user"];
	$password = $config["database_password"];
	$database = $config["database_database"];
	$this->debug = $config["debugdatabase"];
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

    public function performRead($query) {
	if ($this->debug) {
	    echo $query;
	    $result = pg_query($this->connection, $query) or die(pg_last_error($this->connection));
	} else {
	    $result = pg_query($this->connection, $query) or die("data connection error");
	}
	return $result;
    }

    public function readNext($result) {
	$row = pg_fetch_assoc($result);
	return $row;
    }

    public function rowsNumber($result) {
	$n = pg_num_rows($result);
	return $n;
    }

    public function getError() {
	$this->globalError = pg_last_error($this->connection);
	$error = $this->globalError;
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
	return pg_escape_string($this->connection, strval($value));
    }

    public function freeMemory($result) {
	pg_free_result($result);
    }

    public function lastInsertId($table) {
	$result = $this->performRead("SELECT currval('" . strtolower($table) . "_id_seq') as lastid");
	$row = $this->readNext($result);
	if ($row !== false) {
	    return $row['lastid'];
	}
	return -1;
    }

    public function unEscapeChars($value) {
	return $value;
    }

}
