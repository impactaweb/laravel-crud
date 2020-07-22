
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
