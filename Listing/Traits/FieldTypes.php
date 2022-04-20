<?php

namespace Impactaweb\Crud\Listing\Traits;

use Illuminate\Support\Facades\Storage;
use Impactaweb\Crud\Listing\Listing;

trait FieldTypes {

    /**
     * Cria um campo customizado via callback na listagem.
     *
     * @param string $label
     * @param callable $callbackFunction
     * @param array $options
     * @param string|null $name
     * @param string $type
     * @return void
     */
    public function custom(string $label, callable $callbackFunction, array $options = [], ?string $name = null, string $type = 'text')
    {
        if (!$name) {
            // Criando um nome aleatório para o campo
            $name = 'customfield.'
                . substr(strtolower(preg_replace("/[^A-Za-z0-9?!]/",'', $label)), 0, 20)
                . '_'
                . substr(md5(uniqid()),0,10);
        }

        $options['callback'] = $callbackFunction;
        $this->field($name, $label, $options, $type);
    }

    /**
     * Cria uma coluna do tipo "link" e monta a URL conforme parâmetros.
     * Aceita tags. Exemplo: /admin/{url.parameter_from_route}/{variable_from_data}
     *
     * @param string $linkName
     * @param string $urlWithParameters
     * @param array $options
     * @return void
     */
    public function link(string $linkName, string $urlWithParameters, array $options = null)
    {
        $callback = function($data) use ($linkName, $urlWithParameters, $options) {
            $linkClass = $options['class'] ?? '';

            // Target _blank é o padrão
            $target = ($options['blank'] ?? true) ? "_blank" : "_self";
            $url = Listing::fillUrlParameters($urlWithParameters, $data);
            return '<a href="' . $url . '" class="' . $linkClass . '" target="' . $target . '">' . $linkName . '</a>';
        };

        return $this->custom("", $callback);
    }

    /**
     * Cria uma coluna que recerá um "botão" na listagem.
     *
     * @param string $buttonName
     * @param string $urlWithParameters
     * @param string|null $buttonClass
     * @return void
     */
    public function button(string $buttonName, string $urlWithParameters, ?string $buttonClass = null)
    {
        $buttonClass = ($buttonClass && !empty($buttonClass)) ? $buttonClass : ' btn btn-default btn-sm ';
        return $this->link($buttonName, $urlWithParameters, ['class' => $buttonClass]);
    }

    /**
     * Exibe imagem na listagem conforme parâmetros passados. A url aceita tags.
     * maxWidth e maxHeight são 50x50 por padrão (mas pode ser alterado pelo dev)
     *
     * @param string $label
     * @param string $imageUrlWithParameters
     * @param integer $maxWidth
     * @param integer $maxHeight
     * @return void
     */
    public function image(string $label, string $imageUrlWithParameters, int $maxWidth = 50, int $maxHeight = 50)
    {
        $callback = function($data) use ($imageUrlWithParameters, $maxWidth, $maxHeight) {
            $url = Listing::fillUrlParameters($imageUrlWithParameters, $data);
            $maxWidthStyle = ($maxWidth > 0 ? ";max-width:" . $maxWidth . "px" : "");
            $maxHeightStyle = ($maxHeight > 0 ? ";max-height:" . $maxHeight . "px" : "");
            return '<img src="' . $url . '" style="' . $maxWidthStyle . $maxHeightStyle . '" />';
        };

        return $this->custom($label, $callback);
    }

    /**
     * Insere uma view do blade na coluna da listagem. Este blade recebe os dados do item a
     * ser listado na variável $data
     *
     * @param string $label
     * @param string $bladeViewPath
     * @param array $aditionalParameters
     * @return void
     */
    public function blade(string $label, string $bladeViewPath, array $aditionalParameters = [])
    {
        $callback = function($data) use ($label, $bladeViewPath, $aditionalParameters) {
            return view($bladeViewPath, compact('data', 'label', 'aditionalParameters'));
        };

        return $this->custom($label, $callback);
    }

