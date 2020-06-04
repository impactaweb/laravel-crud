const moment = require("moment");

(function (moment, $) {
  $("[data-input='date-time']")
    .change(function () {
      let data = moment($(this).val(), $(this).data("input-format-client"));
      if (!data.isValid()) {
        $(this).next("input:hidden").val("");
        return;
      }
      $(this)
        .next("input:hidden")
        .val(data.format($(this).data("input-format-server")));
    })
    .each(function () {
      let data = moment(
        $(this).next("input:hidden").val(),
        $(this).data("input-format-server")
      );
      if (!data.isValid()) {
        $(this).val("");
        return;
      }
      $(this).val(data.format($(this).data("input-format-client")));
    });

  $("[data-input='date']")
    .change(function () {
      let data = moment($(this).val(), $(this).data("input-format-client"));
      if (!data.isValid()) {
        $(this).next("input:hidden").val("");
        return;
      }
      $(this)
        .next("input:hidden")
        .val(data.format($(this).data("input-format-server")));
    })
    .each(function () {
      let data = moment(
        $(this).next("input:hidden").val(),
        $(this).data("input-format-server")
      );
      if (!data.isValid()) {
        $(this).val("");
        return;
      }
      $(this).val(data.format($(this).data("input-format-client")));
    });
})(moment, window.jQuery);
