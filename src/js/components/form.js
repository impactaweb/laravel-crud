"use strict";

const asyncFileUpload = require("./asyncFileUpload");
const handleFailureSendForm = require("./handleFailureSendForm");
const handleSuccessSendForm = require("./handleSuccessSendForm");
const Swal = require("sweetalert2");

jQuery(document).ready(function() {
  const $ = window.jQuery;
  const axios = window.axios;

  if (!$("[data-its-form]").length) return;

  $('[data-toggle="tooltip"]').tooltip();

  const $deleteFiles = $("[data-destroy]");

  $("#form").validate({
    submitHandler: function(form) {
      $(form).submit(function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
      });

      window.initLoading();

      const ACTION = form.getAttribute("action");

      $.ajax({
        url: ACTION,
        type: "POST",
        processData: false,
        contentType: false,
        data: new FormData(form)
      })
        .done(handleSuccessSendForm)
        .fail(handleFailureSendForm);
    },
    errorElement: "li",
    errorPlacement: function(error, element) {
      error.appendTo(
        element
          .parent()
          .children()
          .last()
      );
      $(
        element
          .parent()
          .children()
          .last()
      ).css("display", "block");
    }
  });

  $("select[multiple]").each(function(idx, ele) {
    $(ele).multiselect({
      enableClickableOptGroups: true,
      enableCollapsibleOptGroups: true,
      includeSelectAllOption: true,
      enableCaseInsensitiveFiltering: !!ele.getAttribute("enable-filter"),
      collapseOptGroupsByDefault: true,
      maxHeight: 400
    });
  });

  $deleteFiles.each(function(idx, $link) {
    $link.onclick = function(e) {
      window.initLoading();
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
          _token: $('[name="_token"]').val()
        },
        function(jsonData) {
          if (jsonData.error) {
            alert(jsonData.error);
            return;
          }

          window.finishLoading();
          $($link)
            .parent("span")
            .prev("input")
            .val(null);
          $($link)
            .parent("span")
            .remove();
        }
      )
        .fail(function(jqXHR) {
          alert(
            jqXHR.responseJSON.error
              ? jqXHR.responseJSON.error
              : "Falha ao excluir o arquivo."
          );
        })
        .always(function() {
          window.finishLoading();
        });
    };
  });

  /**
   * Chamando componente de upload asyncrono
   */
  asyncFileUpload();

  /**
   * Configuração do show-rules
   */
  let inputsToBind = {};

  $("div[data-show-rules]").each(function() {
    let rules = $(this).data("show-rules");
    let hideField = $(this).data("field-name");

    Object.keys(rules).forEach(function(ruleField) {
      inputsToBind[ruleField] = true;
      let inputToHandle = $(":input[name='" + ruleField + "']");
      if (!inputToHandle.length) {
        return;
      }

      let setEventChange = false;
      let fieldHideRules;
      if (inputToHandle.data("hide-rules")) {
        fieldHideRules = inputToHandle.data("hide-rules");
      } else {
        fieldHideRules = {};
        setEventChange = true;
      }

      fieldHideRules[hideField] = rules[ruleField];

      inputToHandle.data("hide-rules", fieldHideRules);

      if (setEventChange) {
        inputToHandle.change(function() {
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

  window.onpageshow = function(event) {
    if (event.persisted) {
      window.location.reload();
    }
  };

});
