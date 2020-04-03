<div data-container="loading"></div>
<div class="corpo-listing" id="psListing">

    @include('listing::remove-modal')

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
                        class="btn btn-lg btn-default tooltips"
                        btn-action-field="{{ $action }}"
                        btn-method="{{ $method }}"
                        btn-url="{{ $url }}"
                        title="{{ $action }}"
                        data-toggle="tooltip" data-placement="top" 
                    >
                        @switch($action)
                            @case('editar')
                                <i class="far fa-edit"></i>
                                @break
                            @case('inserir')
                                <i class="far fa-plus-square"></i>
                                @break
                            @case('excluir')
                                <i class="far fa-trash-alt"></i>
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

    <form id="meuForm" action="" method="POST" >
        {{ csrf_field() }}

    @if($data && $columns)
    <table class="table table-striped table-hover table-sm" id="listagemTable" data-redir="{{ url()->full() }}">
        {{-- Cabeçalho com as columns --}}
        <thead>
            <tr>
            @foreach($columns as $column => $params)
                <th scope="col" class="border-top-0">{!! $params['column_link'] !!}</th>
            @endforeach
            </tr>
        </thead>

        {{-- Registros --}}
        @forelse ($data as $item)
            <tr>
            @foreach ($columns as $column => $params)
                <td>{!! $item->$column !!}</td>
            @endforeach
            </tr>
        @empty
            <tr class="empty">
                <td colspan="{{ count($columns) }}">Nenhum item encontrado</td>
            </tr>
        @endforelse
    </table>
    @endif

    </form>
    @include('listing::pagination')
</div>

<style>
</style>
