<?php

namespace Impactaweb\Crud\Form\Fields;

class MultiSelectGroupField extends BaseField
{
    protected $filter = false;
    protected $value = [];
    protected $selectOptions;

    protected function buildInitialValue(array $initial)
    {
        if (isset($initial[$this->id])) {
            $this->value = $initial[$this->id];
            if (getType($this->value) != 'array') {
                throw new \Exception("Multiselect requires array, " . getType($this->value) . ' given.');
            }
        }
    }
}
