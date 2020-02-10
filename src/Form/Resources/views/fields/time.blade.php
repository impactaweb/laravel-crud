<div class="form-group row align-items-center">

    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <input
            type="text"
            value="{{ $value ?? '' }}"
            name="{{$id}}"
            class="form-control {{$class}}"
            id="c-{{$id}}"
            data-input="time"
            @if($required)
            required
            @endif
        >
        <div class="invalid-feedback"></div>
    </div>
</div>
