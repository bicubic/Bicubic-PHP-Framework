function formSubmit() {
    if (checkInput(document.getElement('[name={APPFORM-PARAM-APP-NAME}]')) && 
        checkInput(document.getElement('[name={APPFORM-PARAM-APP-COMPANY}]')) && 
        checkInput(document.getElement('[name={APPFORM-PARAM-APP-PROTOCOL}]'))) {
        document.getElement('[name={APPFORM-ID}]').submit();
    }
    
}



window.addEvent('domready', function() {
    $('formsubmit').addEvent('click', function() {
        formSubmit();
    });
     
});

