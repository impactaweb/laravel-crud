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

<script>
window.onload = function() {
    var $ = jQuery


/**
 * Configuração do show-rules
 */
    var inputsToBind = {}

$('div[data-show-rules]').each(function() {
    var rules = $(this).data('show-rules')
    var hideField = $(this).data('field-name')

    Object.keys(rules).forEach(function(ruleField){

        inputsToBind[ruleField] = true
        var inputToHandle = $(":input[name='" + ruleField + "']")
        if (!inputToHandle.length) {
            return;
        }

        var setEventChange = false
        if (inputToHandle.data('hide-rules')) {
            var fieldHideRules = inputToHandle.data('hide-rules')
        } else {
            var fieldHideRules = {}
            setEventChange = true
        }

        fieldHideRules[hideField] = rules[ruleField]
        inputToHandle.data('hide-rules', fieldHideRules)

        if (setEventChange) {
            inputToHandle.change(function(){
                
                if ($(this).is(":radio:not(:checked)")) {
                    return;
                }

                var inputValue = $(this).val()
                var hideRules = $(this).data('hide-rules')
                for (var field in hideRules) {

                    var fieldBlockToHide = $("div[data-field-name='" + field + "']")
                    var valuesToCheck = hideRules[field]

                    if (!(typeof valuesToCheck == 'object')) {
                        valuesToCheck = [valuesToCheck]
                    }

                    var eventShow = false;
                    for (var index in valuesToCheck) {
                        if (inputValue == valuesToCheck[index]) {
                            eventShow = true;
                        }
                    }

                    eventShow ? fieldBlockToHide.show() : fieldBlockToHide.hide()
                }
            })
        }
    })
})

// Aciona o evento change dos inputs que deverão ser monitorados
for (var field in inputsToBind) {
    $(":input[name='" + field + "']").trigger('change')
}



}
</script>