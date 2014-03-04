function loginSubmit(loginValidator) {
    loginValidator.reset();
    if(loginValidator.validate()) {
        $("loginForm").submit();
    }
}


window.addEvent('domready', function() {
    /**
     * Agrega validador de checkbox
     */
    Form.Validator.add('requireCheck', {
        errorMsg: '{LANG-ERRORREQUIERED}',
        test: function(element){
            if (!element.checked) return false;
            else return true;
        }
    });
    /**
     * Textos sobre los inputs de Login
     */
    $("loginForm").getElements('[type=text], [type=password]').each(function(el){
        new OverText(el);
    });
    /**
     * Validacion del login
     */
    var loginValidator = new Form.Validator($("loginForm"));
    /**
     * Evento del boton de login
     */
    $('loginButton').addEvent('click',function(){
        loginSubmit(loginValidator);
    });
});