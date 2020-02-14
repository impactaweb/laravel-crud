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

    @if($formAction)
        <form action="{{ $formAction }}" method="{{ $formMethod ?? 'POST' }}" >
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
    @if($formAction)
        </form>
    @endif

    @include('listing::pagination')

</div>