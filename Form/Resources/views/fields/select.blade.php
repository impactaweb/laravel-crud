<div class="form-group row align-items-center">

    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
      <select
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
          <option></option>
          {{-- Construção das opções --}}
          @foreach ($options as $id => $option)
            @if(gettype($option) == 'string')
                @if($id == $value )
                  <option selected="selected" value="{{$id}}">{{$option}}</option>
                @else
                  <option value="{{$id}}">{{$option}}</option>
                @endif
            @endif
          @endforeach
      </select>
      <div class="invalid-feedback"></div>
  </div>

</div>
