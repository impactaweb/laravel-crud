<?php
namespace Impactaweb\Crud\Form\Fields;

use Exception;

class DateField extends BaseField
{
    protected $col = '3';
    public $formatDates;
    public $formatClient;
    public $formatServer;

    /**
     * @inheritDoc
     */
    public function __construct(string $id, string $label, array $options, string $type)
    {
        $this->formatDates = config('form.fields.date.formatDates', true);
        $this->formatClient = (string) config('form.fields.date.formatClient', 'YYYY-MM-DD');
        $this->formatServer = (string) config('form.fields.date.formatServer', 'YYYY-MM-DD');
        parent::__construct($id, $label, $options, $type);
    }

}
