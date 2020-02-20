<div class="form-group row">
    <div class="col-sm-offset-2 col-sm-10">
        <div class="btn-group">

            {{-- Ação Primária--}}
            <button class="btn btn-primary" name="action" value="{{$primaryAction[0]}}"
                    data-action="{{$primaryAction[0]}}">{{$primaryAction[1]}}</button>

            {{-- Açoes secundárias--}}
            @if(!empty($secondaryActions))
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Mais opções</span>
                </button>

                <ul class="dropdown-menu" role="menu" x-placement="bottom-start">
                    @foreach($secondaryActions as $acao)
                    <button data-action="{{$acao[0]}}" name="action" value="{{$acao[0]}}"
                            class="dropdown-item" type="submit">{{$acao[1]}}</button>
                    @endforeach
                </ul>
            @endif
            {{-- Fim ações secundárias --}}

        </div>

        @if($isCancelVisible)
        <a href="{{$cancelUrl}}" class="btn btn-link">Cancelar</a>
        @endif

    </div>
</div>
