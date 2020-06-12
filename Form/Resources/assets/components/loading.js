(function() {
  window.initLoading = function(element = '[data-container="loading"]') {
    jQuery(element).html(`
              <div class='loading-container fixed'>
                  <div class='lds-roller'>
                      <div></div>
                      <div></div>
                      <div></div>
                      <div></div>
                  </div>
              </div>
            `);
  };

  window.finishLoading = function(element = '[data-container="loading"]') {
    jQuery(element).html("");
  };
})();
