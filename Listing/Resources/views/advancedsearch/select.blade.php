<input type="hidden" name="op[{{ $field->getName() }}]" value="=" />
<div class="col-md-8">
    <select class="form-control" name="{{ $field->getName() }}">
        <option value=""></option>
        @foreach ($field->getSearchOptions() as $index => $value)
            <option value="{{ $index }}"{{ request()->get($field->getNameConverted()) == $index ? ' selected' : '' }}>{{ $value }}</option>
        @endforeach
    </select>
</div>
