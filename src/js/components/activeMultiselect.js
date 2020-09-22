(function($) {
  $(document).ready(function() {
    $(".btn-multiselect").click(function() {
      $(this)
        .parent()
        .find(".dropdown-menu")
        .toggleClass("show");
    });

    $(document).mouseup(function(e) {
      let container = $(".multiselect-container.dropdown-menu");

      if (
        !container.is(e.target) &&
        container.has(e.target).length === 0 &&
        container.hasClass("show")
      ) {
        container.removeClass("show");
      }
    });
  });
})(window.jQuery);
