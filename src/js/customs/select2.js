(function ($) {
  $(window.document).ready(function () {
    $('[data-form-select2]').select2({
      placeholder: "Buscar",
      language: {
        inputTooShort: function () {
          return "Digite 3 ou mais caracteres...";
        }
      },
      minimumInputLength: 3,
      ajax: {
        dataType: 'json',
      },
    });

  });
})(window.jQuery);
