<?php
namespace Impactaweb\Crud\Form\Fields;

use Exception;

class DateField extends BaseField
{
    protected $col = '3';

    /**
     * @inheritDoc
     */
    public function __construct(string $id, string $label, array $contexto, string $type)
    {
        $this->formatDates = config('form.fields.date.formatDates', true);
        $this->formatClient = (string) config('form.fields.date.formatClient', 'd/m/Y');
        $this->formatServer = (string) config('form.fields.date.formatServer', 'Y-m-d');
        parent::__construct($id, $label, $contexto, $type);
    }


    /**
     * Override initial value for date with format
     * @param array $initial
     * @throws Exception
     */
    protected function buildInitialValue(array $initial)
    {
        parent::buildInitialValue($initial);
        if ($this->formatDates && !empty($this->value)) {
            $this->value = $this->formatDate($this->value);
        }
    }

    /**
     * Format hour or date value
     * @param string $date
     * @param string $format
     * @return string
     */
    protected function formatDate(string $date) : string
    {
        return \Carbon\Carbon::createFromFormat($this->formatServer, $date)->format($this->formatClient);
    }


}
