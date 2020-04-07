<?php

namespace Impactaweb\Crud\Listing;

use Illuminate\Pagination\LengthAwarePaginator;
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
    protected $dataSource;
    protected $actions = [];
    protected $perPagePagination = 20;
    protected $fields;
    protected $defaultOrderby;
    protected $configFile = 'listing';
    protected $isSearching = false;

    public function __construct(string $primaryKey, object $dataSource, array $options = [])
    {
        $this->primaryKey = $primaryKey;
        $this->dataSource = new DataSource($dataSource);
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
     */
    public function field(string $name, string $label, array $options = [], string $type = 'text')
    {
        $this->fields->add(new Field($type, $name, $label, $options));
        return $this;
    }

    /**
     * Renderiza a página
     */
    public function render()
    {
        $viewFile = config('listing.view');

        $data = [
            'data' => $this->performQuery(),
            'actions' => $this->actions,
            'showCheckbox' => $this->isCheckboxNeeded(),
            'columns' => $this->fields->getActiveFields(),
            'primaryKey' => $this->primaryKey,
            'advancedSearchFields' => $this->fields->getAllFields(),
            'isSearching' => $this->isSearching,
            'advancedSearchOperators' => DataSource::getAdvancedSearchOperators(),
            'currentOrderby' => $this->getOrderby(),
            'allowedOrderbyColumns' => $this->dataSource->getAllowedOrderbyColumns()
        ];

        return view($viewFile, $data);
    }

    // consulta os dados no bd
    public function performQuery(): LengthAwarePaginator
    {
        $activeColumns = $this->fields->getActiveFields(true);
        $queryString = request()->query();
        $orderby = $this->getOrderby();

        $this->isSearching = (request()->has('q') && trim(request()->get('q')) !== '');

        // Adicionar colunas da busca ao SELECT e JOIN para garantir que a coluna esteja acessível
        foreach ($this->fields->getFieldsName() as $fieldName) {
            $fieldNameQuerystring = str_replace('.', '_', $fieldName);
            if (isset($queryString[$fieldNameQuerystring]) && trim($queryString[$fieldNameQuerystring]) !== '') {
                $this->isSearching = true;
                if (!in_array($fieldName, $activeColumns)) {
                    $activeColumns[] = $fieldName;
                }
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
            $perPagePagination = config('listing.defaultPerPage');
        }

        return $perPagePagination;
    }

    // Retorna true se alguma das actions necessitar do checkbox.
    // Assume que qualquer action diferente de GET necessita do checkbox
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

    // Remove todas as actions
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

    // Adiciona uma action à lista
    public function action(string $name, string $label, ?string $method = null, ?string $url = null, ?string $icon = null, ?string $message = null): void
    {
        $this->actions[$name] = new Action($name, $label, $method ?? 'GET', $url, $icon, $message);
    }

    // Custom fields
    public function customField(string $label, callable $callbackFunction, array $options = [], ?string $name = null, string $type = 'text')
    {
        if (!$name) {
            // Criando um nome aleatório para o campo
            $name = 'customfield.' 
                . substr(strtolower(preg_replace("/[^A-Za-z0-9?!]/",'', $label)), 0, 20) 
                . '_' 
                . substr(md5(uniqid()),0,10);
        }

        $options['callback'] = $callbackFunction;
        $this->field($name, $label, $options, $type);
    }

}
