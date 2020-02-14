<?php

namespace Impactaweb\Crud\Form\Fields;


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

        if ($this->startsWith($this->dir, '/') === false) {
            $this->dir = '/' . $this->dir;
        }

        if ($this->endsWith($this->dir, '/') == false) {
            $this->dir = $this->dir . '/';
        }
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
