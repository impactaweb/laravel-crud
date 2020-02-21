<div class="corpo-listing" >
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
                        onclick="handleActionClick('{{ $url }}', '{{ $method }}');"
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
        <table class="table table-striped">
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
                    <td>{!! $item->$column !!}</td>
                @endforeach
                </tr>
            @endforeach
        </table>
    @if($actions)
    </form>
    @endif
    @include('listing::pagination')
</div>
<script rel="text/javascript">
    function handleActionClick(action, method) {
        const $form = document.querySelector('#meuForm')
        $form.setAttribute('action', action)
        $form.setAttribute('method', method)
        $form.submit()
    }

    function handleAllChecked() {
        const $mainCheckbox = document.querySelector('[name="checkbox-listing"]')
        $mainCheckbox.checked
        Array.from(document.querySelectorAll('.listing-checkboxes')).forEach(function($ele) {
            $ele.checked = $mainCheckbox.checked
        })
    }
</script>

