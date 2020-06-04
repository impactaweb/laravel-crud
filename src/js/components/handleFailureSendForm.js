module.exports = function handleFailureSendForm(error) {
  const $alert = document.querySelector("[data-expect-alert]");

  jQuery('[data-container="loading"]').html("");

  if (error.status >= 500) {
    const alertError = `
          <div class="container alert mb-1 alert-danger alert-dismissible fade show" role="alert" data-expect >
          <span data-content>${error.responseJSON.errors}</span>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times</span>
          </button>
          </div>
          `;

    $alert.innerHTML = alertError;
    $alert.scrollIntoView();
    return;
  }

  if (error.status !== 422 || !error.responseJSON.errors) return;

  const alertMessage = `
      <div class="container alert mb-1 alert-danger alert-dismissible fade show" role="alert" data-expect >
          <span data-content>Ops! Por favor, verifique os campos abaixo.</span>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times</span>
          </button>
      </div>
      `;
  const camposInvalidos = error.responseJSON.errors;

  let hasScroll = false;

  // Erros enviados pela request do laravel
  Object.keys(camposInvalidos).forEach(function (name) {
    let input;
    const erros = "<li>" + camposInvalidos[name].join("</li><li>") + "</li>";
    if (name.includes(".")) {
      name = name.split(".");
      input = '[name="' + name[0] + "[" + name[1] + ']"]';
    } else {
      input = '[name="' + name + '"]';
    }
    const $input = jQuery(input);

    $input.hasClass("is-invalid") ? null : $input.toggleClass("is-invalid");
    $input.next(".invalid-feedback").html(erros);

    if (hasScroll) return;

    $input.get(0).scrollIntoView(true);
    hasScroll = true;
  });

  $alert.innerHTML = alertMessage;
  $alert.scrollIntoView();
};
