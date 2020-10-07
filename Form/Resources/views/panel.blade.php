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
                @elseif ($atributo == 'show_rules')
                    data-show-rules='@json($valorAtributo)' 
                @elseif(gettype($valorAtributo) == 'string')
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
                data-parent="#abas-form-{{ $form->formId }}"
                class="collapse show"
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
