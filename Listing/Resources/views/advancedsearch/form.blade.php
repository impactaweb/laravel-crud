<div class="modal fade show" id="modalBuscaAvancada" tabindex="-1" role="dialog" aria-labelledby="modalBuscaAvancadaLabel" aria-modal="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<form method="get" class="frmBuscaAvancada" id="formBuscaAvacada">
    <div class="modal-header">
        <h2 class="modal-title" id="modalBuscaAvancadaLabel">Busca avancada</h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    <div class="modal-body" style="max-height: 450px; overflow-y: scroll;">

    @if(request()->get('to_field_id'))
        <input hidden name="to_field_id" value="{{ request()->get('to_field_id') }}">
        <input hidden name="from_field_id" value="{{ request()->get('from_field_id') }}">
        <input hidden name="is_popup" value="1">
    @endif

    @foreach(request()->except('middleware') as $item => $valor)
        @if(in_array($item, ['page', 'pp', 'q']) && !is_array($valor) && !empty($valor))
            <input type="hidden" name="{{ $item }}" value="{{ $valor }}" />
        @endif
    @endforeach

    @foreach ($advancedSearchFields as $position => $field)
        <div class="d-flex flex-row justify-content-center align-items-center mb-2">
            <div style="width: 8rem;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    overflow: hidden;
                ">
                <span class="font-weight-bold" style="
                text-transform: capitalize;
                font-size: 0.8rem ">
                    {{ $field->getLabel() }}:
                </span>
            </div>
            <div style="flex: 1">
                @switch($field->getType())
                    @case('flag')
                    @case('hidden')
                    @case('select')
                        @include('listing::advancedsearch.' . $field->getType())
                        @break
                    @case('date')
                        @include('listing::advancedsearch.' . $field->getType())
                        @break
                    @default
                        @include('listing::advancedsearch.text')
                @endswitch
            </div>
        </div>
    @endforeach

    </div>
    <div class="modal-footer">
        <a
            href="{{ request()->url() }}"
            title="limpar busca"
            class="btn btn-default"
        >Limpar</a>
        <button type="submit" class="btn btn-primary" data-avancada="buscar">
            <i class="fas fa-search"></i>
            Buscar
        </button>
    </div>
</form>
</div>
</div>
</div>
