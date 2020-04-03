@if($pagination && $data && $data->total() > 0)

    <form class="" action="{{ request()->url() }}" method="get"/>

        {{-- para manter as querystrings ao submeter o form: --}}
        @foreach(request()->query() as $item => $valor)
            @if(!in_array($item, ['page', 'pp']) && !is_array($valor))
                <input type="hidden" name="{{ $item }}" value="{{ $valor }}" />
            @endif
        @endforeach
    
        <div class="form-row float-md-left align-items-center">

            <label class="col-md-auto col-form-label">Por página:</label>

            <div class="col-auto pl-0">
                <input
                    type="number"
                    value="{{ $data->perPage() }}"
                    class="form-control"
                    name="pp"
                    onchange="document.getElementById('paginationPageNumber').value = 1;this.form.submit()"
                    min="1"
                    max="{{ config('listing.defaultPerPageMaximum') ?? $data->total() }}"
                >
            </div>

            <div class="col-auto data-listagem" >
                <strong>{{ $data->firstItem() }} - {{ $data->lastItem() }}</strong> de <strong>{{ $data->lastItem() }}</strong>
                (<strong>{{ $data->lastPage() }}</strong> página{{ $data->lastPage() > 1 ? 's' : '' }})
            </div>

        </div>

        <div class="form-row float-md-right align-items-center">

            <label class="col-md-auto col-form-label">Ir para página:</label>

            <div class="col-auto pl-0">
                <input
                    id="paginationPageNumber"
                    type="number"
                    class="form-control"
                    name="page"
                    onchange="this.form.submit()"
                    value="{{ $data->currentPage() }}"
                    min="1"
                    max="{{ $data->lastPage() }}"
                />
            </div>

            <div class="col-auto">
                <a @if(! $data->onFirstPage()) href="{{ $data->appends(request()->query())->previousPageUrl() }}" @endif
                    class="btn btn-default @if($data->onFirstPage()) disabled @endif" aria-disabled="true"><i 
                    class="fas fa-chevron-left"></i>  
                </a><a @if($data->hasMorePages()) href="{{ $data->appends(request()->query())->nextPageUrl() }}" @endif
                    class="btn btn-default @if(! $data->hasMorePages()) disabled @endif"><i 
                    class="fas fa-chevron-right"></i>
                </a>
            </div>

        <div>
    </form>

@endif
