(function(moment, $) {
  /**
   * For handle date with time
   */
  $("[data-input='date-time']")
    .on("change input", function() {
      // guarda a referência do elemento, ajuda na perfomance da execução do código
      const $ELEMENT_REF = $(this);

      let data = moment(
        $ELEMENT_REF.val(),
        $ELEMENT_REF.data("input-format-client")
      );

      if (!data.isValid()) {
        $ELEMENT_REF.next("input:hidden").val("");
        return;
      }

      $ELEMENT_REF
        .next("input:hidden")
        .val(data.format($ELEMENT_REF.data("input-format-server")));
    })
    .each(function() {
      const $ELEMENT_REF = $(this);

      let data = moment(
        $ELEMENT_REF.next("input:hidden").val(),
        $ELEMENT_REF.data("input-format-server")
      );

      if (!data.isValid()) {
        $ELEMENT_REF.val("");
        return;
      }

      $ELEMENT_REF.val(data.format($ELEMENT_REF.data("input-format-client")));
    });

  /**
   * For handle date only
   */
  $("[data-input='date']")
    .on("change input", function() {
      const $ELEMENT_REF = $(this);
      let data = moment(
        $ELEMENT_REF.val(),
        $ELEMENT_REF.data("input-format-client")
      );

      if (!data.isValid()) {
        $ELEMENT_REF.next("input:hidden").val("");
        return;
      }

      $ELEMENT_REF
        .next("input:hidden")
        .val(data.format($ELEMENT_REF.data("input-format-server")));
    })
    .each(function() {
      $ELEMENT_REF = $(this);

      let data = moment(
        $ELEMENT_REF.next("input:hidden").val(),
        $ELEMENT_REF.data("input-format-server")
      );

      if (!data.isValid()) {
        $ELEMENT_REF.val("");
        return;
      }

      $ELEMENT_REF.val(data.format($ELEMENT_REF.data("input-format-client")));
    });
})(window.moment, window.jQuery);
