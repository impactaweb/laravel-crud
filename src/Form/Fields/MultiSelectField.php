<?php

namespace Impactaweb\Crud\Form\Fields;

class MultiSelectField extends BaseField
{
    protected $filter = false;
    protected $value = [];

    /**
     * @inheritDoc
     * @throws \Exception
     */
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
