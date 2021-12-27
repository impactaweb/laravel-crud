<?php

namespace Impactaweb\Crud\Listing;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class DataSource
{

    public $dataSource;
    public $columns;
    public $columnsSelect;
    public $tablesJoin;
    public $allowedOrderbyColumns;
    public $orderbyList;

    /**
     * Inicia o objeto. O $dataSource deve ser obrigatoriamente
     * do tipo Model ou Builder
     */
    public function __construct($dataSource)
    {
        if (!$dataSource instanceof Model && !$dataSource instanceof Builder) {
            throw new Exception("Invalid source type");
        }

        # Caso o datasource for model, converte ele para Builder
        if ($dataSource instanceof Model) {
            $this->model = $dataSource;
            $this->table = $this->model->getTable();
            $this->dataSource = $dataSource::query();
            return;
        }

        # Caso o datasource for Builder, pega a model apartir dele
        $this->model = $dataSource->getModel();
        $this->table = $this->model->getTable();
        $this->dataSource = $dataSource;
    }

    /**
     * Retorna o Colletion da query
     */
    public function getData(array $columns, ?array $orderby = [], int $perPagePagination = 20, ?array $queryString = [])
    {
        $this->columns = $columns;
        $this->buildJoins();
        $this->buildWhere($queryString);
        $this->buildSelect();
        $this->orderbyList = [$orderby];
        $this->buildOrderby();
        return $this->dataSource->paginate($perPagePagination);
    }

    /**
     * Constroi os joins manualmente, visto que a classe Model realiza Eager Loading
     * Caso o sourceData seja um objeto QueryBuilder
     */
    public function buildJoins(): void
    {
        $source = $this->dataSource;
        $joinList = [];
        $joinTables = [];
        $allowedOrderbyColumns = [];

        // For each column, try to detect it's relations join
        foreach ($this->columns as $column) {

            $orderbyAllowed = true;
            if (strpos($column, ".") === false) {
                $allowedOrderbyColumns[] = $column;
                $this->columnsSelect[$column] = $this->table . "." . $column;
                continue;
            }

            $columnParts = explode(".", $column);
            $join = $source;
            $fullTableName = "";

            // Note: the relation can go deep (model->another->another->...)
            for ($i = 0; $i < count($columnParts) - 1; $i++) {
                $table = $columnParts[$i];

                if ($i > 0 && $join instanceof BelongsTo) {
                    $join = $join->getRelated();
                }

                if ($join instanceof Builder) {
                    $join = $join->getModel();
                }

                if (!method_exists($join, $table)) {
                    $orderbyAllowed = false;
                    continue 2;
                }

                $join = $join->$table();
                if (!$join instanceof BelongsTo) {
                    $orderbyAllowed = false;
                    continue 2;
                }

                $tableName = $join->getRelated()->getTable();
                $qualifiedFK = $join->getQualifiedForeignKeyName();
                $qualifiedPK = $join->getQualifiedOwnerKeyName();

                $fullTableName .= ($fullTableName ? "." : "") . $table;
                if (in_array($fullTableName, $joinTables)) {
                    continue;
                }

                if (method_exists($join->getRelated(), 'joinScopes')) {
                    $scopes = $join->getRelated()->joinScopes();
                    if (is_array($scopes) && count($scopes)) {
                        $qualifiedPK = [$qualifiedPK];
                        $qualifiedFK = [$qualifiedFK];
                        foreach ($scopes as $scope) {
                            $qualifiedPK[] = $scope[0];
                            $qualifiedFK[] = $scope[1];
                        }
                    }

                }
                $joinList[] = [$tableName, $qualifiedPK, '=', $qualifiedFK];
                $joinTables[] = $fullTableName;
            }

            if ($orderbyAllowed) {
                $allowedOrderbyColumns[] = $column;
                $qualifiedColumn = ($join ? $join->getRelated()->getTable() : $this->table) . "." . end($columnParts);
                $this->columnsSelect[$column] = $qualifiedColumn;
            }

        }

        // Performing joins to data source
        foreach ($joinList as $join) {
            if (is_array($join[1])) {
                $source = $source->leftJoin($join[0], function ($joinBuilder) use ($join) {
                    foreach ($join[1] as $i => $index1) {
                        $joinBuilder = $joinBuilder->on($index1, $join[2], is_numeric($join[3][$i]) ? DB::raw($join[3][$i]) : $join[3][$i]);
                    }
                    return $joinBuilder;
                });
            } else {
                $source = $source->leftJoin($join[0], $join[1], $join[2], $join[3]);
            }
        }

        // Returning Model object
        $this->allowedOrderbyColumns = $allowedOrderbyColumns;
        $this->dataSource = $source;
    }

    /**
     * Cria os campos para Select
     */
    public function buildSelect()
    {
        // Se a chave primária não estiver no objeto, inserí-la
        $primaryKey = $this->model->getKeyName();

        if (!array_key_exists($primaryKey, $this->columnsSelect)) {
            $this->columnsSelect[$primaryKey] = $this->table . '.' . $primaryKey;
        }

        $currentColumns = !empty($this->dataSource->getQuery()->columns) ? array_map(function ($item) {
            return $item->getValue();
        }, $this->dataSource->getQuery()->columns) : [];

        $currentColumns = $currentColumns
            ? array_merge($currentColumns, array_values($this->columnsSelect))
            : array_values($this->columnsSelect);

        $this->dataSource = $this->dataSource->selectRaw(implode(",", $currentColumns));
    }

