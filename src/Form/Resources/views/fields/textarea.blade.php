<div class="form-group row align-items-flexstart">

    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <textarea data-textarea name="{{$id}}" id="c-{{$id}}" class=" form-control ps-textarea {{ $class }}"

            {{-- Atributos adicionais --}}
            @foreach ($attrs as $atributo => $valorAtributo)
                @if(gettype($valorAtributo) == 'string')
                    {{ $atributo }}="{{ $valorAtributo }}"
                @endif
            @endforeach
            {{-- Fim Atributos adicionais --}}

            @if($required)
                required
            @endif
            >{{ $value }}</textarea>

        <div class="invalid-feedback"></div>
    </div>

</div>
