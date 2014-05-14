var setInputInvalid = function(element, error) {
    element.setCustomValidity(error);
    element.classList.remove("loading");
    element.classList.add("warnings");
};

var setInputValid = function(element) {
    element.setCustomValidity('');
    element.classList.remove("loading");
    element.classList.remove("warnings");
};

var setInputLoading = function(element) {
    element.setCustomValidity('loading');
    element.classList.remove("warnings");
    element.classList.add("loading");
};

var checkEmpty = function() {
    if (this.value === '') {
        setInputInvalid(this, this.getAttribute('data-langempty'));
    } else {
        setInputValid(this);
    }
};

var checkEqual = function(self, element) {
    if (this.value === '' || self.value !== element.value) {
        setInputInvalid(self, self.getAttribute('data-langnotequal'));
    } else {
        setInputValid(self);
    }
};