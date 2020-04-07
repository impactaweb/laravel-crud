<?php

namespace Impactaweb\Crud\Listing;

use Illuminate\Database\Eloquent\Model;

class Field {

    public $type;
    public $name;
    public $label;
    public $activeByDefault = true;
    protected $callbackFunction;
    protected $mask;

    public function __construct(string $type, string $name, string $label, array $options = [])
    {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        
        if (isset($options['default']) && $options['default'] == false) {
            $this->activeByDefault = false;
        }

        if (isset($options['callback']) && is_callable($options['callback'])) {
            $this->callbackFunction = $options['callback'];
        }

        if (isset($options['mask']) && !empty($options['mask'])) {
            $this->mask = $options['mask'];
        }
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNameConverted()
    {
        return str_replace('.', '_', $this->name);
    }

    public function getIndexName()
    {
        $nameFull = explode('.', $this->getName());
        return end($nameFull);
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function activeByDefault()
    {
        return $this->activeByDefault;
    }

    public function getOrderbyLink(?array $currentOrderBy = [], array $allowedOrderbyColumns = []): string
    {
        $request = request();
        $fieldName = $this->getName();
        $className = '';
        if (in_array($fieldName, $allowedOrderbyColumns)) {

            if (($request->get('ord') ?? $currentOrderBy[0] ?? $fieldName) != $fieldName) {
                $direction = 'asc';
            } else {
                // Aqui, deve inverter o valor (desc vira asc, asc vira desc)
                $direction = strtolower($request->get('dir') ?? $currentOrderBy[1] ?? 'asc') == 'desc' ? 'asc' : 'desc';
            }

            if (($request->get('ord') ?? $currentOrderBy[0]) == $fieldName) {
                $className = ' class="order-' . $direction . '"';
            }

            $fullUrl = $request->fullUrlWithQuery(['ord' => $fieldName, 'dir' => $direction]);
            return '<a href="' . $fullUrl . '"'.$className.'>' . $this->getLabel() . '</a>';
        }

        return $this->getLabel();
    }

    // Formata o dado conforme parâmetros do campo (executa o callback, etc)
    public function formatData(Model $data): ?string
    {
        $columnName = $this->getIndexName();

        // Executando callbacks
        if (is_callable($this->callbackFunction)) {
            $functionToExecute = $this->callbackFunction;
            return $functionToExecute($data);
        }

        // Aplicando máscaras
        if ($this->mask) {
            $masks = config('listing.masks');
            if (is_array($masks) && array_key_exists($this->mask, $masks) && is_callable($masks[$this->mask])) {
                $func = $masks[$this->mask];
                return $func($data->$columnName);
            }
        }

        return $data->$columnName;
    }

}