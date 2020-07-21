<div class="form-group row align-items-center">

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <input
            type="hidden"
            class="form-control {{ $class }}"
            id="c-{{$id}}"
            name="{{$id}}"
            value="{{ $value }}"

        {{-- Atributos adicionais --}}
        @foreach ($attrs as $attr => $attrValue)
            @if(gettype($attrValue) == 'string')
                {{ $attr }}="{{ $attrValue }}"
            @endif
        @endforeach
        {{-- Fim - Atributos adicionais --}}

        >
        <div class="invalid-feedback"></div>
    </div>
</div>
