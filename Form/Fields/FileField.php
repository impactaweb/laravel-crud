<?php

namespace Impactaweb\Crud\Form\Fields;


class FileField extends BaseField
{
    protected $dir = '';

    public function render(array $initial = [], array $rules = [])
    {
        $this->dir = $this->formatDir($this->dir);
        return parent::render($initial, $rules);
    }


    /**
     * Format given directory with necessary backslashes
     * @param string $dir
     * @return string
     */
    private function formatDir(string $dir)
    {
        if ($this->startsWith($dir, '/') === false) {
            $dir = '/' . $dir;
        }

        if ($this->endsWith($dir, '/') == false) {
            $dir = $dir . '/';
        }

        return $dir;
    }

    function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    function endsWith($string, $endString)
    {
        $len = strlen($endString);
        return (substr($string, -$len) === $endString);
    }

}
