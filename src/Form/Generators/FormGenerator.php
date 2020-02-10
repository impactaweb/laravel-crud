<?php

namespace Impactaweb\Crud\Form\Generators;

class FormGenerator
{

    /**
     * @var array
     */
    protected $descricaoSql = [];

    /**
     * PSFormBuilder constructor.
     * Recebe array (describe) do SQL
     * @param array $descricaoSql
     */
    public function __construct(array $descricaoSql)
    {
        $this->descricaoSql = $descricaoSql;
    }

    private $camposSql = [
        'timestamp' => 'datetime',
        'tinyint' => 'flag',
        'text' => 'text',
        'float' => 'number',
        'varchar' => 'text',
        'date' => 'date',
        'datetime' => 'datetime',
        'boolean' => 'flag',
        'mediumtext' => 'rtf',
        'longtext' => 'rtf',
        'time' => 'time',
        'char' => 'text',
        'int' => 'number',
    ];


    /**
     * Gera string completa de todos os campos
     * @return string
     */
    public function gerarCampos()
    {
        $dadosCampos = '';
        if (empty($this->descricaoSql)) {
            return '';
        }
        foreach ($this->descricaoSql as $campo) {
            $dadosCampos .= $this->gerarCampo($campo);
        }
        return $dadosCampos;
    }

    /**
     * Gera a string de um campo único
     * @param object $campo
     * @return string
     */
    public function gerarCampo(object $campo)
    {
        # Ignora chaves primárias
        if ($campo->Key == 'PRI') {
            return '';
        }

        # Tipo do Campo
        $campoObj = new FieldGenerator();
        $campoObj->tipoCampo = $this->identificaTipoCampo($campo);
        if ($campoObj->tipoCampo == 'select') {
            $campoObj->arrayCampo['options'] = [];
        }

        # ID do campo
        $id = $this->identificaIdCampo($campo);
        $campoObj->idCampo = $id;

        # Nome do campo
        $campoObj->labelCampo = $this->identificaNomeCampo($campo);

        return $campoObj->montarCampo();

    }

    /**
     * Traduz tipo do campo SQL para PROSELETA
     * @param $campo
     * @return string
     */
    protected function identificaTipoCampo($campo)
    {
        # Verifica se é chave estrangeira
        if (substr($campo->Field, 0, strlen('id_')) === 'id_') {
            return 'select';
        }

        # Verifica se é flag
        if (substr($campo->Field, 0, strlen('flag_')) === 'flag_') {
            return 'flag';
        }

        foreach ($this->camposSql as $tipoSql => $tipoPS)
        {
            if (substr($campo->Type, 0, strlen($tipoSql)) === $tipoSql) {
                return $tipoPS;
            }
        }
        return 'texto';

    }

    /**
     * Identifica o ID do campo
     * @param $campo
     * @return mixed
     */
    protected function identificaIdCampo($campo)
    {
        return $campo->Field;
    }

    private function identificaNomeCampo($campo)
    {
        $nomeCampo = str_replace('id_', '', $campo->Field);
        $nomeCampo = str_replace('_', ' ', $nomeCampo);
        return ucfirst($nomeCampo);
    }

}
