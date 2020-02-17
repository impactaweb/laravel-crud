<style type="text/css" >
.corpo { margin:15px 0; }
.corpo * { float:none; }
table { width:800px; margin:10px auto; }
th, td { border:solid 1px #ccc; padding:5px; }
.frmbusca, .frmRodape  { text-align:right; background:#dedede; }
input[type="number"], select { width:60px; }
</style>

<div class="corpo" >
    <div class="header">
        @include('listing::search')
    </div>

    @if($actions)
        <form id="meuForm" action="" method="POST" >
        {{ csrf_field() }}
    @endif
        <table>
                {{-- Cabeçalho com as columns --}}
                <tr>
                @foreach($columns as $column => $params)
                    <th>{!! $params['column_link'] !!}</th>
                @endforeach
                </tr>

                {{-- Registros --}}
                @foreach ($data as $item)
                    <tr>
                    @foreach ($columns as $column => $params)
                        <td>{!! $item->$column !!}</td>
                    @endforeach
                    </tr>
                @endforeach
        </table>

    {{-- Exemplo de como utilizar as ações: --}}
    @if($actions)
        @foreach ($actions as $action => $params)
            <?php 
                $fullUrl = request()->url();
                $url = $fullUrl.$params['url'];
                $method = $params['method'];
            ?>
            <button type="button" onclick="$('#meuForm').prop('action', '{{ $url }}' ).prop('method', '{{ $method }}').submit();" title="{{ $action }}" >{{ $action }}</button>
        @endforeach
        </form>
        <br><br>
    @endif

    @include('listing::pagination')

</div>