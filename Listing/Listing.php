<?php

namespace Impactaweb\Crud\Listing;

use Impactaweb\Crud\Listing\DataSource;
use Impactaweb\Crud\Listing\Field;
use Impactaweb\Crud\Listing\FieldCollection;

class Listing {

    protected $primaryKey;
    protected $dataSource;
    protected $actions = ['new', 'edit', 'destroy'];
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

        if (isset($options['actions']) && is_array($options['actions'])) {
            $this->actions = $options['actions'];
        }

        if (isset($options['pp']) && is_numeric($options['pp'])) {
            $this->perPagePagination = $options['pp'];
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
        $actions = [];
        $advancedSearchFields = [];
        $columns = $this->fields;
        $pagination = $data;

        return view($viewFile, compact('data', 'actions', 'advancedSearchFields', 'columns', 'pagination'));
    }

    // consulta os dados no bd
    public function performQuery()
    {
        $activeColumns = $this->fields->getActiveFields(true);
        $queryString = request()->query();
        $orderby = $this->getOrderby();

        // Add columns from where request to select
        foreach ($this->fields->getFieldsName() as $fieldName) {
            $fieldNameQuerystring = str_replace('.', '_', $fieldName);
            if (isset($queryString[$fieldNameQuerystring]) && !empty($queryString[$fieldNameQuerystring])) {
                $activeColumns[] = $fieldName;
            }
        }

        return $this->dataSource->getData($activeColumns, $orderby, $this->perPagePagination, $queryString);
    }

    // Get orderby based on default set value or request()->get
    public function getOrderby(): ?array
    {
        $orderby = request()->get('ord') ?? $this->defaultOrderby[0];
        if (!$orderby) {
            return null;
        }
        $direction = request()->get('dir') ? (strtoupper(request()->get('dir')) == 'DESC' ? 'DESC' : 'ASC') : $this->defaultOrderby[1];

        if (!$this->fields->exists($orderby)) {
            return null;
        }

        return [$orderby, $direction];
    }

}