module.exports = function handleSuccessSendForm(res) {
  if (!res.url) {
    alert(
      "Ops, pedimos desculpas pelo erro, entre em contato com o suporte para que possamos fazer os ajustes."
    );
    jQuery('[data-container="loading"]').html("");
    return;
  }

  const url = res.url;

  window.location.href = url;
};
