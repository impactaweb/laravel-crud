<div class="form-group row align-items-center">
    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <input
            type="text"
            value=""
            name="original-{{$id}}"
            class="form-control {{$class}}"
            id="c-original-{{$id}}"
            data-input="date-time"
            {{-- date-input-format="{{$formatServer}}" --}}
            data-input-format="DD/MM/YYYY hh:mm:ss"
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

