<?php

namespace Impactaweb\Crud\Listing;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class FieldCollection implements Countable, IteratorAggregate {
    public $fields = [];
    public $fieldsToShow = [];
    public $fieldsName = [];
    public $aditionalActiveFields = [];

    public function count()
    {
        return count($this->fields);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->fields);
    }

    public function add(Field $field)
    {
        $this->fieldsName[] = $field->getName();
        $this->fields[] = $field;
    }

    public function getActiveFields($onlyName = false)
    {
        $activeFields = [];
        foreach ($this->fields as $field) {
            if ($field->activeByDefault() || in_array($field->getName(), $this->aditionalActiveFields)) {
                $activeFields[] = $onlyName ? $field->getName() : $field;
            }
        }

        return $activeFields;
    }

    public function exists($fieldName)
    {
        return in_array($fieldName, $this->fieldsName);
    }

    public function getFieldsName()
    {
        return $this->fieldsName;
    }

    public function getAllFields(): array
    {
        $fields = [];
        foreach ($this->fields as $field) {
            $prefixName = explode(".", $field->getName())[0];
            if ($prefixName == 'customfield') {
                continue;
            }
            $fields[] = $field;
        }
        return $fields;
    }

}
