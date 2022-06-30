// constants and common code

// helpers
window.getOffset = function (el) {
    var _x = 0;
    var _y = 0;
    while (el && !isNaN(el.offsetLeft) && !isNaN(el.offsetTop)) {
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop - el.scrollTop;
        el = el.offsetParent;
    }
    return {top: _y, left: _x};
}

// global helper to validate a string presence and length, returns an array of errors
window.validateStrField = function (str, fieldName, minLength = 5) {
    let errors = [];
    str = str.trim();
    if (str.length == 0) {
        errors.push(`El ${fieldName} no puede estar vacío`);
    }

    if (str.length < minLength) {
        errors.push(`El ${fieldName} debe tener al menos ${minLength} caracteres`);
    }
    return errors;
}

window.validateEmailField = function (email) {
    let errors = [];
    email = email.trim();
    if (email.length == 0) {
        errors.push("El e-mail no puede estar vacío");
    }

    if (!/^[A-Z0-9_!#$%&'*+/=?`{|}~^-]+(?:\.[A-Z0-9_!#$%&'*+/=?`{|}~^-]+)*@[A-Z0-9-]+(?:\.[A-Z0-9-]+)*$/i.test(email)) {
        errors.push("El e-mail debe ser válido");
    }
    return errors;
}

window.validatePhoneField = function (phone) {
    let errors = [];
    phone = phone.trim();
    if (phone.length == 0) {
        errors.push("El teléfono no puede estar vacío");
    }

    if (!/^[0-9 ()+-]{5,15}$/.test(phone)){
        errors.push("El teléfono debe ser válido");
    }
    return errors;
}
