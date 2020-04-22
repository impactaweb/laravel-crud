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
    protected $id = "";
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
    protected $options = [];
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


    /**
     *
     * BaseField constructor.
     * @param string $id
     * @param string $label
     * @param array  $contexto
     * @throws Exception
     */
    public function __construct(string $id, string $label, array $contexto, string $type)
    {
        $this->template_name = config('form.templates.fields.' . $type);
        if (!View::exists($this->template_name)) {
            throw new Exception("Template " . $this->template_name . ' não existe!');
        }
        $this->id = $id;
        $this->label = $label;

        foreach ($contexto as $atributo => $parametro) {
            $this->$atributo = $parametro;
        }
        return $this;
    }

    /**
     * @param array $initial
     * @throws Exception
     */
    protected function buildInitialValue(array $initial)
    {
        if (isset($initial[$this->id])) {
            $this->value = $initial[$this->id];
            if (getType($this->value) != 'string' && !is_numeric($this->value)) {
                throw new Exception("Field " . $this->id .
                    ' requires string or numeric, ' . getType($this->value) . ' given');
            }
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

        # Constrói as regras
        $this->buildRules($rules, $initial);

        # Caso o usuário tenha informado a classe no array ATTRS,
        # essa classe será adicionada na classe principal do elemento
        # e depois removida do attrs, para evitar conflito
        if (isset($this->attrs['class'])) {
            $this->class .= ' ' . $this->attrs['class'];
            unset($this->attrs['class']);
        }

        # Caso o usuário informe o ID no ATTRS
        # O programa irá coloca-lo na variável ID se vazia
        # depois vai remove-la para evitar conflito
        if (isset($this->attrs['id'])) {
            if (empty($this->id)) {
                $this->id = $this->attrs['id'];
            }
            unset($this->attrs['id']);
        }

        try {
            # A função get_object_vars, irá jogar todas as variaveis da classe
            # dentro do template do blade
            $html = view($this->template_name, get_object_vars($this));
        } catch (Exception $e) {
            throw new Exception("Field template not found.", 1);
        }
        return $html;
    }


    /**
     * Build field rules
     * @param array $rules
     */
    private function buildRules(array $rules, array $initial)
    {
        $rule = $rules[$this->id] ?? [];

        foreach ($rule as $item) {
            if ($item == 'required') {
                $this->required = true;
            }

            if ($item == 'nullable') {
                $this->nullable = true;
            }

            if (getType($item) == 'string' && strpos($item, 'max') !== false) {
                $this->attrs['maxlength'] = str_replace('max:', '', $item);
            }
            if (getType($item) == 'string' && strpos($item, 'min') !== false) {
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
     * Format hour or date value
     * @param string $date
     * @param string $format
     * @return string
     */
    protected function formatDate(string $date, string $format) : string
    {
        $haveTime = strpos($date, ':');
        if($haveTime && mb_strlen($date) > 1 && mb_strlen($date) <= 8) {

            $date = count(explode(':', $date)) === 3 ? $date : $date . ':00';
            $date = date('H:i:s',strtotime($date));

            return $date;
        }
        return date_format(date_create($date), $format);
    }
}
