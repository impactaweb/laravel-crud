<?php

namespace Impactaweb\Crud\Listing;

class Field {

    public $name;
    public $label;
    public $activeByDefault = true;

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

}