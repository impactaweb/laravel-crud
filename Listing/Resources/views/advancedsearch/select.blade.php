<input type="hidden" name="op[{{ $field->getSearchField() }}]" value="=" />
<div>
    <select class="form-control" name="{{ $field->getSearchField() }}">
        <option value=""></option>
        @foreach ($field->getSearchOptions() as $index => $value)
            <option value="{{ $index }}"{{ request()->get($field->getSearchFieldConverted()) == $index ? ' selected' : '' }}>{{ $value }}</option>
        @endforeach
    </select>
</div>
