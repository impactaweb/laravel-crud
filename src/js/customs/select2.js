(function ($) {
  $(window.document).ready(function () {

    $('[data-select2]').select2({
      ajax: {
        url: $(this).data('url'),
        dataType: 'json'
      }
    });

  });
})(window.jQuery);
