(function ($) {
  $(window.document).ready(function () {
    $("[data-textarea]")
      .each(function () {
        this.setAttribute("style", "height:" + this.scrollHeight + "px;");
      })
      .on("input", function () {
        this.style.height = "auto";
        this.style.height = this.scrollHeight + "px";

        const height = +this.style.height.replace(/\D+/, "");

        this.style.overflowY = height >= 500 ? "scroll" : "hidden";
      });
  });
})(window.jQuery);
