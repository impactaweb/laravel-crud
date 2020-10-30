(function ($) {
  $(window.document).ready(function () {
    $('[data-form-select2]').select2({
      placeholder: "Buscar",
      language: {
        inputTooShort: function () {
          return "Digite 3 ou mais caracteres...";
        },
        noResults: function () {
          return "Nenhum resultado encontrado.";
        }
      },
      minimumInputLength: 1,
      ajax: {
        dataType: 'json',
      },
    });

  });
})(window.jQuery);
