module.exports = function handleFailureSendForm(error) {
  window.jQuery('[data-container="loading"]').html("");

  if (error.status >= 500) {
    // TODO: Uma boa feature seria um endpoint pra envios de erros internos a partir do front.
    const errorData = {
      title: "Ocorreu um erro no processo",
      type: "error",
      html:
        "<p>Pedimos desculpa pelo erro, por favor entre em contato com o suporte.</p>"
    };

    return window.alertaHtml(errorData);
  }

  if (error.status !== 422 || !error.responseJSON.errors) return;

  const camposInvalidos = error.responseJSON.errors;

  let hasScroll = false;

  // Erros enviados pela request do laravel
  Object.keys(camposInvalidos).forEach(function(name) {
    let input;
    const erros = "<li>" + camposInvalidos[name].join("</li><li>") + "</li>";
    if (name.includes(".")) {
      name = name.split(".");
      input = '[name="' + name[0] + "[" + name[1] + ']"]';
    } else {
      input = '[name="' + name + '"]';
    }
    const $input = window.jQuery(input);

    $input.hasClass("is-invalid") ? null : $input.toggleClass("is-invalid");
    $input.next(".invalid-feedback").html(erros);

    if (hasScroll) return;

    $input.get(0).scrollIntoView(true);
    hasScroll = true;
  });

  const errorData = {
    title: "Alguns campos estão incorretos",
    type: "error",
    html: "<p>Corrija os campos marcados em vermelho e reenvie o formulário</p>"
  };

  return window.alertaHtml(errorData, 8000);
};