    /**
     * Constroi as where com base na request
     */
    public function buildWhere($queryString)
    {
        $whereRaw = "";
        $whereRawValues = [];

        // Basic search
        if (isset($queryString['q'])) {
            $searchTextParts = $this->getSearchTextParts(trim($queryString['q']));
            foreach ($searchTextParts as $searchText) {
                $whereRaw .= " AND (";
                $i = 0;
                foreach ($this->columnsSelect as $column) {
                    if (strpos($column, "*") !== false) {
                        continue;
                    }
                    $i++;
                    # remove qualquer apelido que a coluna tiver, para não dar erro na query:
                    $col = explode(' ', $column);
                    $column = $col[0];
                    $whereRaw .= ($i == 1 ? '' : ' OR ') . $column . ' like ? ';
                    $whereRawValues[] = is_numeric($searchText) ? $searchText : '%' . $searchText . '%';
                }
                $whereRaw .= ")";
            }
        }

        // Advanced search
        foreach ($this->columns as $column) {
            $columnQueryString = str_replace('.', '_', $column);
            if (!isset($queryString[$columnQueryString]) || !isset($this->columnsSelect[$column])) {
                continue;
            }

            $searchText = trim($queryString[$columnQueryString]);
            if ($searchText === '') {
                continue;
            }

            $operator = (isset($queryString['op']) && isset($queryString['op'][$column]) ? $queryString['op'][$column] : "like");
            switch ($operator) {
                case '=':
                case '!=':
                case '<':
                case '<=':
                case '>':
                case '>=':
                    $whereRaw .= " AND " . $this->columnsSelect[$column] . " $operator ? ";
                    $whereRawValues[] = $searchText;
                    break;

                case 'in':
                    $operator = 'in';
                    $values = explode(',', $searchText);
                    $inFields = substr(str_repeat('?,', count($values)), 0, -1);
                    $whereRaw .= " AND " . $this->columnsSelect[$column] . " IN ( $inFields )";
                    $whereRawValues = array_merge($whereRawValues, $values);
                    break;

                case 'like':
                case 'not like':
                    $searchTextParts = $this->getSearchTextParts($searchText);
                    foreach ($searchTextParts as $part) {
                        $whereRaw .= " AND " . $this->columnsSelect[$column] . " $operator ? ";
                        $whereRawValues[] = '%' . $part . '%';
                    }
                    break;

                default:
                    break;
            }
        }

        $whereRaw = ltrim($whereRaw, ' AND ');
        if (empty($whereRaw)) {
            return null;
        }

        $this->dataSource = $this->dataSource->whereRaw($whereRaw, $whereRawValues);
    }


    /**
     * Constroi o order by conforme a query
     */
    public function buildOrderby()
    {
        $orderByColumns = [];
        foreach ($this->orderbyList as $orderby) {
            $column = $this->columnsSelect[$orderby[0] ?? 0] ?? null;
            $direction = (strtolower($orderby[1]) == 'desc' ? 'desc' : 'asc');

            // Verifica se a coluna está disponível para ordenação
            if (!$column) {
                continue;
            }

            // Verifica se a coluna pode ser usada para ordenação
            if (in_array($orderby[0], $this->allowedOrderbyColumns)) {
                $this->dataSource = $this->dataSource->orderBy($column, $direction);
            }

            $orderByColumns[] = $column;
        }

        // Insiro aqui a chave primária para não variar a ordem
        $primaryKey = $this->model->getQualifiedKeyName();
        if (!in_array($primaryKey, $orderByColumns)) {
            $this->dataSource = $this->dataSource->orderBy($primaryKey, 'desc');
        }
    }

    public function getAllowedOrderbyColumns(): array
    {
        return $this->allowedOrderbyColumns;
    }

    public static function getAdvancedSearchOperators()
    {
        return [
            'like' => 'is like',
            'not like' => 'not like',
            '=' => 'equal',
            '!=' => 'different',
            '<' => 'less than',
            '<=' => 'less or equal than',
            '>' => 'greater than',
            '>=' => 'greater or equal than',
            'in' => 'in',
        ];
    }

    // Remove caracteres desnecessários, espaços duplos, etc,
    // e retorna um vetor com todas as partes da string para busca
    public function getSearchTextParts(string $text): array
    {
        $text = preg_replace('/\s+/', ' ', trim($text));
        preg_match_all('~(?:[^\'"\s]+|\'[^\']*\'|"[^"]*")+~', $text, $parts);
        $textParts = [];
        if (!$parts[0]) {
            return "";
        }

        foreach ($parts[0] as $part) {
            $part = trim($part, ' "');
            if ($part !== '') {
                $textParts[] = $part;
            }
        }
        return $textParts;

    }

}
