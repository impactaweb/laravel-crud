<div class="modal fade show" id="modalBuscaAvancada" tabindex="-1" role="dialog" aria-labelledby="modalBuscaAvancadaLabel" aria-modal="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<form action="" method="get" class="frmBuscaAvancada" id="formBuscaAvacada">
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

    @foreach ($advancedSearchFields as $position => $field)
        <div class="form-group row">
            <div class="col-md-4">
                <label class="col-auto col-form-label font-weight-bold" style="text-transform: capitalize;">{{ $field->getLabel() }}:</label>
            </div>

            @switch($field->getType())
                @case('flag')
                @case('select')
                    @include('listing::advancedsearch.' . $field->getType())
                    @break
                @default
                    @include('listing::advancedsearch.text')
            @endswitch
        </div>
    @endforeach

    </div>
    <div class="modal-footer">
        <a
            href="{{ request()->url() }}"
            title="limpar busca"
            class="btn btn-default"
        >Limpar</a>
        <button type="submit" class="btn btn-primary" data-avancada="buscar">Buscar</button>
    </div>
</form>
</div>
</div>
</div>
