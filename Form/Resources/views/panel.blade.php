<style>
    /* 1) Remove sublinhado do botão link SEMPRE */
    .card-header .btn-link,
    .card-header .btn-link:hover,
    .card-header .btn-link:focus {
        text-decoration: none !important;
    }

    /* 2) Título: ganha sublinhado só no hover */
    .card-header .btn-link .accordion-title {
        text-decoration: none;
    }

    .card-header .btn-link:hover .accordion-title {
        text-decoration: underline !important;
    }

    /* 3) Garante que a seta nunca recebe sublinhado */
    .card-header .btn-link .arrow-icon,
    .card-header .btn-link:hover .arrow-icon {
        text-decoration: none !important;
    }

    /* 4) Animação / rotação da seta conforme aberto/fechado */
    .card-header .btn-link .arrow-icon {
        display: inline-block;
        transition: transform 0.3s ease;
    }

    /* Quando o botão está colapsado (fechado) → chevron para baixo */
    .card-header .btn-link.collapsed .arrow-icon {
        transform: rotate(0deg);
    }

    /* Quando está aberto (sem .collapsed) → chevron para cima */
    .card-header .btn-link:not(.collapsed) .arrow-icon {
        transform: rotate(180deg);
    }
</style>

<div class="accordion" id="abas-form-{{ $form->formId }}">

    @foreach ($form->panels as $index => $panel)
        <div class="card mb-4 border overflow-visible"
             data-field-name="panel-{{ $index }}"
             @foreach ($panel->attrs as $atributo => $valorAtributo)
                 @if ( $atributo === 'data-show-rules-panel')
                     data-show-rules-panel='@json($valorAtributo)'
             style="display: none;"
             @elseif ($atributo == 'show_rules')
                 data-show-rules='@json($valorAtributo)'
        @elseif(gettype($valorAtributo) == 'string')
            {{ $atributo }}="{{ $valorAtributo }}"
        @endif
    @endforeach
    >
    @php
        $isCollapsed = $panel->attrs['collapsed'] ?? false;
        $independent = $panel->attrs['independent'] ?? false;
    @endphp

    <div class="card-header" id="aba-{{$panel->getPanelId()}}">
        <h4 class="mb-0">
            <button
                    class="btn btn-link d-flex justify-content-between w-100 align-items-center {{ $isCollapsed ? 'collapsed' : '' }}"
                    type="button"
                    data-toggle="collapse"
                    data-target="#collapse-{{$panel->getPanelId()}}"
                    aria-controls="collapse-{{$panel->getPanelId()}}"
                    aria-expanded="{{ $isCollapsed ? 'false' : 'true' }}"
            >
                <span class="accordion-title">{{ $panel->title }}</span>

                <span class="arrow-icon">
<i class="fas fa-chevron-down"></i>
</span>
            </button>
        </h4>
    </div>

    <div
            id="collapse-{{$panel->getPanelId()}}"
            aria-labelledby="aba-{{$panel->getPanelId()}}"
            class="collapse {{ $isCollapsed ? '' : 'show' }}"
    >
        <div class="card-body">

            @foreach ($panel->fields as $field)
                <div class="fieldBlock" data-field-name="{{ $field->id }}"
                     @if(isset($field->options['show_rules'])) data-show-rules='@json($field->options['show_rules'])' @endif
                >

                    {!! $field->render($form->initial, $form->getRules()) !!}

                </div>
            @endforeach

        </div>
    </div>
</div>
@endforeach
</div>