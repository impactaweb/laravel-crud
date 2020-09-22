<input type="hidden" name="op[{{ $field->getName() }}]" value="=" />
<div class="col-md-8">
    <select class="form-control" name="{{ $field->getName() }}">
        <option value=""></option>
        <option value="1"{{ request()->get($field->getNameConverted()) == '1' ? ' selected' : '' }}>Sim</option>
        <option value="0"{{ request()->get($field->getNameConverted()) == '0' ? ' selected' : '' }}>Não</option>
    </select>
</div>
