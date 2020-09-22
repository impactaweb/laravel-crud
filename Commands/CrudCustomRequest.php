<?php

namespace Impactaweb\Crud\Commands;

use Exception;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CrudCustomRequest extends GeneratorCommand
{

    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $name = 'crud:customrequest';


    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Cria uma nova classe de request';

    /**
     * O tipo de classe sendo gerada.
     *
     * @var string
     */
    protected $type = 'Custom Request';


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

        if (!class_exists($namespace)) {
            throw new Exception("A classe $namespace não foi encontrada", 1);
        }

        $model = new $namespace;

        $fields = DB::select("describe {$model->getTable()}");

        $editedFields = [];

        foreach ($fields as $field) {
            $rules = $this->buildRequestRules($field);
            $editedFields[] = "// '{$field->Field}' => [{$rules}],";
        }

        $newFields = implode("\n" . str_repeat(' ', 12), $editedFields);


        $stub = str_replace('{{ myRules }}', $newFields, $stub);

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
        return  __DIR__ . '/../Form/Resources/stubs/custom_request.stub';
    }


    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Requests';
    }

    /**
     * Obtém os argumentos do comando do console.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Nome do request.'],
        ];
    }


    /**
     * Fazer regras de acordo com o tipo de dado que o campo recebe
     * @param object $field
     * @return string Deve ser inserida nas rules
     */
    private function buildRequestRules(object $field): string
    {
        preg_match('/^varchar\((\d+)\)$/', $field->Type, $output);

        if(!empty($output)) {
            $max = "max:{$output[1]}";
            return "'required', '{$max}'";
        }

        if($field->Type === 'date' || $field->Type === 'timestamp') {
            return "'required', 'date_format:FORMATO'";
        }
        return "'required'";
    }


    /**
     * Obter opções do console
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['modelna', 'l', InputOption::VALUE_OPTIONAL, 'O namespace da model que o request irá refereciar exe: App/Models/Foo'],
        ];
    }
}
