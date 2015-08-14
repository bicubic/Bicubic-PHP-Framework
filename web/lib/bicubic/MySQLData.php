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
