<?php


namespace Impactaweb\Crud\Form\Traits;

trait Fields
{
    /**
     * Campo de texto
     * @param string $name
     * @param string $label
     * @param array $options
     * @return mixed
     */
    public function text(string $name, string $label, array $options = [])
    {
        return $this->field('text', $name, $label, $options);
    }

    public function file(string $name, string $label, string $diretorio, $options = [])
    {
        # Verifica se há coringas de rotas no diretório
        $parametros = request()->route()->originalParameters();
        foreach ($parametros as $nome => $valor) {
            $diretorio = str_replace("{url.$nome}", $valor, $diretorio);
        }

        # Verifica se há coringas de storage no diretório
        $storageDefault = config('filesystems.default');
        $disks = config('filesystems.disks');

        $url = rtrim($disks[$storageDefault]['url'] ?? '', '/');
        $diretorio = str_replace("{storagePath}", $url, $diretorio);

        foreach ($disks as $disk => $diskConfigurations) {
            $url = rtrim($diskConfigurations['url'] ?? '', '/');
            $diretorio = str_replace("{storage.$disk}", $url, $diretorio);
        }
        return $this->field('file', $name, $label, array_merge(['dir' => $diretorio], $options));
    }

    /**
     * Campo de números
     * @param string $name
     * @param string $label
     * @param array $options
     * @return mixed
     */
    public function password(string $name, string $label, array $options = [])
    {
        return $this->field('password', $name, $label, $options);
    }

    /**
     * @param string $name
     * @param string $label
     * @param array $options
     * @return mixed
     */
    public function number(string $name, string $label, array $options = [])
    {
        return $this->field('number', $name, $label, $options);
    }

    /**
     * Campo com select
     * @param string $name
     * @param string $label
     * @param array $options
     * @return mixed
     */
    public function select(string $name, string $label, array $selectOptions = [], array $options = [])
    {
        return $this->field('select', $name, $label, array_merge(['selectOptions' => $selectOptions], $options));
    }

    /**
     * Campo Text Area
     * @param string $name
     * @param string $label
     * @param array $options
     * @return mixed
     */
    public function textArea(string $name, string $label, array $options = [])
    {
        return $this->field('textarea', $name, $label, $options);
    }

    /**
     * Campo de multiSelect
     * @param string $name
     * @param string $label
     * @param array $selectOptions
     * @param array $options
     * @return mixed
     */
    public function multiSelect(string $name, string $label, array $selectOptions = [], array $options = [])
    {
        return $this->field('multiselect', $name, $label, array_merge(['selectOptions' => $selectOptions], $options));
    }

    /**
     * Campo flag, "Sim" ou "Não"
     * @param string $name
     * @param string $label
     * @param array $options
     * @return mixed
     */
    public function flag(string $name, string $label, array $options = [])
    {
        return $this->field('flag', $name, $label, $options);
    }

    /**
     * Campo com conteúdo HTML livre
     * @param $conteudo
     * @return mixed
     */
    public function html($conteudo)
    {
        return $this->field('html', '', '', ['content' => $conteudo]);
    }

    /**
     * Campo para mostrar IDS e links
     * Caso o conteúdo for vazio, ele esconde o campo
     * @param $label
     * @param $conteudo
     * @return mixed
     */
    public function show($label, $conteudo, $hideEmpty = true)
    {
        if ($hideEmpty && empty($conteudo)) {
            return $this;
        }
        return $this->field('show', '', $label, ['content' => $conteudo]);
    }

    /**
     * Campo oculto
     * @param $name
     * @param $label
     * @param $conteudo
     * @return mixed
     */
    public function hidden($name, array $options = [])
    {
        return $this->field('hidden', $name, '', $options);
    }

    /**
     * Campo de data
     * @param string $name
     * @param string $label
     * @param array $options
     * @return mixed
     */
    public function dateTime(string $name, string $label, array $options = [])
    {
        return $this->field('datetime', $name, $label, $options);
    }

    /**
     * Campo de hora
     * @param string $name
     * @param string $label
     * @param array $options
     * @return mixed
     */
    public function time(string $name, string $label, array $options = [])
    {
        return $this->field('time', $name, $label, $options);
    }

    /**
     * Editor de Rich Text
     * @param string $name
     * @param array $options
     * @return mixed
     */
    public function rtf(string $name, array $options = [])
    {
        return $this->field('rtf', $name, '', $options);
    }

    /**
     * MultiSelect com grupos
     * @param string $name
     * @param string $label
     * @param array $selectOptions
     * @param array $options
     * @return mixed
     */
    public function multiSelectGroup(string $name, string $label, array $selectOptions = [], array $options = [])
    {
        return $this->field('multiselectgroup', $name, $label, array_merge(['selectOptions' => $selectOptions], $options));
    }

    /**
     * Search Field
     * @param string $name
     * @param string $label
     * @param string $url
     * @param string $coluna
     * @return mixed
     */
    public function search(string $name, string $label, string $url, string $search)
    {
        return $this->field('search', $name, $label, ['url' => $url, 'search' => $search]);
    }


}
