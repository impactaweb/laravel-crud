module.exports = function asyncFileUpload() {
  const $inputs = jQuery('[data-file="async-upload"]');
  let inProgrees = false;
  const method = jQuery('input[name="_method"').val();
  const action = jQuery("#form").attr("action");
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
      const $info = jQuery(`[mock-name="${key}"]`)
        .parent("div")
        .find("[actions-container]");
      $info
        .find("[link-container]")
        .attr("href", success[key].url)
        .css("display", "none");
      $info.css("display", "block");
      $info.find("[destroy-file]").attr("destroy-file", success[key].hashName);
      jQuery(`input[name="${key}"]`).val(success[key].hashName);
    });

    $info.css("display", "block");
    inProgrees = false;
  }

  function handleFailure() {
    const $progressContainer = jQuery(this).next(".progress.mt-1");
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
    const $progressContainer = jQuery(this).next(".progress.mt-1");
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
    jQuery($input)
      .parent(".form-group")
      .find("[destroy-file]")
      .click(function (e) {
        e.preventDefault();
        const hash = jQuery(this).attr("destroy-file");
        const $inputFile = jQuery($input);
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
};
