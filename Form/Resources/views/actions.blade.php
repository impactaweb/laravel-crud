<div class="form-group row">
    <div class="col-sm-offset-2 col-sm-10">
        <div class="btn-group">


            @foreach($form->actions as $ind => $action)
                @if($ind == 0)
                    <button class="btn btn-primary" name="action" value="{{$action[0]}}"
                            data-action="{{$action[0]}}" data-route="{{$action[2]}}">{{$action[1]}}
                    </button>
                    @if (count($form->actions) > 1)
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Mais opções</span>
                        </button>

                        <ul class="dropdown-menu" role="menu" x-placement="bottom-start">
                    @endif
                @else
                    <button data-route="{{$action[2]}}" data-action="{{$action[0]}}" name="action"
                            value="{{$action[0]}}"
                            class="dropdown-item" type="submit">{{$action[1]}}
                    </button>
                @endif
            @endforeach

            @if (count($form->actions) > 1)
                </ul>
            @endif

        </div>

        @if($form->cancelVisible)
            <a href="{{$form->cancelLinkUrl}}" class="btn btn-link">Cancelar</a>
        @endif

    </div>
</div>
