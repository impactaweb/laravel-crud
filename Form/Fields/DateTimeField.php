<?php
namespace Impactaweb\Crud\Form\Fields;

use Exception;

class DateTimeField extends BaseField
{
    protected $col = '3';

    /**
     * @inheritDoc
     */
    public function __construct(string $id, string $label, array $contexto, string $type)
    {
        $this->formatDates = config('form.fields.dateTime.formatDates', true);
        $this->formatClient = (string) config('form.fields.dateTime.formatClient', 'd/m/Y H:i:s');
        $this->formatServer = (string) config('form.fields.dateTime.formatServer', 'Y-m-d H:i:s');
        parent::__construct($id, $label, $contexto, $type);
    }

}
