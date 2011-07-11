var overlay = new Overlay(document.body,{
    id: 'overlay',
    color: '#000',
    duration: 200,
    opacity: 0.9,
    onShow: function() {
        this.element.removeClass('hidden');
    }
});

function checkInput(field) {
    if (field.value == "") {
        field.getParent().set("class","error");
        if (!field.disabled) {
            field.focus();
        }
        return false;
    }
    else {
        field.getParent().set("class","");
        return true;
    }
}

function checkSelect(field) {
    if (field.selectedIndex <= 0 || field.text == "" || field.value == "-1") {
        field.getParent().set("class","error");
        if (!field.disabled) {
            field.focus();
        }
        return false;
    }
    else {
        field.getParent().set("class","")
        return true;
    }
}

function checkList(select,token) {
    if(token <= 0) {
        select.getParent().set("class","error");
        select.selectedIndex="0";
        select.focus();
        return false;
    }
    select.getParent().set("class","");
    return true;
}

function checkBox(input) {
    if(!input.checked) {
        input.getParent().set("class","error");
        input.focus();
        return false;
    }
    input.getParent().set("class","");
    return true;
}

function validateEmail(field) {
    var pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,7}$/;
    if (pattern.test(field.value)) {
        field.getParent().set("class","");
        return true;
    }
    else {
        field.getParent().set("class","error");
        if (field.get('disabled') != 'disabled') {
            field.focus();
        }
        return false;
    }
}

//Validar números:
function validateNumber(field) {
    var ok = true;
    if (field.value != '' && field.value != null) {
        var pattern = /^[0-9]+$/
        if (pattern.test(field.value)) {
            field.getParent().set("class","");
            ok = true;
        }
        else {
            field.getParent().set("class","error");
            if (field.get('disabled') != 'disabled') {
                field.focus();
            }
            ok = false;
        }
    }
    else {
        field.getParent().set("class","");
        ok = true;
    }
    return ok;
}
function validatePhone(field1,field2) {
    if (validateNumber(field1) == false || validateNumber(field2) == false) {
        field1.getParent().set('class','error');
        if (field1.get('disabled') != 'disabled') {
            field1.focus();
        }
        return false;
    }
    else {
        field1.getParent().set('class','')
        return true;
    }
}
function validatePercentage(field) {
    var ok = true;
    if (field.value != '' && field.value != null) {
        var pattern = /^[0-9]+$|(^[0-9]*\,[0-9]+$)/
        if (pattern.test(field.value) && parseFloat(field.value) <= 100.0) {
            field.getParent().set("class","");
            ok = true;
        }
        else {
            field.getParent().set("class","error");
            if (field.get('disabled') != 'disabled') {
                field.focus();
            }
            ok = false;
        }
    }
    else {
        field.getParent().set("class","");
        ok = true;
    }
    return ok;
}