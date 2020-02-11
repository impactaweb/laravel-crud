<?php

namespace Impactaweb\Crud\Commands;

use Impactaweb\Crud\Form\Generators\FormGenerator;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CrudForm extends GeneratorCommand
{

    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $name = 'crud:form';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Cria uma nova classe de Formulário';

    /**
     * O tipo de classe sendo gerada.
     *
     * @var string
     */
    protected $type = 'Form';

    /**
     * Substitui o nome da classe para o stub fornecido.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $namespace = $this->option('modelna');
        $namespace = str_replace('/', '\\', $namespace);

        if (class_exists($namespace)) {
            $model = new $namespace;
        } else {
            $this->error('O namespace da model informada não existe');
            exit(1);
        }

        $describe = DB::select("describe {$model->getTable()}");
        $formBuider = new FormGenerator($describe);

        $this->line('Gerando os campos do formulario...');

        $fields = $formBuider->gerarCampos();
        $stub = str_replace('{{ myFields }}', $fields, $stub);
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace('DummyClass', $class, $stub);
    }


    /**
     * Obtpem o arquivo stub para o gerador.
     *
     * @return string
     */
    protected function getStub()
    {
        return  __DIR__ . '/../Form/Resources/stubs/custom_form.stub';
    }


    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Forms';
    }

    /**
     * Obtém os argumentos do comando do console.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Nome do formulário.'],
        ];
    }

    /**
     * Obter opções do console
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['modelna', 'm', InputOption::VALUE_OPTIONAL, 'O namespace da model que o request irá refereciar exe: App/Models/Foo'],
        ];
    }
}
