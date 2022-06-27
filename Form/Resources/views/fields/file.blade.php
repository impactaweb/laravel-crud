<div class="form-group row align-items-baseline">
    @include('form::fields.label')
    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <input
                type="file"
                class="form-control {{ $class }}"
                id="c-{{$id}}"
                name="{{$id}}"
                value="{{ $value ?? '' }}"
                @if($required)
                required
                @endif

            @foreach ($attrs as $attr => $attrValue)
                {{ $attr }}="{{ $attrValue }}"
            @endforeach
        >

        @if(!empty($value))
            <span class="link-file">
            Visualizar:
            <a href="{{ $dir }}{{ $value }}" target="_blank">
                {{ basename($value) }}
            </a>

            @if($nullable)
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
