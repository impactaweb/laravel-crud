<?php

namespace Impactaweb\Crud\Form\Fields;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

/**
 * Class BaseField
 */
class BaseField
{
    /**
     * @var string
     */
    public $id = "";
    /**
     * @var string
     */
    protected $label = "";
    /**
     * @var array
     */
    protected $attrs = [];
    /**
     * @var array
     */
    public $options = [];
    /**
     * @var string
     */
    protected $help = "";
    /**
     * @var string
     */
    protected $class = "";
    /**
     * @var string
     */
    protected $value = "";
    /**
     * @var bool
     */
    protected $required = false;
    /**
     * @var string
     */
    protected $template_name = '';
    /**
     * @var bool
     */
    protected $hidden = false;
    /**
     * @var string
     */
    protected $col = '10';

    /** @var string|null */
    protected $format = null;

    /** @var string|null */
    protected $nullable = false;

    protected $ajax = [];


    /**
     *
     * BaseField constructor.
     * @param string $id
     * @param string $label
     * @param array  $options
     * @throws Exception
     */
    public function __construct(string $id, string $label, array $options, string $type)
    {
        $this->id = $id;
        $this->label = $label;
        $this->options = $options;

        # Get field template
        $this->getTemplate($type);

        # Set all options as local variables
        $this->setOptions($options);
        return $this;
    }

    public function getTemplate(string $type)
    {
        $this->template_name = config('form.templates.fields.' . $type);
        if (!View::exists($this->template_name)) {
            throw new Exception("Template " . $this->template_name . ' não existe!');
        }
    }

    public function setOptions(array $options)
    {
        foreach ($options as $attr => $value) {
            $this->$attr = $value;
        }
    }

    /**
     * Returns field HTML
     * @param array $initial
     * @param array $rules
     * @return Factory|\Illuminate\View\View
     * @throws Exception
     */
    public function render(array $initial = [], array $rules = [])
    {
        $this->buildInitialValue($initial);
        $this->buildRules($rules, $initial);
        $this->clearAttrs();
        $this->buildAjax($this->options);

        try {
            # The function get_object_vars, will put all class local variables inside blade template
            $html = view($this->template_name, get_object_vars($this));
        } catch (Exception $e) {
            throw new Exception("Field template not found.", 1);
        }
        return $html;
    }

    /**
     * @param array $initial
     * @throws Exception
     */
    protected function buildInitialValue(array $initial)
    {
        if (!isset($initial[$this->id])) {
            return;
        }

        $this->value = $initial[$this->id];
        if (getType($this->value) != 'string' && !is_numeric($this->value)) {
            throw new Exception("Field " . $this->id .
                ' requires string or numeric, ' . getType($this->value) . ' given');
        }
    }

    /**
     * Build field rules
     * @param array $rules
     */
    private function buildRules(array $rules, array $initial)
    {
        $rule = $rules[$this->id] ?? [];

        foreach ($rule as $item) {

            // Se não for string, ignorar a regra
            if (getType($item) != 'string') {
                continue;
            }

            if ($item == 'required') {
                $this->required = true;
            }

            if ($item == 'nullable') {
                $this->nullable = true;
            }

            if (strpos($item, 'max') !== false) {
                $this->attrs['maxlength'] = str_replace('max:', '', $item);
            }
            if (strpos($item, 'min') !== false) {
                $this->attrs['min'] = str_replace('min:', '', $item);
            }

            if (Str::startsWith($item, 'required_without')) {
                $idRequired = Str::replaceFirst('required_without:', '', $item);
                if (!isset($initial[$idRequired])) {
                    $this->required = true;
                }
            };

            if (Str::startsWith($item, 'required_if')) {
                $idRequired = Str::replaceFirst('required_if:', '', $item);
                if (isset($initial[$idRequired])) {
                    $this->required = true;
                }
            };

        }
    }

    /**
     * Clear not necessary attrs
     */
    private function clearAttrs()
    {
        # If the user has entered the class in ATTRS array,
        # this class will be added to the element's main class
        # and then will be removed from attrs, to avoid conflict
        if (isset($this->attrs['class'])) {
            $this->class .= ' ' . $this->attrs['class'];
            unset($this->attrs['class']);
        }

        # If user enters an ID in the ATTRS
        # The id will be store in $this->id, then will be unset
        if (isset($this->attrs['id'])) {
            if (empty($this->id)) {
                $this->id = $this->attrs['id'];
            }
            unset($this->attrs['id']);
        }
    }

    /**
     * @param array $options
     */
    public function buildAjax(array $options)
    {
        if (!isset($options['ajax'])) {
            return;
        }

        $this->attrs['data-ajax-url'] = $options['ajax']['url'] ?? '';
        $this->attrs['data-ajax-fields'] = json_encode($options['ajax']['fields'] ?? []);
        $this->attrs['data-ajax-fields-options'] = json_encode($options['ajax']['fieldsOptions'] ?? []);
        $this->attrs['data-ajax-method'] = $options['ajax']['method'] ?? 'GET';
        $this->attrs['data-ajax-event'] = $options['ajax']['event'] ?? 'change';
        $this->attrs['data-ajax-data'] = json_encode($options['ajax']['data'] ?? []);
        $this->attrs['data-ajax-data-fields'] = json_encode($options['ajax']['dataFields'] ?? []);
    }

}
