<div class="form-group row align-items-center">

    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <input
          type="password"
          class="form-control {{ $class }}"
          id="c-{{$id}}"
          name="{{$id}}"
          value="{{ $value ?? '' }}"

          {{-- Atributos adicionais --}}
          @foreach ($attrs as $atributo => $valorAtributo)
            @if(gettype($valorAtributo) == 'string')
                {{ $atributo }}="{{ $valorAtributo }}"
            @endif
          @endforeach
          {{-- Fim - Atributos adicionais --}}

          @if($required)
          required
          @endif
        >
        <div class="invalid-feedback"></div>
    </div>
</div>
