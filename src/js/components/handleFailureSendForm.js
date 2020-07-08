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

  `
  {
    "message": "The given data was invalid.",
    "errors": {
      "tipoisencao.teste.resultado": [
          "O campo tipoisencao.teste.resultado deve ser um endereco"
        ]
      }
    }
  `

  // Erros enviados pela request do laravel
  Object.keys(camposInvalidos).forEach(function(name) {
    let inputName;

    if (name.includes(".")) {
      const paths = name.replace('..', '.').split(".");
      inputName = `[name="${paths.reduce((prev, cur) =>  `${prev}[${cur}]`)}"]`; // js love reduce + arrow function
    } else {
      inputName = '[name="' + name + '"]';
    }

    const $input = window.jQuery(inputName);
    const $label = $input.parent().prev();
    let erros = null;
    if ($label.is('label')) {
      erros = camposInvalidos[name].reduce( (prev, cur) => `${prev}<li>${cur
        .replace(
            name,
            $label.html().replace(/(\n\s+ | \s+\n+)+/gim, // regex <3
              ''
          )
        )}</li>`,'');
    } else {
      erros = camposInvalidos[name].reduce( (prev, cur) => `${prev}<li>${cur}</li>`,'');
    }

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
