<?php

namespace Impactaweb\Crud\Commands;

use Exception;
use Illuminate\Console\Command;

require_once __DIR__ . '/../Helpers/Helpers.php';

class CrudCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:create
                                    {name  :  Nome da classe}
                                    {--modelna=  :  Namespace da model a ser usada no crud Ex: App/Models/Cargos}
                                    {--wc=1  :  Com controller ? 1-sim 0-não}
                                    {--wr=1  : Com request ? 1-sim 0-não}
                                    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um CRUD com base nos campos de uma model';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $name = $this->argument('name');
            $modelnamespace = unbackSlash($this->option('modelna'));

            $withRequest = (int) $this->option('wr');
            $withController = (int) $this->option('wc');

            $folder = $this->ask('Digite o padrão de pastas das classes (Exe: Admin/Concursos):');

            $folder = unbackSlash($folder);

            if ($withRequest) {
                $this->createRequest($name, $folder, $modelnamespace);
            }

            if ($withController) {
                $this->createController($name, $folder, $modelnamespace);
            }

            $this->createForm($name, $folder, $modelnamespace);

        } catch (Exception $exception) {
            $this->error('OPSS!: ' . $exception->getMessage());
        }

    }

    private function createRequest(string $name,string $folder, string $modelnamespace): void
    {
        $this->line('Criando Custom Request...');

        $requestName = "{$folder}/{$name}Request";
        $this->call('crud:customrequest', [
            'name' => $requestName,
            '--modelna' => $modelnamespace
        ]);
    }

    private function createForm(string $name,string $folder, string $modelnamespace): void
    {
        $this->line('Criando Formulario...');

        $formName = "{$folder}/{$name}Form";
        $this->call('crud:form', [
            'name' => $formName,
            '--modelna' => $modelnamespace
        ]);
    }

    private function createController(string $name,string $folder, string $modelnamespace): void
    {
        $this->line('Criando Custom Controller...');

        $formName = "{$folder}/{$name}Controller";
        $this->call('crud:customcontroller', [
            'name' => $formName,
            '--basename' => $name,
            '--basedir' => $folder,
            '--modelna' => $modelnamespace
        ]);
    }
}
