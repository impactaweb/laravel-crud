<?php

namespace Impactaweb\Crud\Listing;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Impactaweb\Crud\Listing\DataSource;
use Impactaweb\Crud\Listing\Field;
use Impactaweb\Crud\Listing\FieldCollection;
use Impactaweb\Crud\Listing\Action;
use Impactaweb\Crud\Listing\Traits\FieldTypes;
use Impactaweb\Crud\Listing\Traits\Util;

class Listing {

    use FieldTypes;
    use Util;

    protected $primaryKey;
    protected $columnAlias;
    protected $dataSource;
    protected $actions = [];
    protected $perPagePagination = 20;
    protected $fields;
    protected $defaultOrderby;
    protected $configFile = 'listing';
    protected $isSearching = false;
    protected $aditionalSelectFields = [];
    protected $showCheckbox = true;
    protected $keepQueryStrings = [];
    protected $exportCSV = false;

    /**
     * Construtor da classe
     *
     * @param string $primaryKey
     * @param mixed $dataSource
     * @param array $options
     */
    public function __construct(string $primaryKey, $dataSource, array $options = [])
    {
        $this->primaryKey = $primaryKey;
        $this->dataSource = new DataSource($dataSource, $options);
        $this->fields = new FieldCollection();

        $this->field($primaryKey, "ID", ['default' => $options['showID'] ?? true]);
        $this->setDefaultOrderby($primaryKey, 'DESC');

        // Quantidade por página
        if (isset($options['pp']) && is_numeric($options['pp'])) {
            $this->setPerPageDefault($options['pp']);
        }

        $this->setDefaultActions();
    }

    /**
     * Define as actions padrão (new, edit, destroy)
     *
     * @return void
     */
    public function setDefaultActions(): void
    {
        $defaultActions = config('listing.defaultActions');
        if (!is_array($defaultActions)) {
            return;
        }

        foreach ($defaultActions as $action) {
            $this->actions[$action['name']] = new Action($action['name'], $action['label'], $action['method'] ?? 'GET', $action['url'] ?? null, $action['icon'] ?? null);
        }
    }

    /**
     * Configura novo campo
     *
     * @param string $name
     * @param string $label
     * @param array $options
     * @param string $type
     * @return void
     */
    public function field(string $name, string $label, array $options = [], string $type = 'text')
    {
        $this->fields->add(new Field($type, $name, $label, $options));
        return $this;
    }

    /**
     * Renderiza a página da listagem, de acordo com a view configurada (Blade View)
     *
     * @return void
     */
    public function render()
    {
        $viewFile = config('listing.view');

        $data = [
            'data' => $this->performQuery(),
            'formToFieldId' => request()->get('to_field_id', null),
            'formFromFieldId' => request()->get('from_field_id', null),
            'actions' => $this->actions,
            'showCheckbox' => $this->showCheckbox == false ? false : $this->isCheckboxNeeded(),
            'columns' => $this->fields->getActiveFields(),
            'primaryKey' => $this->primaryKey,
            'advancedSearchFields' => $this->fields->getAllFields(),
            'isSearching' => $this->isSearching,
            'advancedSearchOperators' => DataSource::getAdvancedSearchOperators(),
            'currentOrderby' => $this->getOrderby(),
            'allowedOrderbyColumns' => $this->dataSource->getAllowedOrderbyColumns(),
            'keepQueryStrings' => $this->keepQueryStrings,
            'exportCSV' => $this->exportCSV,
        ];

        return view($viewFile, $data);
    }

    /**
     * Consulta os dados no banco de dados
     *
     * @return LengthAwarePaginator
     */
    public function performQuery()
    {
        $activeColumns = $this->fields->getActiveFields(true);
        $queryString = request()->query();
        $orderby = $this->getOrderby();

        $this->isSearching = (request()->has('q') && trim(request()->get('q')) !== '');

        // Adicionar colunas da busca ao SELECT e JOIN para garantir que a coluna esteja acessível
        foreach ($this->fields as $field) {
            $fieldName = $field->name;
            if (!$field->activeByDefault) {
                continue;
            }

            $fieldNameQuerystring = str_replace('.', '_', $fieldName);
            if (isset($queryString[$fieldNameQuerystring]) && trim($queryString[$fieldNameQuerystring]) !== '') {
                $this->isSearching = true;
                if (!in_array($fieldName, $activeColumns)) {
                    $activeColumns[] = $fieldName;
                }
            }
        }

        // Campos adicionais para o select
        $activeColumns = array_merge($activeColumns, $this->aditionalSelectFields);

        if (request()->get('csv') == '1') {
            return $this->dataSource->getData($activeColumns, $orderby, $this->getPerPagePagination(), $queryString, $this->columnAlias, true);
        }
        // Consulta os dados
        return $this->dataSource->getData($activeColumns, $orderby, $this->getPerPagePagination(), $queryString, $this->columnAlias);
    }

