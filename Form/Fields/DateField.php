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

}
