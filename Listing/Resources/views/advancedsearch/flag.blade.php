<input type="hidden" name="op[{{ $field->getSearchField() }}]" value="=" />
<div class="col-md-8">
    <select class="form-control" name="{{ $field->getSearchField() }}">
        <option value=""></option>
        <option value="1"{{ request()->get($field->getSearchFieldConverted()) == '1' ? ' selected' : '' }}>Sim</option>
        <option value="0"{{ request()->get($field->getSearchFieldConverted()) == '0' ? ' selected' : '' }}>Não</option>
    </select>
</div>
