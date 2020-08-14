<form action="{{ request()->url() }}" method="get" class="frmBusca">
    <div class="form-group">
        <div class="input-group mb-2 mr-sm-2">
            @forelse($keepQueryStrings as $i => $field)
                <input type="hidden" 
                       name="{{ $field }}"
                       value="{{ request()->get($field) ?? '' }}" 
                />
            @empty
            @endforelse
            <div class="input-group mb-3">
                <input
                    type="text"
                    class="form-control form-control-lg"
                    name="q"
                    value="{{ request()->get('q') ?? '' }}"
                    placeholder="{{ __('listing::listing.search') }}"
                    aria-label="{{ __('listing::listing.search') }}"
                >
                @if(request()->get('to_field_id'))
                <input hidden name="to_field_id" value="{{ request()->get('to_field_id') }}">
                <input hidden name="from_field_id" value="{{ request()->get('from_field_id') }}">
                <input hidden name="is_popup" value="1">
                @endif

                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <button class="input-group-append btn btn-default p-0 border d-flex align-items-center" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                      <span class="input-group-text bg-transparent border-0" id="basic-addon2"><i class="fas fa-search"></i></span>
                    </button>
                    <div class="btn-group" role="group">
                        <button
                            id="buscaAvancadaBtn"
                            type="button"
                            class="btn btn-lg btn-default dropdown-toggle border"
                            data-toggle="modal"
                            data-target="#modalBuscaAvancada"
                        ></button>
                    </div>
                </div>
                @if($isSearching)
                    @if(request()->get('to_field_id'))
                        <a href="{{ request()->url() }}?is_popup=1&to_field_id={{ request()->get('to_field_id') }}&from_field_id={{ request()->get('from_field_id') }}"
                           class="btn close" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                        </a>
                    @else
                        <a href="{{ request()->url() }}" class="btn close" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>
                    @endif
                @endif
            </div>

        </div>
    </div>
</form>

@if( count($advancedSearchFields) > 0)

    @include('listing::advancedsearch.form')

@endif
