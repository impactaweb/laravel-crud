<form action="{{ request()->url() }}" method="get" class="frmBuscaAvancada">
    <input type="hidden" name="pp" value="{{ request()->get('pp') }}" />
 
    @foreach ($advancedSearchFields as $position => $field)
    
    {{-- Nome do campo na tabela: --}}
    <input type="hidden" name="fields[]" value="{{ $field}}" />
    <span>{{ $field }}: </span>

    {{-- Nome do operador: --}}
    <select name="operators[]">
            @foreach ($advancedSearchOperators as $key => $name)
                <option value="{{ $key }}" {{ request()->get('operators')[$position] == $key ? 'selected' : '' }} >{{ __('listing::listing.operators.'.$name) }}</option>
            @endforeach
    </select> 

    {{-- Valor digitado pelo usuário: --}}
    <input value="{{ request()->get('terms')[$position] }}" name="terms[]" />
    
    <hr>
    @endforeach
    <button type="submit">buscar</button>

</form>
