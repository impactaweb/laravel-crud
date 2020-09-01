<div class="col-md-3">
    <select name="op[{{ $field->getName() }}]" class="form-control">
        @if(request()->get('op', null))
            @foreach ($advancedSearchOperators as $key => $name)
            <option value="{{ $key }}" {{ request()->get('op')[$field->getName()] == $key ? 'selected' : '' }}>
                {{ __('listing::listing.operators.'.$name) }}
            </option>
            @endforeach
        @endif
    </select>
</div>
<div class="col-md-5">
    <input type="text" class="form-control" value="{{ request()->get($field->getNameConverted()) }}" name="{{ $field->getName() }}">
</div>
