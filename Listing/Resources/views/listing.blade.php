<div data-container="loading"></div>
<div class="corpo-listing" id="psListing">

    @include('listing::confirmation-modal')

    <div class="header row">
        @if($actions && !$formToFieldId)
            <div class="col">
                @foreach ($actions as $action)
                    <button
                        type="button"
                        class="btn btn-lg btn-default tooltips actionButton"
                        data-name="{{ $action->getName() }}"
                        data-url="{{ $action->getUrl() }}"
                        data-verb="{{ $action->getVerb() }}"
                        data-method="{{ $action->getMethod() }}"
                        title="{{ strip_tags($action->getLabel()) }}"
                        data-confirmation="{{ $action->getConfirmationText() }}"
                        data-toggle="tooltip" data-placement="top"
                    >
                        @if($action->getIcon())
                            <i class="{{ $action->getIcon() }}"></i>
                            <span class="sr-only">
                                {{ $action->getLabel() }}
                            </span>
                        @else
                            {!! $action->getLabel() !!}
                        @endif
                    </button>
                @endforeach
            </div>
        @endif
        @if($searchBar)
                <div class="col">
                    @include('listing::search')
                </div>

                <form id="listingForm" action="" method="POST" style="display:none">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value=""></button>
                    <button type="submit"></button>
                </form>
            @endif
        </div>

    @if($data && $columns)
        <table class="table table-striped table-hover table-sm" id="listagemTable" data-redir="{{ url()->full() }}">

            {{-- Cabeçalho com as columns --}}
            <thead>
            <tr>
                <th scope="col" class="border-top-0"  @if(!$showCheckbox) style="display:none" @endif >
                    <input type="checkbox" name="checkbox-listing" />
                </th>
                @foreach($columns as $column)
                    <th scope="col" class="border-top-0">
                        {!! $column->getOrderbyLink($currentOrderby, $allowedOrderbyColumns) !!}
                    </th>
                @endforeach
            </tr>
            </thead>

            <tbody>
            {{-- Registros --}}
            @forelse ($data->items() as $index => $item)
                <tr @if($formToFieldId)
                        data-search-field="{{ $formToFieldId }}"
                    data-search-value="{{ $item->$formFromFieldId }}"
                    @endif>

                    <td @if(!$showCheckbox) style="display:none" @endif >
                        <input type="checkbox" name="item[]" class="listing-checkboxes" value="{{ $item->$primaryKey }}"/>
                    </td>

                    @foreach ($columns as $column)
                        <td>{!! $column->formatData($item, $index, $data->items()) !!}</td>
                    @endforeach
                </tr>
            @empty
                <tr class="empty">
                    <td colspan="100%">Nenhum item encontrado</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    @endif

    @if($exportCSV)
        <div class="d-flex justify-content-end w-100 mb-2 p-0">
            @php
                $query = $_GET;
                $query['csv'] = 1;
                $novaUrl = url()->current() . '?' . http_build_query($query);
            @endphp
            <a href="{{$novaUrl}}" class="btn btn-default">
                <i class="fas fa-file-csv"></i> Exportar CSV
            </a>
        </div>
    @endif

    @include('listing::pagination')
</div>

<style>
</style>
