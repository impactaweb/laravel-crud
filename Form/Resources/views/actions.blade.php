<div class="form-group row">
    <div class="col-sm-offset-2 col-sm-10">
        <div class="btn-group">


            @foreach($form->actions as $actionName => $action)
                @if($actionName == $firstAction)
                    <button class="btn btn-primary" name="action" value="{{$actionName}}" data-action="{{$actionName}}">
                        {{$action[0]}}
                    </button>
                    @if (count($form->actions) > 1)
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Mais opções</span>
                        </button>

                        <ul class="dropdown-menu" role="menu" x-placement="bottom-start">
                    @endif
                @else
                    <button data-action="{{$actionName}}" name="action"
                            value="{{$actionName}}"
                            class="dropdown-item" type="submit">{{$action[0]}}
                    </button>
                @endif
                <input hidden value="{{$action[1]}}" name="redirect_{{$actionName}}">
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
