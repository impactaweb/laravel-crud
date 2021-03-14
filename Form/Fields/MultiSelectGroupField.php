<?php

namespace Impactaweb\Crud\Form\Fields;

class MultiSelectGroupField extends BaseField
{
    protected $filter = false;
    protected $value = [];
    protected $selectOptions;

    protected function buildInitialValue(array $initial): void
    {
        parent::buildInitialValue($initial);
        if (!is_array($this->value)) {
            $this->value = [];
        }
    }
}
