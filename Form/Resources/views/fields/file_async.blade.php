<div class="form-group row align-items-baseline">
    @include('form::fields.label')
    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <input type="hidden" name="{{$id}}" data-hash-file />
        <input
            type="file"
            class="form-control {{ $class }}"
            id="c-{{$id}}"
            mock-name="{{$id}}"
            value="{{ $value }}"
            @if($required)
                required
            @endif
            data-file="async-upload"
        >

        <div class="progress mt-1" style="height: 15px; display: none;">
            <div 
              class="progress-bar"
              role="progressbar"
              style="width: 0%;"
              aria-valuenow="25"
              aria-valuemin="0"
              aria-valuemax="100">
              0%
            </div>
        </div>


        <span class="link-file mt-1" style="display: none;" actions-container >
            Ações: &nbsp;
            <a href="{{ $dir ?? '' }}{{ $value ?? '' }}" target="_blank" link-container>
                Ver arquivo enviado
            </a> &nbsp; &nbsp;

            @if($required != true)
                &nbsp; &nbsp;
                <a href="#" class="excluir" destroy-file>
                    Excluir arquivo enviado
                </a>
            @endif

        </span>

        <div class="invalid-feedback"></div>
    </div>
</div>
