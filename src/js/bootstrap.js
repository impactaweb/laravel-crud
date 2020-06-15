try {
    window.knockout = require('knockout');
    window.ko = require('knockout');
    window.Popper = require('popper.js').default;
    window.jQuery = require('jquery');
    window.$ = window.jQuery;
    require('bootstrap');
    window.Swal = require('sweetalert2')
    require('jquery-ui');
    require('jquery-validation');
    require('bootstrap4-notify');
    require('checkboxes.js/dist/jquery.checkboxes-1.2.2');
    require('summernote/dist/summernote-bs4');
} catch(err) {
    console.error(err.message);
}
