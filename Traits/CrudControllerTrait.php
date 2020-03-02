<?php

namespace Impactaweb\Crud\Traits;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Impactaweb\Crud\Form\FormUrls;

trait CrudControllerTrait
{

    public function store(Request $request)
    {
        $this->applyValidation($request);
        return $this->salvarRedirecionar($request->all(), $this->belongsToManyRelations);
    }

    public function applyValidation(Request $request)
    {
        if ($this->validation) {
            $validation = new $this->validation();
            $request->validate($validation->rules());
        }
    }

    /**
     * Salva e redireciona em sequencia
     * @param array $requestData
     * @param array $relations
     * @return JsonResponse
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
            $newId = $modelObj->getKey();
            # Envia o id da model para função de redirecionar
            return $this->redirecionar($newId);

        } catch (\Throwable $e) {
            # Return erros
            if (method_exists($e, 'errors') && is_a($e, ValidationException::class)) {
                return $this->returnErrors(
                    ['message' => 'Ops! Por favor corrija os campos do formulário',
                        'errors' => $e->errors()], 422);
            }

            $error = ["errors" => "Ops! Ocorreu um erro ao salvar!"];
            if (App::environment('local')) {
                $error = ["errors" => "Ops!" . (string)$e->getMessage()];
            }
            return $this->returnErrors($error);
        }
    }

    protected function beforeSave($requestData)
    {
        return $requestData;
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
        return $model->saveFromRequest($requestData, $relations);
    }

    protected function afterSave($modelObj)
    {
        # Do stuff here
    }

    /**
     * Faz a seleção para qual URL o formulário fará o redirecionamento
     * do usuário, com base no botão clicado para submeter
     * @paramm $idNovo ID do nova Model
     * @param null $idNovo
     * @return JsonResponse
     * @throws Exception
     */
    public function redirecionar($idNovo = null)
    {
        return response()->json(['url' => FormUrls::redir('auto', $idNovo)]);
    }

    /**
     * Return Json errors with status
     * @param array $errors
     * @param int $status
     * @return JsonResponse
     */
    public function returnErrors(array $errors, int $status = 500)
    {
        # Retorna json com erro
        return new JsonResponse($errors, $status);
    }

    public function update(Request $request)
    {
        $this->applyValidation($request);
        return $this->salvarRedirecionar($request->all(), $this->belongsToManyRelations);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        if ($request->query('modelId') && $request->query('filetodelete') && $request->query('fieldFile')) {
            return $this->deleteFileFromRequest($request);
        }
    }

    /**
     * Delete selected file from request
     * Used for delete files with POST in forms
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteFileFromRequest(Request $request)
    {
        $deleted = $this->destroyFile($request->query('filetodelete'));
        $this->model::deleteFile($request->query('modelId'), $request->query('fieldFile'));
        return response()->json(['ok' => $deleted]);
    }

    public function destroy(Request $request)
    {
       if ($request->query('modelId') && $request->query('filetodelete') && $request->query('fieldFile')) {
           $deleted = $this->destroyFile($request->query('filetodelete'));
           $this->model::deleteFile($request->query('modelId'), $request->query('fieldFile'));
           return response()->json(['ok' => $deleted]);
       }
    }

    /**
     * Método desativado
     */
    public function show()
    {
        abort(404);
    }


    /**
     * Raise single field error
     * @param string $field
     * @param $message
     * @throws ValidationException
     */
    public function raiseFieldError(string $field, $message)
    {
        throw ValidationException::withMessages([
            $field => [$message,],
        ]);
    }


    /**
     * Raise form errors
     * Message array example:
     * [
     * 'file' => ['Validation Message #1'],
     * 'number' => ['Validation Message #2', 'Validation Message #2b'],
     * ];
     * @param array $messages
     */
    public function raiseFormErrors($messages)
    {
        throw ValidationException::withMessages($messages);
    }

}
