<div class="d-flex flex-row">
    <input type="date" class="form-control"
           value="{{ request()->get($field->getSearchFieldConverted()) }}"
           name="{{ $field->getSearchField() }}">
</div>
