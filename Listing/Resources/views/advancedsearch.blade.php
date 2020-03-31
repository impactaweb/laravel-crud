@if( count($advancedSearchFields) > 0)
<form action="{{ request()->url() }}" method="get" class="frmBuscaAvancada" id="formBuscaAvaçada">
    <input type="hidden" name="pp" value="{{ request()->query('pp') ?? $perPage }}">

    @foreach ($advancedSearchFields as $position => $field)
        <input type="hidden" name="fields[]" value="{{ $field }}">
        <div class="form-group row">
            <div class="col">
                <label class="col-auto col-form-label font-weight-bold" style="text-transform: capitalize;">{{ listingRelationLabel($field) }}:</label>
            </div>
            <div class="col">
                <select name="operators[]" class="form-control">
                    @foreach ($advancedSearchOperators as $key => $name)
                        <option value="{{ $key }}" {{ request()->get('operators')[$position] == $key ? 'selected' : '' }}>
                            {{ __('listing::listing.operators.'.$name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <input type="text" class="form-control" value="{{ request()->get('terms')[$position] }}" name="terms[]">
            </div>
        </div>
    @endforeach

</form>
@endif
