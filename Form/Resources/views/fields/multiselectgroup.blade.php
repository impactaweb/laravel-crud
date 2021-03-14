<div class="form-group row align-items-center">

    @include('form::fields.label')

    <div class="@if($col >= '10') col @else col-md-{{$col}} @endif">
        <select
            multiple
            class="form-control {{ $class }}"
            id="c-{{$id}}"
            name="{{$id}}[]"

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
            {{-- Build group selectOptions --}}
            @foreach ($selectOptions as $groupParent => $childrens)
                <optgroup label="{{ $groupParent }}">
                    @foreach ($childrens as $id => $textValue)
                        <option
                            @if(is_array($value) && in_array($id, $value))
                            selected
                            @endif
                            value="{{$id}}"
                        > 
                            {{ $textValue }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach

        </select>
        <div class="invalid-feedback"></div>
    </div>
</div>