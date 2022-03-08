<div class="d-flex flex-row">
    <div class="mr-1" style="flex: 3">
        <select name="op[{{ $field->getSearchField() }}]" class="form-control">
            @foreach ($advancedSearchOperators as $key => $name)
            <option value="{{ $key }}" {{ isset(request()->get('op')[$field->getSearchField()]) && request()->get('op')[$field->getSearchField()] == $key ? 'selected' : '' }}>
                {{ __('listing::listing.operators.'.$name) }}
            </option>
            @endforeach
        </select>
    </div>
    <div style="flex: 5">
        <input type="text" class="form-control" value="{{ request()->get($field->getSearchFieldConverted()) }}" name="{{ $field->getSearchField() }}">
    </div>
</div>
