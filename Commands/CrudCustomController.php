<?php

namespace Impactaweb\Crud\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CrudCustomController extends GeneratorCommand
{

    /**
     * O nome e a assinatura do comando do console.
     *
     * @var string
     */
    protected $name = 'crud:customcontroller';


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
    protected $type = 'Custom Controller';


    /**
     * Substitui o nome da classe para o stub fornecido.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $basedir = unbackSlash($this->option('basedir'));
        $basedir = str_replace('/', '\\', $basedir);
        $basename = $this->option('basename');
        $modelnamespace = str_replace('/', '\\', $this->option('modelna'));
        $class = str_replace($this->getNamespace($name).'\\', '', $name);
        $stub = str_replace([
            'DummyName',
            'DummyDir',
            'DummyClass',
            'DummyModel'], [
                $basename,
                $basedir,
                $class,
                $modelnamespace
            ], $stub);
        return $stub;
    }
    /**
     * Obtpem o arquivo stub para o gerador.
     *
     * @return string
     */
    protected function getStub()
    {
        return  app_path() . '/Console/Stubs/custom_controller.stub';
    }


    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers';
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
     * Obter opções do console
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['basedir', 'd', InputOption::VALUE_OPTIONAL, 'Nome base do diretório em comum da request, formulario e controller.'],
            ['basename', 'f', InputOption::VALUE_OPTIONAL, 'Nome base da classe em comum da request, formulario e controller.'],
            ['modelna', 'u', InputOption::VALUE_OPTIONAL, 'Namespace da model principal que o controller utilizará.']
        ];
    }
}
