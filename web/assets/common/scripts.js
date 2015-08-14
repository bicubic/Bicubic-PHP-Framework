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