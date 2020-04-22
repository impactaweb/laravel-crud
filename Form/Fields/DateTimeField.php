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
        $this->formatClient = (string) config('form.fields.dateTime.formatClient', 'YYYY-MM-DD hh:mm:ss');
        $this->formatServer = (string) config('form.fields.dateTime.formatServer', 'YYYY-MM-DD hh:mm:ss');
        parent::__construct($id, $label, $contexto, $type);
    }

}
