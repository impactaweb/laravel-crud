<div class="form-group row align-items-center">
    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <input
            type="text"
            value=""
            name="original-{{$id}}"
            class="form-control {{$class}}"
            id="c-original-{{$id}}"
            data-input="date"
            data-input-format-server="{{ $formatServer }}"
            data-input-format-client="{{ $formatClient }}"
            @if($required)
                required
            @endif
        >

        <input hidden
            type="text"
            name="{{$id}}"
            id="c-{{$id}}"
            value="{{ $value ?? '' }}"
        >

        <div class="invalid-feedback"></div>
    </div>
</div>

