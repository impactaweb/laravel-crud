<?php

namespace Impactaweb\Crud\Form\Fields;

require_once __DIR__ . '/../Helpers/Helpers.php';

class FileField extends BaseField
{
    protected $dir = '';

    public function render(array $initial = [], array $rules = [])
    {
        $this->formatDir();
        return parent::render($initial, $rules);
    }


    /**
     * Format given directory with necessary backslashes
     */
    private function formatDir()
    {

        if (startsWith($this->dir, '/') === false) {
            $this->dir = '/' . $this->dir;
        }

        if (endsWith($this->dir, '/') == false) {
            $this->dir = $this->dir . '/';
        }
    }
}
