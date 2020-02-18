<?php

namespace Impactaweb\Crud\Traits;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Impactaweb\Crud\Form\FormUrls;
use App;
trait CrudControllerTrait
{

    /**
     * Faz a seleção para qual URL o formulário fará o redirecionamento
     * do usuário, com base no botão clicado para submeter
     * @paramm $idNovo ID do nova Model
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirecionar($idNovo = null)
    {
        return response()->json(['url' => FormUrls::redir('auto', $idNovo)]);
    }

    /**
     * Salva a model de acordo com dados da Request
     * @param array $requestData
     * @param array $relations
     * @return mixed
     */
    protected function salvar(array $requestData, array $relations = [])
    {
        $model = new $this->model();
        $modelInstance = $model->saveFromRequest($requestData, $relations);
        return $modelInstance;
    }

	/**
	 * Salva e redireciona em sequencia
	 * @param array $requestData
	 * @param array $relations
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function salvarRedirecionar(array $requestData, array $relations = [])
	{
		try {
            # Callback antes de salvar
            $requestData = $this->beforeSave($requestData);

            # Salva a model
            $modelObj = $this->salvar($requestData, $relations);

            # Callback apos o save
            $this->afterSave($modelObj);

            # Envia o id da model para função de redirecionar
            $redirect = $this->redirecionar($modelObj->getKey());

            # Adiciona mensagem flash com sucesso
            # TODO Session::flash('success', 'Sua mensagem');

            return $redirect;

		} catch (\Exception $e){

		    # Enviado mensagem do erro caso ambiente for local
			if (App::environment('local')) {
                return new JsonResponse(["errors" => "Ops!" . $e->getMessage()], 500);
			}
			# Retorna json com erro
			return new JsonResponse(["errors" => "Ops! Ocorreu um erro ao salvar."], 500);
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

    protected function beforeSave($requestData)
    {
	    return $requestData;
    }

    protected function afterSave($modelObj)
    {
        # Do stuff here
    }

    /**
     * Método desativado
     */
    public function show()
    {
        abort(404);
    }


}
