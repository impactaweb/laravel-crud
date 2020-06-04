const ajaxEventHandler = require("./ajaxEventHandler")(function (
  $,
  axios,
  ajaxEventHandler
) {
  if (!$("[data-its-form]").length) return;

  $('[data-toggle="tooltip"]').tooltip();

  const $alert = document.querySelector("[data-expect-alert]");
  const $deleteFiles = $("[data-destroy]");

  $("#form").validate({
    submitHandler: function (form) {
      $(form).submit(function (e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
      });

      $('[data-container="loading"]').html(`
              <div class="loading-container fixed">
                  <div class="lds-roller">
                      <div></div>
                      <div></div>
                      <div></div>
                      <div></div>
                  </div>
              </div>
          `);

      const ACTION = form.getAttribute("action");

      $.ajax({
        url: ACTION,
        type: "POST",
        processData: false,
        contentType: false,
        data: new FormData(form),
      })
        .done(handleSuccess)
        .fail(handleFailure);
    },
    errorElement: "li",
    errorPlacement: function (error, element) {
      error.appendTo(element.parent().children().last());
    },
  });

  function handleSuccess(res) {
    if (!res.url) {
      alert(
        "Ops, pedimos desculpas pelo erro, entre em contato com o suporte para que possamos fazer os ajustes."
      );
      $('[data-container="loading"]').html("");
      return;
    }

    const url = res.url;

    window.location.href = url;
  }

  function handleFailure(error) {
    $('[data-container="loading"]').html("");
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

    Object.keys(camposInvalidos).forEach(function (name) {
      let input;
      const erros = "<li>" + camposInvalidos[name].join("</li><li>") + "</li>";
      if (name.includes(".")) {
        name = name.split(".");
        input = '[name="' + name[0] + "[" + name[1] + ']"]';
      } else {
        input = '[name="' + name + '"]';
      }
      const $input = $(input);

      $input.hasClass("is-invalid") ? null : $input.toggleClass("is-invalid");
      $input.next(".invalid-feedback").html(erros);

      if (hasScroll) return;

      $input.get(0).scrollIntoView(true);
      hasScroll = true;
    });

    $alert.innerHTML = alertMessage;
    $alert.scrollIntoView();
  }

  $("select[multiple]").each(function (idx, ele) {
    $(ele).multiselect({
      enableClickableOptGroups: true,
      enableCollapsibleOptGroups: true,
      includeSelectAllOption: true,
      enableCaseInsensitiveFiltering: !!ele.getAttribute("enable-filter"),
      collapseOptGroupsByDefault: true,
      maxHeight: 400,
    });
  });

  $deleteFiles.each(function (idx, $link) {
    $link.onclick = function (e) {
      $('[data-container="loading"]').html(`
              <div class="loading-container fixed">
                  <div class="lds-roller">
                      <div></div>
                      <div></div>
                      <div></div>
                      <div></div>
                  </div>
              </div>
          `);
      e.preventDefault();
      e.stopPropagation();
      const path = $link.getAttribute("data-destroy");
      const fieldFile = $link.getAttribute("data-file-field");
      const $mainForm = $("#form");
      const id = $("[data-id]").val();

      if (!path || !fieldFile || !id) return;

      $.post(
        `${window.location.pathname.replace(
          "/editar",
          ""
        )}/destroyfile?model_id=${id}&file_delete=${fieldFile}`,
        {
          _token: $('[name="_token"]').val(),
        },
        function (jsonData) {
          if (jsonData.error) {
            alert(jsonData.error);
            return;
          }

          $('[data-container="loading"]').html("");
          $($link).parent("span").prev("input").val(null);
          $($link).parent("span").remove();
        }
      )
        .fail(function (jqXHR) {
          alert(
            jqXHR.responseJSON.error
              ? jqXHR.responseJSON.error
              : "Falha ao excluir o arquivo."
          );
        })
        .always(function () {
          $('[data-container="loading"]').html("");
        });
    };
  });

  function asyncFileUpload() {
    const $inputs = $('[data-file="async-upload"]');
    let inProgrees = false;
    const method = $('input[name="_method"').val();
    const action = $("#form").attr("action");
    if (!$inputs.length) return;

    function handleSuccess(res) {
      const { error, success } = res.data;
      if (error) {
        const items = Object.values(error).reduce(function (prev, cur) {
          return prev + ", " + cur;
        }, "");
        alert("Falha no envio dos seguintes arquivos: " + items);
        this.value = null;
        this.files.length = 0;
        return;
      }

      Object.keys(success).forEach(function (key) {
        const $info = $(`[mock-name="${key}"]`)
          .parent("div")
          .find("[actions-container]");
        $info
          .find("[link-container]")
          .attr("href", success[key].url)
          .css("display", "none");
        $info.css("display", "block");
        $info
          .find("[destroy-file]")
          .attr("destroy-file", success[key].hashName);
        $(`input[name="${key}"]`).val(success[key].hashName);
      });

      $info.css("display", "block");
      inProgrees = false;
    }

    function handleFailure() {
      const $progressContainer = $(this).next(".progress.mt-1");
      $progressContainer.find(".progress-bar").css("width", "0%");
      $progressContainer.css("display", "none");
      inProgrees = false;
    }

    /**
     * Handle input file
     * @param {event} e
     */
    function handleChange(e) {
      const files = new FormData();
      files.append(this.getAttribute("mock-name"), this.files[0]);
      const $progressContainer = $(this).next(".progress.mt-1");
      $progressContainer.css("display", "block");
      const $progress = $progressContainer.find(".progress-bar");
      $progress.css("width", "0%");
      inProgrees = true;
      axios[method.toLowerCase()](action, files, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
        onUploadProgress: function (progressEvent) {
          let percentCompleted = Math.round(
            (progressEvent.loaded * 100) / progressEvent.total
          );
          $progress
            .css("width", percentCompleted + "%")
            .html(
              percentCompleted === 100 ? "Concluído" : percentCompleted + "%"
            );
        },
      })
        .then(handleSuccess)
        .catch(handleFailure);
    }

    $inputs.each(function (idx, $input) {
      let accept = $input.getAttribute("accept");
      let extensions = null;
      if (accept) {
        accept = accept.split(",");
        extensions = accept.map(function (ext) {
          return ext.replace(/(\w*\/?\.?)(\w+[-?\w\.]*)/im, "$2");
        });

        $input.setAttribute("data-ext", extensions.join(","));
      }

      $input.onchange = handleChange;
      $($input)
        .parent(".form-group")
        .find("[destroy-file]")
        .click(function (e) {
          e.preventDefault();
          const hash = $(this).attr("destroy-file");
          const $inputFile = $($input);
          const field = $inputFile.attr("mock-name");
          if (!hash) return;
          axios
            .delete(`${action}?field=${field}&hash=${hash}`)
            .then(function (res) {
              $inputFile
                .parent("div")
                .find("[actions-container]")
                .css("display", "none");
              $inputFile.parent("div").find('input[type="hidden"]').val(null);
              $inputFile.parent("div").find(".progress").css("display", "none");
              $inputFile.val(null);
            })
            .catch(function () {
              alert("Falha ao excluir");
            });
        });
    });
  }

  asyncFileUpload();

  /**
   * Configuração do show-rules
   */
  let inputsToBind = {};

  $("div[data-show-rules]").each(function () {
    let rules = $(this).data("show-rules");
    let hideField = $(this).data("field-name");

    Object.keys(rules).forEach(function (ruleField) {
      inputsToBind[ruleField] = true;
      let inputToHandle = $(":input[name='" + ruleField + "']");
      if (!inputToHandle.length) {
        return;
      }

      let setEventChange = false;
      if (inputToHandle.data("hide-rules")) {
        let fieldHideRules = inputToHandle.data("hide-rules");
      } else {
        let fieldHideRules = {};
        setEventChange = true;
      }

      fieldHideRules[hideField] = rules[ruleField];
      inputToHandle.data("hide-rules", fieldHideRules);

      if (setEventChange) {
        inputToHandle.change(function () {
          if ($(this).is(":radio:not(:checked)")) {
            return;
          }

          let inputValue = $(this).val();
          let hideRules = $(this).data("hide-rules");
          for (let field in hideRules) {
            let fieldBlockToHide = $("div[data-field-name='" + field + "']");
            let valuesToCheck = hideRules[field];

            if (!(typeof valuesToCheck == "object")) {
              valuesToCheck = [valuesToCheck];
            }

            let eventShow = false;
            for (let index in valuesToCheck) {
              if (inputValue == valuesToCheck[index]) {
                eventShow = true;
              }
            }

            eventShow ? fieldBlockToHide.show() : fieldBlockToHide.hide();
          }
        });
      }
    });
  });

  // Aciona o evento change dos inputs que deverão ser monitorados
  for (let field in inputsToBind) {
    $(":input[name='" + field + "']").trigger("change");
  }

  /**
   * -----------------------------------------------------------------------------------------------
   * Ajax para preenchimento de campos
   * -----------------------------------------------------------------------------------------------
   */

  $("[data-ajax-url]").each(function () {
    switch ($(this).data("ajax-event")) {
      case "click":
        $(this).click(ajaxEventHandler);
        break;
      default:
        $(this).change(ajaxEventHandler);
    }
  });
})(jQuery, axios, ajaxEventHandler);

window.onpageshow = function (event) {
  if (event.persisted) {
    window.location.reload();
  }
};
