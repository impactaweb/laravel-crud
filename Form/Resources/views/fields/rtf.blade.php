<div class="form-group">

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif mt-2">
        <textarea
            class="form-control {{ $class }}"
            data-type="summernote"
            id="c-{{$id}}"
            name="{{$id}}"
            @if($required)
                required
            @endif
            rows="3"
        >{{$value}}</textarea>

        <div class="invalid-feedback"></div>
    </div>
</div>
