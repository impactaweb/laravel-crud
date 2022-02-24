<div class="col-md-3">
    <select name="op[{{ $field->getSearchField() }}]" class="form-control">
        @foreach ($advancedSearchOperators as $key => $name)
        <option value="{{ $key }}" {{ isset(request()->get('op')[$field->getSearchField()]) && request()->get('op')[$field->getSearchField()] == $key ? 'selected' : '' }}>
            {{ __('listing::listing.operators.'.$name) }}
        </option>
        @endforeach
    </select>
</div>
<div class="col-md-5">
    <input type="text" class="form-control" value="{{ request()->get($field->getSearchFieldConverted()) }}" name="{{ $field->getSearchField() }}">
</div>
