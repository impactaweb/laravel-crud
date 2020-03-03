<form action="{{ request()->url() }}" method="get" class="frmBuscaAvancada">
@foreach ($advancedSearchFields as $position => $field)
  
  <select name="fields[]">
        @foreach ($advancedSearchFields as $field)
            <option value="{{ $field }}" {{ request()->get('fields')[$position] == $field ? 'selected' : '' }} >{{ $field }}</option>
        @endforeach
   </select>
   <select name="operators[]">
        @foreach ($advancedSearchOperators as $key => $name)
            <option value="{{ $key }}" {{ request()->get('operators')[$position] == $key ? 'selected' : '' }} >{{ __('listing::listing.operators.'.$name) }}</option>
        @endforeach
   </select> 
   <input value="{{ request()->get('terms')[$position] }}" name="terms[]" />
   
   <hr>
@endforeach
<button type="submit">buscar</button>

</form>
