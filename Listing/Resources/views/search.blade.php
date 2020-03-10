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
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <button class="input-group-append btn btn-default p-0 border" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                      <span class="input-group-text bg-transparent border-0" id="basic-addon2"><i class="fas fa-search"></i></span>
                    </button>
                    <div class="btn-group" role="group">
                        <button
                            id="buscaAvancadaBtn"
                            type="button"
                            class="btn btn-lg btn-default dropdown-toggle border"
                            data-toggle="modal"
                            data-target="#modalBuscaAvançada"
                        ></button>
                    </div>
                </div>
                @if(request()->get('q') !== null || request()->get('terms') !== null)
                    <a href="{{ request()->url() }}" class="btn close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                @endif
            </div>

        </div>
    </div>
</form>

<div
    class="modal fade show"
    id="modalBuscaAvançada"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalBuscaAvançadaLabel"
    aria-modal="true"
>
    <div
        class="modal-dialog"
        role="document"
    >
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="modalBuscaAvançadaLabel">Busca avançada</h2>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body" style="max-height: 450px; overflow-y: scroll;">
            @include('listing::advancedsearch')
        </div>
        <div class="modal-footer">
            <a
                href="{{ request()->url() }}"
                title="limpar busca"
                class="btn btn-secondary"
            >Limpar</a>
            <button type="button" class="btn btn-primary" data-avancada="buscar">Buscar</button>
        </div>
        </div>
  </div>
</div>
