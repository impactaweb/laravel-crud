<div data-container="loading"></div>
<div class="corpo-listing" id="psListing">
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

    <div class="header row">
        @if($actions)
            <div class="col">
                @foreach ($actions as $action => $params)
                    <?php
                        $fullUrl = request()->url();
                        $url = $fullUrl.$params['url'];
                        $method = $params['method'];
                    ?>
                    <button
                        type="button"
                        class="btn btn-lg btn-default"
                        btn-action-field="{{ $action }}"
                        btn-method="{{ $method }}"
                        btn-url="{{ $url }}"
                        title="{{ $action }}"
                    >
                        @switch($action)
                            @case('editar')
                                <i class="fas fa-pen"></i>
                                @break
                            @case('inserir')
                                <i class="fas fa-plus"></i>
                                @break
                            @case('excluir')
                                <i class="fas fa-trash-alt"></i>
                                @break
                            @default
                                {!! $action !!}
                        @endswitch
                    </button>
                @endforeach
            </div>
        @endif
        <div class="col">
            @include('listing::search')
        </div>
    </div>

    @if($actions)
    <form id="meuForm" action="" method="POST" >
        {{ csrf_field() }}
    @endif
        @if($data)
        <table class="table table-striped table-hover" id="listagemTable">
            {{-- Cabeçalho com as columns --}}
            <thead>
                <tr>
                @foreach($columns as $column => $params)
                    <th scope="col" class="border-top-0">{!! $params['column_link'] !!}</th>
                @endforeach
                </tr>
            </thead>

            {{-- Registros --}}
            @foreach ($data as $item)
                <tr>
                @foreach ($columns as $column => $params)
                    {{-- valor padrão para relacionamento vazio: --}}
                    <?php $valor = config('listing.defaultEmptyRelationValue'); ?>
                    
                    {{-- valor customizado para relacionamento vazio: --}}
                    @if (isset($params['emptyRelationValue']))
                        <?php $valor = $params['emptyRelationValue']; ?>
                    @endif

                    @if (isset($params['relations']))
                        @if ( !is_null($item->{$params['original']}) )
                            <?php $valor = $item->{$params['original']}; ?>
                            @foreach ($params['relations'] as $related)
                                <?php $valor = $valor->{$related}; ?>
                            @endforeach
                        @endif
                    @else
                        <?php $valor = $item->{$column}; ?>
                    @endif
                    <td>{!! $valor !!}</td>
                @endforeach
                </tr>
            @endforeach
        </table>
        @endif
    </form>
    @include('listing::pagination')
</div>
<script rel="text/javascript">
    function handleAllChecked() {
        const $mainCheckbox = document.querySelector('[name="checkbox-listing"]')
        $mainCheckbox.checked
        Array.from(document.querySelectorAll('.listing-checkboxes')).forEach(function($ele) {
            $ele.checked = $mainCheckbox.checked
        })
    }
</script>

