<div class="form-group row">
    <div class="col-sm-offset-2 col-sm-10">
        <div class="btn-group">

            @foreach($form->actions as $ind => $action)
                @if($ind == 1)
                    <button class="btn btn-primary" name="action" value="{{$action[0]}}"
                            data-action="{{$action[0]}}" data-route="{{$action[2]}}">{{$action[1]}}
                    </button>
                @else
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Mais opções</span>
                    </button>

                    <ul class="dropdown-menu" role="menu" x-placement="bottom-start">
                        <button data-route="{{$action[2]}}" data-action="{{$action[0]}}" name="action"
                                value="{{$action[0]}}"
                                class="dropdown-item" type="submit">{{$action[1]}}
                        </button>
                    </ul>
                @endif
            @endforeach

        </div>

        @if($form->cancelVisible)
            <a href="{{$form->cancelLinkUrl}}" class="btn btn-link">Cancelar</a>
        @endif

    </div>
</div>
