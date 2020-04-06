<?php

namespace Impactaweb\Crud\Listing;

class Field {

    public $name;
    public $label;
    public $activeByDefault = true;
    public $flagOrderby = true;

    public function __construct(string $name, string $label, array $options = [])
    {
        $this->name = $name;
        $this->label = $label;
        
        if (isset($options['default']) && $options['default'] == false) {
            $this->activeByDefault = false;
        }
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

    public function getFieldOrderbyLink()
    {
        $request = request();
        $fieldName = $this->getName();
        $className = '';
        if ($this->flagOrderby) {

            if (($request->get('ord') ?? $fieldName) != $fieldName) {
                $direction = 'asc';
            } else {
                // Aqui, deve inverter o valor (desc vira asc, asc vira desc)
                $direction = strtolower($request->get('dir') ?? 'asc') == 'desc' ? 'asc' : 'desc';
            }

            if ($request->get('ord') == $fieldName) {
                $className = ' class="order-' . $direction . '"';
            }

            $fullUrl = $request->fullUrlWithQuery(['ord' => $fieldName, 'dir' => $direction]);
            return '<a href="' . $fullUrl . '"'.$className.'>' . $this->getLabel() . '</a>';
        }

        return $this->getLabel();
    }

}