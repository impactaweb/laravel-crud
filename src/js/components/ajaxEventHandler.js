module.exports = function ajaxEventHandler(element) {
  let $element = $(this);
  let $url = $element.data("ajax-url");
  let $method =
    $element.data("ajax-method").toUpperCase() == "POST" ? "POST" : "GET";
  let $data = $element.data("ajax-data");
  let $dataFields = $element.data("ajax-data-fields");

  // Preenche parâmetros da URL. Exemplo: /url/{chave}
  $element
    .parents("form")
    .find(":input")
    .each(function () {
      if (!$(this).attr("name")) return;
      $url = $url.replace("{" + $(this).attr("name") + "}", $(this).val());
    });

  // Pega os parametros do dataFields e cada elemento vira um parametro para
  // submeter ao formulário, sendo seu valor representando o nome de um :input do form.
  Object.keys($dataFields).forEach(function (key) {
    $data[key] = $('[name="' + $dataFields[key] + '"]').val();
  });

  // Inicia a requisição, com o contexto do elemento principal (input que chama o ajax)
  $.ajax({
    method: $method,
    url: $url,
    data: $data,
    dataType: "json",
    context: $element,
  })
    .done(function (json) {
      let $fieldsOptions = $(this).data("ajax-fields-options");
      Object.keys($fieldsOptions).forEach(function (key) {
        const $field = $('[name="' + key + '"]');
        if (!$field.length) return;

        // Perfil de dado esperado
        //[{ value: 1, label: 'UF'}, ...]
        if (Array.isArray(json[$fieldsOptions[key]])) {
          const template = json[$fieldsOptions[key]].reduce(
            (prev, cur) =>
              prev + `<option value="${cur.value}">${cur.label}</option>`,
            ""
          );
          $field.html(template);
        }
      });

      // Para cada campo configurado em fields, alimenta o :input correspondente
      let $fields = $(this).data("ajax-fields");
      Object.keys($fields).forEach(function (key) {
        const $field = $('[name="' + key + '"]');
        if (!$field.length) return;

        if ($field.is("div")) {
          $field.replaceWith(json[$fields[key]]);
          return;
        }

        if ($field.is("select") || $field.is("input")) {
          $field.val(json[$fields[key]]);
        }
      });
    })
    .fail(function () {
      alert("error");
    })
    .always(function () {
      // alert( "complete" );
    });
};
