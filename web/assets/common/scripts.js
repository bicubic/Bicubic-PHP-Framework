//Systemuser elements
document.addEventListener('DOMContentLoaded', function() {
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
            newpassword.addEventListener('blur', function() {
                checkEqual(newpassword, confirmnewpassword);
            });
        }
    }
    var confirmnewpassword = document.getElementById('systemuser-confirmnewpassword');
    if (confirmnewpassword) {
        if (confirmnewpassword.required) {
            confirmnewpassword.addEventListener('blur', function() {
                checkEqual(newpassword, confirmnewpassword);
            });
        }
    }
});