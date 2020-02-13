<div class="form-group row align-items-center">

    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <select
            multiple
            class="form-control {{ $class }}"
            id="c-{{$id}}"
            name="{{$id}}"

            {{-- Attributes --}}
            @foreach ($attrs as $attr => $attrValue)
                @if(gettype($attrValue) == 'string')
                    {{ $attr }}="{{ $attrValue }}"
                @endif
            @endforeach

            @if($required)
                required
            @endif
        >
            {{-- Build options --}}
            @foreach ($options as $id => $option)
                <option
                    @if(in_array($id, $value))
                    selected
                    @endif
                    value="{{$id}}">{{$option}}
                </option>
            @endforeach

        </select>
        <div class="invalid-feedback"></div>
    </div>
</div>
