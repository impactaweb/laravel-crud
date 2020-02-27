<form action="{{ request()->url() }}" method="get" class="frmBusca">
    <div class="form-group">
        <div class="input-group mb-2 mr-sm-2">

            <div class="input-group mb-3">
                <input
                    type="text"
                    class="form-control form-control-lg"
                    name="q"
                    value="{{ request()->get('q') ?? '' }}"
                    placeholder="{{ __('listing::listing.search') }}"
                    aria-label="{{ __('listing::listing.search') }}"
                >
                <button class="input-group-append btn btn-default p-0 border" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                  <span class="input-group-text bg-transparent border-0" id="basic-addon2"><i class="fas fa-search"></i></span>
                </button>
                @if(request()->get('q') !== null)
                    <a href="{{ request()->url() }}" title="limpar busca" >X</a>
                @endif
            </div>

        </div>
    </div>
</form>