    /**
     * Cria um campo do tipo "flag", com ícone ativado/desativado e ação para ativar/desativar
     * na própria tela via ajax.
     *
     * @param string $name
     * @param string $label
     * @param boolean $hasFlagLink
     * @return void
     */
    public function flag(string $name, string $label, $hasFlagLink = false)
    {
        $callback = function($data) use ($name, $hasFlagLink) {
            if(is_null($data->$name) && $hasFlagLink) {
                return "
                    <a href='javascript:;' data-double-flag='off' class='flagItem flag-off' data-field='{$name}' title='Desativar' style='margin-right: 4px'>0</a>
                    <a href='javascript:;' data-double-flag='on' class='flagItem flag-on' data-field='{$name}' title='Ativar'>1</a>
                ";
            }

            if (!isset($data->$name)) {
                return 'ERRO';
            }

            if ($hasFlagLink) {
                return '<a href="javascript:;" class="flagItem '
                        . ($data->$name == 1 ? 'flag-on' : 'flag-off')
                        .' " data-field="' . $name . '">'
                        . $data->$name
                        . '</a>';
            } else {
                return '<span class="flagItem '
                        . ($data->$name == 1 ? 'flag-on' : 'flag-off')
                        . '">'
                        . $data->$name
                        . '</span>';
            }
        };

        return $this->custom($label, $callback, [], $name, 'flag');
    }

    /**
     * Cria um campo padrão, adicionando itens para a busca avançada no formato <select>
     *
     * @param string $name
     * @param string $label
     * @param array $searchOptions
     * @param array $options
     * @return void
     */
    public function select(string $name, string $label, array $searchOptions, array $options = [], ?string $searchField = null)
    {
        if (is_null($searchField)) {
            $searchField = $name;
        }
        $options['searchOptions'] = $searchOptions;
        if (!is_null($searchField)) {
            $options['searchField'] = $searchField;
            $this->addSelectFields($searchField);
            $this->addColumnAlias($searchField, $searchField);
        }

        return $this->field($name, $label, $options, 'select');
    }

    /**
     * Cria um link já inserindo no início a url do storage. Caso o $disk não deja definido, será o default
     * do app.
     *
     * @param string $name
     * @param string $label
     * @param string $urlWithParameters Asdasdasdas
     * @param array $options
     * @param string|null $disk
     * @return void
     */
    public function storageLink(string $name, string $label, string $urlWithParameters = "", array $options = [], ?string $disk = null)
    {
        if ($disk) {
            $baseUrl = Storage::disk($disk)->url("");
        } else {
            $baseUrl = Storage::url("");
        }

        $callback = function($data) use ($name, $label, $baseUrl, $urlWithParameters, $options) {
            if (!$data->$name ?? false) {
                return "";
            }
            $linkClass = $options['class'] ?? '';

            // Se a URL não for setada, ela recebe o nome do campo
            if (empty($urlWithParameters)) {
                $urlWithParameters = $data->$name;
            }

            // Target _blank é o padrão
            $target = ($options['blank'] ?? true) ? "_blank" : "_self";
            $urlWithParameters = rtrim($baseUrl, '/') . '/' . ltrim($urlWithParameters, '/');
            $url = Listing::fillUrlParameters($urlWithParameters, $data);
            return '<a href="' . $url . '" class="' . $linkClass . '" target="' . $target . '">' . $label . '</a>';
        };

        return $this->custom("", $callback);
    }

    /**
     * Cria uma coluna padrão do tipo "text" (exibe o campo sem modificar seu conteúdo)
     *
     * @param string $name
     * @param string $label
     * @param array $options
     * @return void
     */
    public function text(string $name, string $label, array $options = [])
    {
        return $this->field($name, $label, $options, 'text');
    }


    /**
     * Cria uma coluna padrão do tipo "data"
     *
     * @param string $name
     * @param string $label
     * @param array $options
     * @return void
     */
    public function date(string $name, string $label, array $options = [])
    {
        return $this->field($name, $label, $options, 'date');
    }

}
