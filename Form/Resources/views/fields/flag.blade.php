<div class="form-group row align-items-center">

    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <div class="form-check form-check-inline mr-0 pl-3 pr-0 d-flex align-items-center">
            <input
                type="radio"
                name="{{$id}}"
                id="c-{{$id}}-sim"
                class=""
                value="1"
                @if($value == 1)
                    checked="checked"
                @endif
                @if($required)
                required
                @endif
            />&nbsp;
            <label for="c-{{$id}}-sim" class="mb-0 mr-3">Sim</label>

            <input
                type="radio"
                name="{{$id}}"
                id="c-{{$id}}-nao"
                class=""
                value="0"
                @if($value == 0)
                    checked="checked"
                @endif
                @if($required)
                    required
                @endif
            />&nbsp;
            <label for="c-{{$id}}-nao" class="mb-0">Não</label>

        </div>

        <div class="invalid-feedback"></div>
    </div>
</div>
