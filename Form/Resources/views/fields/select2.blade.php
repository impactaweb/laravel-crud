<div class="form-group row align-items-center">

    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <select
            data-form-select2="true"
            data-ajax--url="{{ $url }}"
            data-ajax--cache="true"
            class="form-control {{ $class }}"
            id="c-{{$id}}"
            name="{{$id}}"
        {{-- Atributos adicionais --}}
        @foreach ($attrs as $ind => $attrValue)
            @if(gettype($attrValue) == 'string')
                {{ $ind }}="{{ $attrValue }}"
            @endif
        @endforeach
        @if($required)
            required
        @endif
        >
        </select>
        <div class="invalid-feedback"></div>
    </div>

</div>
