<div data-container="loading"></div>
<div class="corpo-listing" id="psListing">

    @include('listing::remove-modal')

    <div class="header row">
        @if($actions)
            <div class="col">
                @foreach ($actions as $action)
                    <button
                        type="button"
                        class="btn btn-lg btn-default tooltips actionButton"
                        data-name="{{ $action->getName() }}"
                        data-url="{{ $action->getUrl() }}"
                        data-method="{{ $action->getMethod() }}"
                        title="{{ $action->getLabel() }}"
                        data-toggle="tooltip" data-placement="top" 
                    >
                        @if($action->getIcon())
                            <i class="{{ $action->getIcon() }}"></i>
                        @endif

                        <span class="sr-only">
                            {{ $action->getLabel() }}
                        </span>
                    </button>
                @endforeach
            </div>
        @endif
        <div class="col">
            @include('listing::search')
        </div>
    </div>

    <form id="listingForm" action="" method="POST" style="display:none">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value=""></button>
        <button type="submit"></button>
    </form>

    @if($data && $columns)
    <table class="table table-striped table-hover table-sm" id="listagemTable" data-redir="{{ url()->full() }}">

        {{-- Cabeçalho com as columns --}}
        <thead>
            <tr>
            @foreach($columns as $column)

                @if($loop->first && $column->getName() == $primaryKey)
                    <th scope="col" class="border-top-0">
                        <input type="checkbox" name="checkbox-listing" />
                    </th>
                @endif

                <th scope="col" class="border-top-0">
                    {!! $column->getFieldOrderbyLink() !!}
                </th>
            @endforeach
            </tr>
        </thead>

        {{-- Registros --}}
        @forelse ($data->items() as $item)
            <tr>
            @foreach ($columns as $column)
                @if($loop->first && $column->getName() == $primaryKey)
                    <td><input type="checkbox" name="item[]" class="listing-checkboxes" value="{{ $item->{$column->getIndexName()} }}" /></td>
                @endif
                <td>{!! $item->{$column->getIndexName()} !!}</td>
            @endforeach
            </tr>
        @empty
            <tr class="empty">
                <td colspan="{{ count($columns) }}">Nenhum item encontrado</td>
            </tr>
        @endforelse
    </table>
    @endif

    @include('listing::pagination')
</div>

<style>
</style>
