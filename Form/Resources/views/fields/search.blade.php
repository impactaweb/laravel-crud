<div class="form-group row align-items-center">

    @include('form::fields.label')

    <div class="col-5">
        <input
          type="text"
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

        <a href="#" data-toggle="modal" class="search-icon d-inline" data-target="#modal-search-{{$id}}">
            <i class="fa fa-search"></i> Buscar
        </a>

        <div class="invalid-feedback"></div>


    </div>
</div>


<div class="modal" data-search-iframe="{{$id}}" id="modal-search-{{$id}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <iframe data-url="{{ $url }}?to_field_id={{$id}}&from_field_id={{ $search }}&is_popup=1"
                    id="iframe-{{$id}}" src="{{ $url }}?to_field_id={{$id}}&from_field_id={{ $search }}&is_popup=1"
                    style="width: 100%; height: 900px; border: 0">
            </iframe>
        </div>
    </div>
</div>
