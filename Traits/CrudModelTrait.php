<?php

namespace Impactaweb\Crud\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Mockery\Exception;

/**
 * Trait CrudModelTrait
 * @package Impactaweb\Crud\Traits
 */
trait CrudModelTrait
{
    /**
     * Verifica se aas colunas existem no cache
     * @return bool
     */
    private function cacheExists()
    {
        return Cache::has($this->getTable() . '.columns');
    }

    /**
     * Pega as colunas da model, com cache
     * @return mixed
     */
    protected function getColunas()
    {
        if ($this->cacheExists()) {
            $columns = Cache::get($this->getTable() . '.columns');
        } else {
            $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
            Cache::put($this->getTable() . '.columns', $columns, 200);
        }
        return $columns;
    }

    /**
     * Pega a Instancia da model a ser submitada
     * Se no array $data estiver populado a chave primária
     * ele fará a consulta da model a ser submitada, caso contrário ele irá pegar
     * esta mesma model
     * @param array $requestData
     * @return CrudModelTrait
     */
    protected function getModelInstance(array $requestData)
    {
        # Verify if primary key exist in request data
        if (isset($requestData[$this->getKeyName()])) {
            # Get model instance
            $modelInstance = self::find($requestData[$this->getKeyName()]);
        } else {
            # Starts new clean model
            $modelInstance = $this;
        }
        return $modelInstance;
    }

    /**
     * Save data from inputs
     * @param array $requestData
     * @param $modelInstance
     * @return mixed
     */
    protected function saveInputData(array $requestData, $modelInstance)
    {
        $colunas = $modelInstance->getColunas();
        if (empty($colunas)) {
            throw new \Exception("Ocorreu um erro ao obter as colunas da tabela.");
        }
        foreach ($colunas as $coluna) {
            if (array_key_exists($coluna, $requestData)) {
                $modelInstance->$coluna = $requestData[$coluna];
            }
        }
        # Salva a model
        $modelInstance->save();
        return $modelInstance;
    }

    /**
     * Sync ManyToMany relations based on $relations array
     * @param array $requestData
     * @param array $relations
     * @param $modelInstance
     * @return mixed
     */
    public function saveRelations(array $requestData, array $relations, $modelInstance)
    {
        # Salva os relacionamentos obtidos através da função de cada model
        foreach ($relations as $function => $relation) {

            # Se o valor não estiver setado coloca a lista vazia
            if (!isset($requestData[$relation])) {
                $requestData[$relation] = [];
            }

            $relationIds = array_values((array)$requestData[$relation]);
            $modelInstance->$function()->sync($relationIds);
        }
        return $modelInstance;
    }

    /**
     * @param array $requestData Data form request->all()
     * @param array $relations Relations ManyToMany
     * @return CrudModelTrait
     */
    public function saveFromRequest(array $requestData, array $relations = [])
    {
        $modelInstance = $this->getModelInstance($requestData);
        $modelInstance = $this->saveInputData($requestData, $modelInstance);
        if (!empty($relations) && ($relations != [])) {
            $modelInstance = $this->saveRelations($requestData, $relations, $modelInstance);
        }
        return $modelInstance;
    }

    /**
     * Delete selected field
     * @param int $key
     * @param string $field
     */
    static public function deleteFile(int $key, string $field) {
        $entity = self::find($key);
        $entity->$field = null;
        $entity->save();
    }

    static public function updateFlag(int $id, $field, string $flag) {
        $entity = self::find($id);
        if (!in_array($entity->{$field}, ['0', '1', null])) {
            return;
        }
        $entity->$field = ($flag == 1 ? '1' : '0');
        $entity->save();
    }

}
