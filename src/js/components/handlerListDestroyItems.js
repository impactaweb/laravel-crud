/**
 * Excluir items da lista de forma assincrona e
 * pusha o estado atual da pagina.
 *
 * @param {string} url - Url de exclusao dos items
 * @param {number[]} ids - ids a serem excluidos
 * @return {Promise<void>}
 */
async function handlerListDestroyItems(url, ids) {
  try {
    const { href } = location;
    const _token = document.querySelector('[name="_token"]').value;

    if (!_token) {
      throw new Error("Token não definido");
    }

    const { data } = await window.axios.delete(url, {
      _token
    });

    const successIds = data.success.map(item =>
      Number(item.replace(/\D/gi, ""))
    );

    handlerItems(ids, successIds);

    window.Swal.fire({
      title: "Resultado",
      icon: "info",
      html: makeContent(data),
      focusConfirm: false,
      confirmButtonText: "Ok",
      confirmButtonAriaLabel: "Okay"
    });
  } catch (err) {
    //TODO: Acesso a variavel de ambiente para mostrar o erro em ambiente local;
    alert("Erro ao excluir items");
  } finally {
    window.finishLoading();
  }
}

/**
 * Exclui e desmarca os Items selecionados
 *
 * @param {number[]} ids - Ids dos items selecionados
 * @param {number[]} successIds - Ids dos items excluidos
 */
function handlerItems(ids, successIds) {
  ids.forEach(id => {
    const numberId = parseInt(id);
    if (!numberId) return;

    const $tr = jQuery(`input[value="${numberId}"]`)
      .prop("checked", false)
      .parent()
      .parent();
    $tr.removeClass("active");

    if (successIds.indexOf(numberId) !== -1) {
      $tr.remove();
    }
  });
}

/**
 * Constrói o conteúdo a ser aprensentado no sweetalert
 *
 * @param {obejct} data - Resposta retornada pelo backend
 */
function makeContent(data) {
  let content = data.success.reduce(
    (prev, item) =>
      `${prev}<li class="text-success" style="font-size: 13px;">${item}</li>`,
    '<ul style="max-width: 220px;">'
  );

  content = data.erros.reduce(
    (prev, item) =>
      `${prev}<li class="text-danger" style="font-size: 13px;"></li>`,
    content
  );
  content = data.warnings.reduce(
    (prev, item) =>
      `${prev}<li class="text-warning" style="font-size: 13px;"></li>`,
    content
  );

  return `<div class="d-flex justify-content-center">${content}</ul><div>`;
}

module.exports = handlerListDestroyItems;
