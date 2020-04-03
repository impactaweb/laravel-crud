<div class="custom-crud-container">

    <label class="font-weight-bold custom-crud-label" for="c-{{$id}}">
      @if($required) <span class="text-danger">*</span>@endif
      {{ $label }}:
    </label>

    <div class="custom-crud-group">
        <input
          type="text"
          maxlength="1"
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

          @if($required)
          required
          @endif
        >
    </div>
</div>