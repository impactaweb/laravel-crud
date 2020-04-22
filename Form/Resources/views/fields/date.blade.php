<div class="form-group row align-items-center">
    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <input
            type="text"
            value="{{ $value ?? '' }}"
            name="original-{{$id}}"
            class="form-control {{$class}}"
            id="c-original-{{$id}}"
            data-input="date"
            date-input-format="{{$formatServer}}"
            @if($required)
                required
            @endif
        >

        <input hidden
            type="text"
            id="c-{{$id}}"
            value="COLOCAR A DATA FORMATADA AQUI"
            name="{{$id}}"
        >

        <div class="invalid-feedback"></div>
    </div>
</div>

