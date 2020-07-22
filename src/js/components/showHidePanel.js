(function($) {
  const $panels = document.querySelectorAll("div[data-show-rules-panel]");

  if (!$panels.length) return;

  $panels.forEach($panel => {
    const rules = JSON.parse($panel.getAttribute("data-show-rules-panel"));
    Object.keys(rules).forEach(key => {
      const $shoter = document.querySelector(`[name=${key}]`);

      $shoter &&
        $shoter.addEventListener("change", ({ target }) => {
          if (target.value === String(rules[key])) {
            return $($panel).show();
          }

          $($panel).hide();
        });
    });
  });
})(jQuery);
