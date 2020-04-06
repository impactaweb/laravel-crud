<?php

namespace Impactaweb\Crud\Traits;

use Exception;
use Impactaweb\Crud\Helpers\Msg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Impactaweb\Crud\Form\FormUrls;

trait CrudControllerTrait
{
    use Upload;

    public function store(Request $request)
    {
        $belongsToManyRelations = isset($this->belongsToManyRelations) ? $this->belongsToManyRelations : [];
        $this->applyValidation($request);
        return $this->salvarRedirecionar($request->all(), $belongsToManyRelations);
    }

    /**
     * Aplica o validation rules
     * @param Request $request
     */
    public function applyValidation(Request $request)
    {
        if (isset($this->validation)) {
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

            # Upload all files
            $requestData = $this->uploadFiles($requestData);

            # Salva a model
            $modelObj = $this->salvar($requestData, $relations);

            # Callback apos o save
            $this->afterSave($modelObj);

            # Exibe mensagens
            $this->showMessages($modelObj);

            # Envia o id da model para função de redirecionar
            return $this->redirecionar($modelObj->getKey());

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

    /**
     * Callback before save CRUD
     * @param $requestData
     * @return mixed
     */
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

    /**
     * Callback after save Crud
     * @param $modelObj
     */
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

    /**
     * Update
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $belongsToManyRelations = isset($this->belongsToManyRelations) ? $this->belongsToManyRelations : [];
        $this->applyValidation($request);
        return $this->salvarRedirecionar($request->all(), $belongsToManyRelations);
    }

    public function destroyfile(Request $request)
    {
        # Delete files from request
        if ($request->query('model_id') && $request->query('file_delete')) {
            try {
                $this->model::deleteFile($request->query('model_id'), $request->query('file_delete'));
                return response()->json(['success' => true]);
            } catch (Exception $e) {
                return response()->json(['success' => false], 422);
            }
        }
        return response()->json(['success' => false], 422);
    }
    public function destroy(Request $request, $id)
    {
        // Multiplos itens para excluir (da listagem)
        if ($request->has('multiple')) {

            $items = explode(',', $request->get('multiple'));

            foreach ($items as $item) {
                if (!is_numeric($item)) {
                    continue;
                }
                
                try {
                    $this->model::find($item)->delete();
                } catch (Exception $e) {
                    abort('Erro ao excluir item: '.$item, 422);
                }   
            }

            return $request->has('redir') ? redirect($request->get('redir')) : back();
        }

        # Delete Model (antigo)
        if ($request->post('item')) {
            $success = [];
            $errors  = [];

            foreach ($request->post('item') as $id) {
                try {
                    $this->model::find($id)->delete();
                    $sucess[] = $id;
                } catch (Exception $e) {
                    $errors[] = $id;
                }
            }
            # Return Json data
            if (!empty($error)) {
                # Error
                return response()->json(['success' => $sucess, 'errors' => $errors], 422);
            } else {
                # Success
                return response()->json(['success' => $sucess, 'errors' => $errors], 200);
            }
        }

        // Single delete
        if (is_numeric($id)) {
            try {
                $this->model::find($id)->delete();
            } catch (Exception $e) {
                abort('Erro ao excluir item: '.$id, 422);
            }   
            return $request->has('redir') ? redirect($request->get('redir')) : back();
        }

        abort('Método não encontrado.', 422);
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
     * Show alert messages
     * @param $modelObj
     */
    public function showMessages($modelObj)
    {
        if (request()->route()->getActionMethod() == 'store') {
            Msg::success(__("form::form.success_message"));
        } else {
            Msg::info(__('form::form.info_message'));
        }
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

    /**
     * Save all uploaded files in requestData
     * @param $requestData
     * @return array
     */
    protected function uploadFiles($requestData): array
    {
        foreach ($this->getSavedFiles() as $file => $path)
        {
            $requestData[$file] = $path;
        }
        return $requestData;
    }

}
