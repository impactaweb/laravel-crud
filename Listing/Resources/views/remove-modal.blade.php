<button type="button" class="btn d-none" data-toggle="modal" data-target="#excluirModal" data-excluir="abrirModal"></button>

<div class="modal fade" id="excluirModal" tabindex="-1" role="dialog" aria-labelledby="excluirModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="excluirModalLabel">Excluir</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir os itens selecionados?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" data-excluir="cancel">Cancelar</button>
                <button type="button" class="btn btn-danger" data-excluir="confirm">Excluir</button>
            </div>
        </div>
    </div>
</div>
