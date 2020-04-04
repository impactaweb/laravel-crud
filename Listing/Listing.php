<?php

namespace Impactaweb\Crud\Listing;

use Illuminate\Pagination\LengthAwarePaginator;
use Impactaweb\Crud\Listing\DataSource;
use Impactaweb\Crud\Listing\Field;
use Impactaweb\Crud\Listing\FieldCollection;
use Impactaweb\Crud\Listing\Action;

class Listing {

    protected $primaryKey;
    protected $dataSource;
    protected $actions = [];
    protected $perPagePagination = 20;
    protected $fields;
    protected $defaultOrderby;
    protected $configFile = 'listing';

    public function __construct(string $primaryKey, object $dataSource, array $options = [])
    {
        $this->primaryKey = $primaryKey;
        $this->dataSource = new DataSource($dataSource);
        $this->fields = new FieldCollection();
        $this->fields->add(new Field($primaryKey, "ID"));

        if (isset($options['orderby']) && is_array($options['orderby']) && count($options['orderby']) == 2) {
            $this->defaultOrderby = $this->setDefaultOrderby($options['orderby'][0], $options['orderby'][1]);
        }

        if (isset($options['pp']) && is_numeric($options['pp'])) {
            $this->setPerPageDefault($options['pp']);
        }

        $this->setDefaultActions();
    }

    /**
     * Define as actions padrão (new, edit, destroy)
     */
    public function setDefaultActions(): void
    {
        $defaultActions = config($this->configFile . '.defaultActions');
        if (!is_array($defaultActions)) {
            return;
        }

        foreach ($defaultActions as $action) {
            $this->actions[$action['name']] = new Action($action['name'], $action['label'], $action['method'] ?? 'GET', $action['url'] ?? null, $action['icon'] ?? null);
        }
    }

    /**
     * Configura novo campo 
     */
    public function field(string $name, string $label, array $options = [])
    {
        $this->fields->add(new Field($name, $label, $options));
        return $this;
    }

    /**
     * Renderiza a página
     */
    public function render()
    {
        $viewFile = config($this->configFile . '.view');
        $data = $this->performQuery();
        $actions = $this->actions;
        $advancedSearchFields = [];
        $columns = $this->fields->getActiveFields();
        $pagination = $data;
        $primaryKey = $this->primaryKey;

        $allowedOrderbyColumns = $this->dataSource->getAllowedOrderbyColumns();
        return view($viewFile, compact('data', 'actions', 'advancedSearchFields', 'columns', 'pagination', 'allowedOrderbyColumns', 'primaryKey'));
    }

    // consulta os dados no bd
    public function performQuery(): LengthAwarePaginator
    {
        $activeColumns = $this->fields->getActiveFields(true);
        $queryString = request()->query();
        $orderby = $this->getOrderby();

        // Adicionar colunas da busca ao SELECT e JOIN para garantir que a coluna esteja acessível
        foreach ($this->fields->getFieldsName() as $fieldName) {
            $fieldNameQuerystring = str_replace('.', '_', $fieldName);
            if (isset($queryString[$fieldNameQuerystring]) && !empty($queryString[$fieldNameQuerystring])) {
                $activeColumns[] = $fieldName;
            }
        }

        return $this->dataSource->getData($activeColumns, $orderby, $this->getPerPagePagination(), $queryString);
    }

    // Order by padrão (se não houver nenhuma setada)
    public function setDefaultOrderby($order, $direction): void
    {
        $direction = (strtolower($direction) == 'desc' ? 'desc' : 'asc');
        $this->defaultOrderby = [$order, $direction];
    }

    // Get orderby based on default set value or request()->get
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

    // Quantidade de registros por página
    public function setPerPageDefault(int $perPage): void
    {
        if (!isset($this->perPagePagination)) {
            $this->perPagePagination = $perPage;
        }
    }

    // Pega a qtde por página padrão
    public function getPerPagePagination(): int
    {
        $perPagePagination = request()->get('pp') ?? $this->perPagePagination;
        if (!is_numeric($perPagePagination) || !($perPagePagination > 0)) {
            $perPagePagination = config($this->configFile . '.defaultPerPage');
        }

        return $perPagePagination;
    }

}