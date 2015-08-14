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

class ObjectParamList {

    private $dataobject;
    private $values;
    private $selected;

    function __construct(DataObject $dataobject, $values, $selected = array()) {
	$this->dataobject = $dataobject;
	$this->values = $values;
	$this->selected = $selected;
    }

    function getValues() {
	return $this->values;
    }

    function setValues($value) {
	$this->values = $value;
    }

    function getDataObject() {
	return $this->dataobject;
    }

    function setDataObject($value) {
	$this->dataobject = $value;
    }

    public function getSelected() {
	return $this->selected;
    }

    public function setSelected($selected) {
	$this->selected = $selected;
    }

}
