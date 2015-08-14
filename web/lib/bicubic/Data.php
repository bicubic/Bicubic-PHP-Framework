<?php

/*
 * Copyright (C)  Juan Francisco Rodríguez
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
