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
		return Cache::has($this->primaryKey . '.columns');
	}

	/**
	 * Pega as colunas da model, com cache
	 * @return mixed
	 */
	protected function getColunas()
	{
		if ($this->cacheExists()) {
			$columns = Cache::get($this->primaryKey . '.columns');
		} else {
			$columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
			Cache::put($this->primaryKey . '.columns', $columns, 200);
		}
		return $columns;
	}


    /**
     * @param array $requestData Data form request->all()
     * @param array $relations Relations ManyToMany
     * @return CrudModelTrait
     */
    public function saveFromRequest(array $requestData, array $relations = [])
	{
		# Pega a Instancia da model a ser submitada
		# Se no array $data estiver populado a chave primária
		# ele fará a consulta da model a ser submitada, caso contrário ele irá pegar
		# esta mesma model
		if (isset($requestData[$this->getKeyName()])) {
			# Entrada no BD - Update
			$modelInstance = self::find($requestData[$this->getKeyName()]);
		} else {
			# Model limpa - Create
			$modelInstance = $this;
		}

		# Compara as chaves dos dois array e coloca os atributos dentro da model
		try {
			foreach ($modelInstance->getColunas() as $coluna) {
				if (array_key_exists($coluna, $requestData)) {
					$modelInstance->$coluna = $requestData[$coluna];
				}
			}

			# Salva a model
			$modelInstance->save();

			# Salva os relacionamentos obtidos através da função de cada model
			foreach ($relations as $function => $relation) {

				# Se o valor não estiver setado coloca a lista vazia
				if (!isset($requestData[$relation])) {
					$requestData[$relation] = [];
				}

				$relationIds = array_values((array)$requestData[$relation]);
				$modelInstance->$function()->sync($relationIds);
			}

			# Devole a chave primária
			return $modelInstance;

		} catch (\Exception $e){
			throw new Exception($e->getMessage());
		}

	}

	static public function deleteFile(int $key, string $field) {
        $entity = self::find($key);
        $entity->$field = null;
        $entity->save();
    }

}
