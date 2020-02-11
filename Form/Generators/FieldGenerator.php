<?php

namespace Impactaweb\Crud\Form\Generators;

/**
 * Class CampoBuilder
 * Classe utiliada para criação de CAMPOS de formulário automaticamente
 * @package App\Lib\Formulario
 */
class FieldGenerator {

    /**
     * Tipo do campo (AliasCampos.php)
     * @var string
     */
    public $tipoCampo = '';

    /**
     * @var string
     */
    public $labelCampo = '';

    /**
     * @var string
     */
    public $idCampo = '';

    /**
     * Array que guarda informações
     * para montagem do campo
     * @var array
     */
    public $arrayCampo = [];

    /**
     * String padrão para ser utilizada na construção do campo
     * @var string
     */
    protected $template = "->field('%s', '%s', '%s', [%s])\n                ";

    /**
     * Monta a string do campo
     * @return string
     */
    public function montarCampo()
    {
        $camposString = [];
        foreach ($this->arrayCampo as $ind => $valor)
        {
            if (getType($valor) == 'array') {
                $camposString[] = "'{$ind}' => [],";
            } else {
                $camposString[] = "'{$ind}' => '{$valor}',";
            }
        }
        $camposString = implode("", $camposString);
        return sprintf($this->template, $this->tipoCampo, $this->idCampo, $this->labelCampo, $camposString);
    }

}
