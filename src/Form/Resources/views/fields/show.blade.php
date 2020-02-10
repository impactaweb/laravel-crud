<div class="form-group row align-items-center">
    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        {!! $conteudo !!}
        <div class="invalid-feedback"></div>
    </div>
</div>
