@if($pagination && $data)
    {{-- dica: o método appends informa ao links() que deve manter a query string ao paginar --}}
    <div class="row justify-content-center">
        <form class="col row" action="{{ request()->url() }}" method="get" style="text-align:left;" class="frmRodape" />
            <div class="col-4 form-group row align-items-center">
                <label class="col col-form-label">Por página:</label>
                <div class="col-sm-5 pl-0">
                <input
                    type="number"
                    value="{{ request()->query('pp') ?? $perPage }}"
                    class="form-control"
                    name="pp"
                    onchange="this.form.submit()"
                    min="1"
                    max="{{ config('listing.defaultPerPageMaximum') }}"
                >
                </div>
            </div>

            <div class="col-5 form-group row">
                <label class="col col-form-label">ir para página:</label>
                <div class="col-sm-6 pl-0">
                    <input
                        type="number"
                        class="form-control"
                        name="page"
                        onchange="this.form.submit()"
                        value="{{ request()->query('page') ?? '1' }}"
                        min="1"
                        max="{{ $data->appends(request()->query())->lastPage() }}"
                    >
                </div>
            </div>

            {{-- para manter as query strings ao submeter o form: --}}
            @foreach(request()->query() as $item => $valor)
                @if($item != 'page' && $item != 'pp' && !is_array($valor))
                    <input type="hidden" name="{{ $item }}" value="{{ $valor }}" />
                @endif
            @endforeach

            <div class="col-3 data-listagem" >
                <strong>{{ $data->appends(request()->query())->currentPage() }} - {{$data->appends(request()->query())->perPage()}}</strong>
                de
                <strong>{{ $data->appends(request()->query())->lastPage() }}</strong>
                <a
                    data-paginate="left"
                    @if($data->appends(request()->query())->currentPage() !== 1)
                        href="?pp={{ $data->appends(request()->query())->perPage() }}&page={{ $data->appends(request()->query())->currentPage() - 1 }}"
                    @endif
                    class="btn btn-default @if($data->appends(request()->query())->currentPage() === 1)disabled @endif" aria-disabled="true"><i class="fas fa-chevron-left"></i></a><a
                    data-paginate="right"
                    @if($data->appends(request()->query())->currentPage() !== $data->appends(request()->query())->lastPage())
                        href="?pp={{ $data->appends(request()->query())->perPage() }}&page={{ $data->appends(request()->query())->currentPage() + 1 }}"
                    @endif
                    class="btn btn-default @if($data->appends(request()->query())->currentPage() === $data->appends(request()->query())->lastPage())disabled @endif"><i class="fas fa-chevron-right"></i></a>
            </div>
            <br> Total de {{ $data->total() }} registros
        </form>
    </div>

@endif
