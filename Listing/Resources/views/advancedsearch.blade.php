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

    @foreach ($advancedSearchFields as $position => $field)
        <div class="form-group row">
            <div class="col">
                <label class="col-auto col-form-label font-weight-bold" style="text-transform: capitalize;">{{ $field->getLabel() }}:</label>
            </div>
            <div class="col">
                <select name="op[{{ $field->getName() }}]" class="form-control">
                    @foreach ($advancedSearchOperators as $key => $name)
                        <option value="{{ $key }}" {{ request()->get('op')[$field->getName()] == $key ? 'selected' : '' }}>
                            {{ __('listing::listing.operators.'.$name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <input type="text" class="form-control" value="{{ request()->get($field->getNameConverted()) }}" name="{{ $field->getName() }}">
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
        <button type="submit" class="btn btn-primary" data-avancada="buscar">Buscar</button>
    </div>
</form>
</div>
</div>
</div>
