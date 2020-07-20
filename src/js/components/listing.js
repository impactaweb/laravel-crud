"use strict";

const $ = window.jQuery;

$(document).ready(function() {
  const $form = $("#listingForm");

  $(".actionButton").click(function() {
    let url = $(this).data("url");
    let _method = $(this).data("method");
    let confirmationText = $(this).data("confirmation");
    let continueFunction = null;
    let method = $(this).data("method") == "GET" ? "GET" : "POST";
    if (!$.inArray(method, ["GET", "POST", "PUT", "PATCH", "DELETE"]) == -1) {
      method = "GET";
    }

    let $checkboxes = $(".listing-checkboxes:checked");

    if (
      (url.indexOf("{id}") >= 0 ||
        url.indexOf("{ids}") >= 0 ||
        method != "GET") &&
      !($checkboxes.length > 0)
    ) {
      alert("Selecione no minimo 1 item da listagem");
      return;
    }

    let ids = [];
    $checkboxes.each(function() {
      ids.push($(this).val());
    });
    let id = ids[0];
    let idsFormatado = ids.join(",");

    url = url.replace("{id}", id).replace("{ids}", idsFormatado);

    if (method == "GET") {
      continueFunction = function() {
        listagemLoading();
        window.location.href = url;
      };
    } else {
      $form.prop("action", url);
      $form.prop("method", method);
      $form.find('input[name="_method"]').val(_method);
      continueFunction = function() {
        $form.submit();
      };
    }

    if (confirmationText.length > 0) {
      $("#confirmationModal")
        .data("executar", continueFunction)
        .modal("show");
      $("#confirmationModal .modal-body").html(confirmationText);
      $("#confirmationModal .btnConfirm").click(function() {
        const func = $("#confirmationModal").data("executar");
        func();
        e.preventDefault();
      });
    } else {
      continueFunction();
    }
  });

  function listagemLoading(open = true) {
    if (!open) {
      window.finishLoading();
      return;
    }
    window.initLoading();
  }

  const $checkboxs = $("input.listing-checkboxes");

  function handleCheckboxChange(e) {
    if (this.checked) {
      $(this.parentNode.parentNode).addClass("active");
      return;
    }
    $(this.parentNode.parentNode).removeClass("active");
  }

  function handleBuscaAvancada() {
    $("#formBuscaAvacada").submit();
  }

  function handleTdClick(e) {

    // Verifica se é o clique para o SearchField
    const $tr = $(this).parents("tr")
    let data_search = $tr.attr('data-search-field')
    if (data_search) {
      parent.$('#modal-search-' + data_search).modal('hide')
      parent.$('#c-' + data_search).val($tr.attr('data-search-value'))
      return;
    }

    // Verifica se foi clicado em elementos
    if (
      $(e.target).is("a") ||
      $(e.target).is("img") ||
      $(e.target).is("input") ||
      $(e.target).is("button")
    ) {
      return;
    }

    const $checkbox = $(this)
      .parents("tr")
      .find(".listing-checkboxes:first");

    if ($checkbox.is(":checked")) {
      $checkbox.prop("checked", false);
      $(this)
        .parents("tr")
        .removeClass("active");
    } else {
      $checkbox.prop("checked", "checked");
      $(this)
        .parents("tr")
        .addClass("active");
    }
  }

  function handleDblClick(e) {
    if (
      $(e.target).is("a") ||
      $(e.target).is("img") ||
      $(e.target).is("input") ||
      $(e.target).is("button")
    ) {
      return;
    }

    const $item = $(this);

    $(".listing-checkboxes")
      .prop("checked", false)
      .parents("tr")
      .removeClass("active");

    $item
      .addClass("active")
      .find(".listing-checkboxes")
      .prop("checked", "checked");

    if ($('.actionButton[data-verb="edit"]').length > 0) {
      $('.actionButton[data-verb="edit"]').trigger("click");
    }
  }

  // Funcão para atualizar a flag de um registro:
  function handleListingFlag() {
    let primaryKeyValue = $(this)
      .parents("tr")
      .find(".listing-checkboxes")
      .val();

    let newFlag = null;
    if ($(this).parents("td").find("[data-double-flag]").length > 1) {
      newFlag = $(this).hasClass("flag-on") ? 1 : 0;
    } else {
      newFlag = $(this).hasClass("flag-on") ? 0 : 1;
    }

    let fieldName = $(this).data("field");

    listagemLoading();

    let postUrl =
      window.location.pathname.replace(/\/+$/, "") +
      "/" +
      primaryKeyValue +
      "/updateflag";

    let postData = {
      //'_method': 'PUT',
      responseFormat: "json",
      listingFlagField: fieldName,
      newFlag: newFlag
    };

    $.ajax({

      url: postUrl,
      method: "POST",
      data: postData,
      context: $(this),
      dataType: "json"

    }).done(function(jsonData) {

        if (jsonData.error) {
          alert(jsonData.error);
          return;
        }

        if ($(this).parents("td").find("[data-double-flag]").length > 1) {
          jsonData.flag === "1"
            ? $(this).parents('td').find('[data-double-flag="off"]').remove()
            : $(this).parents('td').find('[data-double-flag="on"]').remove();

          $(this).attr("data-double-flag", "");
        }

        $(this)
          .html(jsonData.flag)
          .removeClass("flag-on")
          .removeClass("flag-off")
          .addClass(jsonData.flag == "1" ? "flag-on" : "flag-off");
    })
    .fail(function(jqXHR) {
      alert(
        jqXHR.responseJSON.error
          ? jqXHR.responseJSON.error
          : "Erro ao alterar."
      );s
    })
    .always(function() {
      listagemLoading(false);
    });


  }

  function handleAllChecked() {
    if ($('[name="checkbox-listing"]').is(":checked")) {
      $(".listing-checkboxes")
        .prop("checked", "checked")
        .parents("tr")
        .addClass("active");
    } else {
      $(".listing-checkboxes")
        .prop("checked", false)
        .parents("tr")
        .removeClass("active");
    }
  }

  if ($checkboxs.length > 0) {
    $("#listagemTable tbody tr:not(.empty) td").click(handleTdClick);
    $("#listagemTable tbody tr:not(.empty)").dblclick(handleDblClick);
  }
  $("input.listing-checkboxes").change(handleCheckboxChange);
  $("#listagemTable").checkboxes("range", true);
  $('[data-avancada="buscar"]').click(handleBuscaAvancada);
  $("a.flagItem").click(handleListingFlag);
  $('input[name="checkbox-listing"]').click(handleAllChecked);

  $checkboxs.each(function(idx, $item) {
    $item.checked = false;
  });

  // Atilet tooltip para todos os actions
  if (
    !(
      $('[data-toggle="tooltip"]:first').data &&
      $('[data-toggle="tooltip"]:first').data("bs.tooltip")
    )
  ) {
    $('[data-toggle="tooltip"]').tooltip();
  }

  $("#listingForm th order-asc").addClass("fas fa-sort-up");
  $("#listingForm th order-desc").addClass("fas fa-sort-down");
});

window.onpageshow = function(event) {
  if (event.persisted) {
    window.location.reload();
  }
};
