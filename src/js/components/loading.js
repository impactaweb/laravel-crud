"use strict";
const $ = window.jQuery;

window.initLoading = function(element = '[data-container="loading"]') {
  $(element).html(`
      <div class="loading-container fixed">
          <div class="lds-roller">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
          </div>
      </div>
    `);
};

window.finishLoading = function(element = '[data-container="loading"]') {
  $(element).html("");
};
