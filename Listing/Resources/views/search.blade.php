
<form action="{{ request()->url() }}" method="get" class="frmBusca" />
    @if(request()->get('q') !== null) 
        {{-- para limpar a busca: --}}
        <a href="{{ request()->url() }}" title="limpar busca" >X</a> 
    @endif
    <input type="text" name="q" value="{{ request()->get('q') }}" placeholder="{{ __('listing::listing.search') }}" />
    <button type="submit" >{{ __('listing::listing.search') }}</button>
</form>