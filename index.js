try {
    window.knockout = require('knockout')
    window.Popper = require('popper.js').default;
    window.jQuery = require('jquery');
    window.$ = window.jQuery;
    require('bootstrap');
    require('jquery-ui');
    require('jquery-validation');
    require('bootstrap4-notify');
    require('checkboxes.js/dist/jquery.checkboxes-1.2.2')
    require('summernote/dist/summernote-bs4');
    require("inputmask/dist/jquery.inputmask");
    require("./src/js/form/lib/multicheckbox");
} catch (e) {
    console.error(e.message)
}

