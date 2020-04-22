<?php

namespace Impactaweb\Crud\Form\Fields;


class FileField extends BaseField
{
    protected $dir = '';

    public function render(array $initial = [], array $rules = [])
    {
        if (!empty($this->dir)) {
            $this->dir = '/' . ltrim($this->dir, '/');
        }
        return parent::render($initial, $rules);
    }
}
