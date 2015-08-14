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

//Systemuser elements
document.addEventListener('DOMContentLoaded', function () {
    var name = document.getElementById('systemuser-name');
    if (name) {
    }
    var email = document.getElementById('systemuser-email');
    if (email) {
    }
    var newemail = document.getElementById('systemuser-newemail');
    if (newemail) {
    }
    var usercountry = document.getElementById('systemuser-usercountry');
    if (usercountry) {
    }
    var userlang = document.getElementById('systemuser-userlang');
    if (userlang) {
    }
    var password = document.getElementById('systemuser-password');
    if (password) {
    }
    var currentpassword = document.getElementById('systemuser-currentpassword');
    if (currentpassword) {
    }
    var newpassword = document.getElementById('systemuser-newpassword');
    if (newpassword) {
	if (newpassword.required) {
	    newpassword.addEventListener('blur', function () {
		checkEqual(newpassword, confirmnewpassword);
	    });
	}
    }
    var confirmnewpassword = document.getElementById('systemuser-confirmnewpassword');
    if (confirmnewpassword) {
	if (confirmnewpassword.required) {
	    confirmnewpassword.addEventListener('blur', function () {
		checkEqual(newpassword, confirmnewpassword);
	    });
	}
    }
});