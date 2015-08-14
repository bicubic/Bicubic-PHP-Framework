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
