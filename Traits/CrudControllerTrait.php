<?php

namespace Impactaweb\Crud\Traits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Impactaweb\Crud\Form\FormUrls;

trait CrudControllerTrait
{

    /**
     * Faz a seleção para qual URL o formulário direcionará
     * o usuário, com base no botão clicado para submeter
     * @paramm $idNovo ID do nova Model
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirecionar($idNovo = null)
    {
        return response()->json(['url' => FormUrls::redir('auto', $idNovo)]);
    }

    /**
     * Salva a model de acordo com dados da Request
     * @param array $dadosRequest
     * @param array $relations
     * @return mixed
     */
    protected function salvar(array $dadosRequest, array $relations = [])
    {
        $model = new $this->model();
        $modelInstance = $model->saveFromRequest($dadosRequest, $relations);
        return $modelInstance;

    }

    /**
     * Salva e redireciona sem sequencia
     * @param array $dadosRequest
     * @param array $relations
     * @return \Illuminate\Http\JsonResponse
     */

    protected function salvarRedirecionar(array $dadosRequest, array $relations = [])
    {
        try {
            $modelId = $this->salvar($dadosRequest, $relations)->getKey();
            return $this->redirecionar($modelId);
        } catch (\Exception $e){
            return response()->json(['errors' => $e->getMessage()]);
        }

    }

    public function store(Request $request)
    {
        if ($this->validation) {
            $validation = new $this->validation();
            $request->validate($validation->rules());
        }

        return $this->salvarRedirecionar($request->all(), $this->relations);
    }

    public function update(Request $request)
    {
        if ($this->validation) {
            $validation = new $this->validation();
            $request->validate($validation->rules());
        }

        return $this->salvarRedirecionar($request->all(), $this->relations);
    }

    /**
     * Método desativado
     */
    public function show()
    {
        abort(404);
    }


}
