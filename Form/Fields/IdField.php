<?php

namespace Impactaweb\Crud\Form\Fields;

/**
 * @param array $contexto Array com ID e VALOR
*/
class IdField
{
    protected $id = "id";
    protected $valor = "";

    public function __construct(string $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Renderiza o campo
     */
    public function render($dadosIniciais)
    {
        # Coloca a variável valor recebida do render
        $this->valor = $dadosIniciais[$this->id] ?? '';
        if ($this->valor == '') {
            return '';
        }
        return view($this->template, ["valor" => $this->valor, "id" => $this->id]);
    }
}
