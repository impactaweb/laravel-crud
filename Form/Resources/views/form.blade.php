<div data-container="loading">
</div>
<div data-expect-alert></div>
<form
    {{-- não apaga --}}
    data-its-form
    {{-- Actions--}}
    @if($action)
    action="{{ $action }}"
    @endif

    id="{{ $formId }}"
    class="container p-3 {{$formClass}}"

    {{-- Target Blank--}}
    @if($targetBlank)
    target="__blank"
    @endif

    method="POST"
    enctype="multipart/form-data"

    {{-- Autocomplete--}}
    @if($autoComplete)
    autocomplete="off"
    @endif>

    {{ csrf_field() }}
    {{ method_field($method) }}


    <div class="panel-group" id="Abas" role="tablist" aria-multiselectable="true">
        {{-- Render template for panels --}}
        @include($panelTemplate)
    </div>

    @include($actionsTemplate)
</form>