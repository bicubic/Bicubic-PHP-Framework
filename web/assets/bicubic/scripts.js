/*
 * Copyright (C) Juan Francisco Rodríguez
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

var CLASS_LOADING = "loading";
var CLASS_WARNINGS = "warnings";
var DATA_LANGEMPTY = "data-langempty";
var DATA_LANGNOTEQUAL = "data-langnotequal";
var DATA_REQUIRED = "data-required";
var DATA_SELECTED = "data-selected";
var DATA_CHECKED = "data-checked";

var setInputInvalid = function (element, error) {
    element.setCustomValidity(error);
    element.classList.remove(CLASS_LOADING);
    element.classList.add(CLASS_WARNINGS);
};
var setInputValid = function (element) {
    element.setCustomValidity('');
    element.classList.remove(CLASS_LOADING);
    element.classList.remove(CLASS_WARNINGS);
};
var setInputLoading = function (element) {
    element.setCustomValidity(CLASS_LOADING);
    element.classList.remove(CLASS_WARNINGS);
    element.classList.add(CLASS_LOADING);
};
var checkEmpty = function () {
    if (this.value === '') {
	setInputInvalid(this, this.getAttribute(DATA_LANGEMPTY));
    } else {
	setInputValid(this);
    }
};
var checkEqual = function (self, element) {
    if (this.value === '' || self.value !== element.value) {
	setInputInvalid(self, self.getAttribute(DATA_LANGNOTEQUAL));
    } else {
	setInputValid(self);
    }
};
function getAllElementsWithAttribute(attribute)
{
    var matchingElements = [];
    var allElements = document.getElementsByTagName('*');
    for (var i = 0, n = allElements.length; i < n; i++)
    {
	if (allElements[i].getAttribute(attribute))
	{
	    matchingElements.push(allElements[i]);
	}
    }
    return matchingElements;
}

//Common elements
document.addEventListener('DOMContentLoaded', function () {
    var datarequireds = getAllElementsWithAttribute(DATA_REQUIRED);
    for (var i = 0, n = datarequireds.length; i < n; i++) {
	if (datarequireds[i].getAttribute(DATA_REQUIRED) === "required") {
	    datarequireds[i].required = true;
	}
    }
    var dataselecteds = getAllElementsWithAttribute(DATA_SELECTED);
    for (var i = 0, n = dataselecteds.length; i < n; i++) {
	if (dataselecteds[i].getAttribute(DATA_SELECTED) === "selected") {
	    dataselecteds[i].selected = true;
	}
    }
    var datacheckeds = getAllElementsWithAttribute(DATA_CHECKED);
    for (var i = 0, n = datacheckeds.length; i < n; i++) {
	if (datacheckeds[i].getAttribute(DATA_CHECKED) === "checked") {
	    datacheckeds[i].checked = true;
	}
    }
});

//Bicubic Table
document.addEventListener('DOMContentLoaded', function () {
    var bicubictable = document.getElementById('bicubic-table');
    if (bicubictable) {
	var bicubictableRequest = new XMLHttpRequest();
	var bicubictableUrl = bicubictable.getAttribute("data-url");
	var bicubictableMaxSize = bicubictable.getAttribute("data-maxsize");
	var bicubictableMore = true;
	function loadBicubicTableData() {
	    if (bicubictableRequest.readyState === 4 || bicubictableRequest.readyState === 0) {
		bicubictableRequest.open("POST", bicubictableUrl);
		bicubictableRequest.onreadystatechange = function () {
		    if (bicubictableRequest.readyState === 4 && bicubictableRequest.status === 200) {
			var response = JSON.parse(this.responseText);
			if (response.status === 'success') {
			    if (response.data) {
				bicubictable.innerHTML = bicubictable.innerHTML + response.data;
			    }
			    bicubictable.setAttribute("data-lastid", response.lastid);
			    if (response.size < bicubictableMaxSize) {
				bicubictableMore = false;
			    }
			    else {
				if (document.body.scrollHeight <= window.innerHeight) {
				    loadBicubicTableData();
				}
			    }
			}
		    }
		};
		bicubictableRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		bicubictableRequest.send("lastid=" + bicubictable.getAttribute("data-lastid"));
	    }
	}
	loadBicubicTableData();
	document.addEventListener('scroll', function () {
	    if (bicubictableMore) {
		if ((document.body.scrollHeight / window.innerHeight) * 100 > 80) {
		    loadBicubicTableData();
		}
	    }
	});
    }
});