<div class="form-group row align-items-baseline">
    @include('form::fields.label')
    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <input
                type="file"
                class="form-control {{ $class }}"
                id="c-{{$id}}"
                name="{{$id}}"
                value="{{ $value }}"
                @if($required)
                required
                @endif
        >

        @if(!empty($value))
            <span class="link-file">
            Visualizar:
            <a href="{{ $dir }}{{ $value }}" target="_blank">
                {{ $value }}
            </a>

            @if($required != true)
                &nbsp; ‹ &nbsp;
                <a href="#" class="excluir" data-destroy="{{ $dir}}{{$value}}" data-file-field="{{ $id }}">
                    Excluir
                </a>
                @endif

        </span>
        @endif

        <div class="invalid-feedback"></div>
    </div>
</div>