    /**
     * Order by padrão (se não houver nenhuma setada)
     *
     * @param string $order
     * @param string $direction
     * @return void
     */
    public function setDefaultOrderby(string $order, string $direction): void
    {
        $direction = (strtolower($direction) == 'desc' ? 'desc' : 'asc');
        $this->defaultOrderby = [$order, $direction];
    }

    /**
     * Get orderby based on default set value or request()->get
     *
     * @return array|null
     */
    public function getOrderby(): ?array
    {
        $orderby = request()->get('ord') ?? $this->defaultOrderby[0];
        if (!$orderby) {
            return null;
        }
        $direction = request()->get('dir') ?? $this->defaultOrderby[1];
        $direction = (strtolower($direction) == 'desc' ? 'desc' : 'asc');

        if (!$this->fields->exists($orderby)) {
            return null;
        }

        return [$orderby, $direction];
    }

    /**
     * Define a quantidade de registros por página
     *
     * @param integer $perPage
     * @return void
     */
    public function setPerPageDefault(int $perPage): void
    {
        $this->perPagePagination = $perPage;
    }

    /**
     * Pega a qtde por página para a paginação
     *
     * @return integer
     */
    public function getPerPagePagination(): int
    {
        $perPagePagination = request()->get('pp') ?? $this->perPagePagination;
        if (!is_numeric($perPagePagination) || !($perPagePagination > 0)) {
            $perPagePagination = config('listing.defaultPerPage');
        }

        return $perPagePagination;
    }

    /**
     * Retorna true se alguma das actions necessitar do checkbox.
     * Assume que qualquer action diferente de GET necessita do checkbox
     *
     * @return boolean
     */
    public function isCheckboxNeeded(): bool
    {
        foreach ($this->actions as $action) {
            $url = $action->getUrl();
            if ($action->getMethod() != 'GET'
                || strpos($url, '{id}') !== false
                || strpos($url, '{ids}') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove todas as actions
     *
     * @param array $actionsToClear
     * @return void
     */
    public function clearActions($actionsToClear = []): void
    {
        if (!empty($actionsToClear)) {
            foreach ((array)$actionsToClear as $actionName) {
                unset($this->actions[$actionName]);
            }
        } else {
            $this->actions = [];
        }
    }

    /**
     * Adiciona uma action à lista
     *
     * @param string $label
     * @param string|null $url
     * @param string|null $icon
     * @param string|null $method
     * @param string|null $message
     * @return void
     */
    public function action(string $label, ?string $url = null, ?string $icon = null, ?string $method = null, ?string $message = null): void
    {
        $name = preg_replace("/[^a-z]/",'',strtolower($label));
        $this->actions[$name] = new Action($name, $label, $method ?? 'GET', $url, $icon, $message);
    }

    /**
     * Cria lista de campos adicionais para o select da query
     *
     * @param array $fields
     * @return void
     */
    public function aditionalSelectFields(array $fields)
    {
        foreach ($fields as $alias => $field) {
            if (!is_numeric($alias)) {
                $this->addColumnAlias($field, $alias);
            }
        }
        $this->aditionalSelectFields = $fields;
    }

    /**
     * Adiciona campos adicionais para o select da query
     *
     * @param string $field
     * @return void
     */
    public function addSelectFields(string $field)
    {
        $this->aditionalSelectFields[] = $field;
    }

    /**
     * Adiciona alias para coluna
     * @param $field
     * @return void
     */
    public function addColumnAlias(string $column, string $alias)
    {
        $alias = str_replace('.', '_', $alias);
        $this->columnAlias[$column] = $alias;
    }

    /**
     * Informa quais queryStrings vamos manter no searchform ao realizar uma busca:
     */
    public function setKeepQueryString(array $fields)
    {
        $this->keepQueryStrings = $fields;
    }

    public function enableCSV(string $name, array $columns,bool $enable = true)
    {
        if (request()->get('csv') == '1') {
            $dados = $this->performQuery()->toArray();

            $timestamp = time();
            header("Content-Type: text/csv; charset=UTF-8");
            header("Content-Disposition: attachment; filename={$name}-{$timestamp}.csv");

            // Adiciona BOM para suporte UTF-8 no Excel
            echo "\xEF\xBB\xBF";

            $formetedColumns = [];
            foreach ($columns as $column) {
                $formetedColumns[] = Str::upper(Str::slug($column, '_'));
            }
            // Escreve a linha de cabeçalho usando as colunas desejadas
            $this->writeCsvLine($formetedColumns);

            // Itera através dos dados e escreve apenas as colunas desejadas
            foreach ($dados as $linha) {
                $linha_filtrada = [];
                foreach (array_flip($columns) as $column) {
                    $linha_filtrada[$column] = data_get($linha, $column);
                }
                $this->writeCsvLine($linha_filtrada);
            }

            exit;
        }

        $this->exportCSV = $enable;
    }

    // Função para escrever uma linha no CSV
    public function writeCsvLine($array)
    {
        echo implode(';', array_map(function($value) {
            return mb_convert_encoding($value, 'UTF-8');
        }, $array)) . "\r\n";
    }

}
