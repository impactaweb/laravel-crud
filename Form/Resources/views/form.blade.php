<div data-container="loading">
</div>
<div data-expect-alert></div>
<form
    enctype='multipart/form-data'
    method="{{ $form->method }}"
    {{-- não apaga --}}
    data-its-form
    {{-- Actions--}}
    @if($form->formAction)
    action="{{ $form->formAction }}"
    @endif

    id="{{ $form->formId }}"
    class="{{$form->class}}"

    {{-- Target Blank--}}
    @if($form->targetBlank)
    target="__blank"
    @endif

    @if($form->ajax)
    data-ajax="true"
    @endif

    {{-- Autocomplete--}}
    @if($form->autoComplete)
    autocomplete="off"
    data-form-prefix="c-"
    @endif>


    {{ csrf_field() }}
    {{ method_field($form->method) }}

    @if(!empty($form->primaryKeyValue))
        <input type="hidden" name="{{$form->primaryKey}}" value="{{$form->primaryKeyValue}}" data-id>
    @endif

    {{-- Render template for panels --}}
    <div class="panel-group" id="Abas" role="tablist" aria-multiselectable="true">
        @include($panelTemplate)
    </div>

    @include($actionsTemplate)
</form>
