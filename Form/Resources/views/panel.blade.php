<div class="accordion" id="abas-form-{{ $formId }}">
    @foreach ($panels as $panel)
        <div class="card mb-4 border overflow-visible"
            @foreach ($panel->attrs as $atributo => $valorAtributo)
                @if(gettype($valorAtributo) == 'string')
                    {{ $atributo }}="{{ $valorAtributo }}"
                @endif
            @endforeach
            >
            <div class="card-header" id="aba-{{$panel->getPanelId()}}">
                <h4 class="mb-0">
                <button
                    class="btn btn-link"
                    type="button" data-toggle="collapse"
                    data-target="#collapse-{{$panel->getPanelId()}}"
                    aria-controls="collapse-{{$panel->getPanelId()}}"
                    aria-expanded="false"
                    >
                    {{ $panel->title }}
                </button>
                </h4>
            </div>
            <div
                id="collapse-{{$panel->getPanelId()}}"
                aria-labelledby="aba-{{$panel->getPanelId()}}"
                data-parent="#abas-form-{{ $formId }}"
                class="collapse show"
                >
                <div class="card-body">
                    {!! $panel->render($form->initial, $form->getRules()) !!}
                </div>
            </div>
        </div>
    @endforeach
</div>
