<?php

namespace Impactaweb\Crud\Listing;

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class DataSource {

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
        // Validate Data Source Type
        if (!$dataSource instanceof QueryBuilder && !$dataSource instanceof Model) {
            throw new Exception("Invalid source type");
        }

        $this->dataSource = $dataSource;
    }

    /**
     * Retorna o Colletion da query
     */
    public function getData(array $columns, ?array $orderby = [], int $perPagePagination = 10, ?array $queryString = [])
    {
        $this->columns = $columns;

        $this->buildJoins();
        $this->buildWhere($queryString);
        $this->buildSelect();

        if ($orderby) {
            $this->orderbyList = [$orderby];
            $this->buildOrderby();
        }

        return $this->dataSource->paginate($perPagePagination);
    }
    
    /**
     * Constroi os joins manualmente, visto que a classe Model realiza Eager Loading
     * Caso o sourceData seja um objeto QueryBuilder
     */
    public function buildJoins(): void
    {
        $source = $this->dataSource;
        $columns = $this->columns;

        // If it's not a Model instance, exit
        if (! $source instanceof Model) {
            return;
        }

        $joinList = [];
        $joinTables = [];
        $allowedOrderbyColumns = [];

        // For each column, try to detect it's relations join
        foreach ($columns as $column) {

            $orderbyAllowed = true;
            if (strpos($column, ".") === false) {
                $allowedOrderbyColumns[] = $column;
                $this->columnsSelect[$column] = $source->getTable() . "." . $column;
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
                
                
                if (!method_exists($join, $table)) {
                    $orderbyAllowed = false;
                    continue 2;
                }

                $join = $join->$table();
                if (! $join instanceof BelongsTo) {
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

                $joinList[] = [$tableName, $qualifiedFK, '=', $qualifiedPK];
                $joinTables[] = $fullTableName;
            }
            
            if ($orderbyAllowed) {
                $allowedOrderbyColumns[] = $column;
                $this->columnsSelect[$column] = ($join ? $join->getRelated() : $source)->getTable() . "." . end($columnParts);
            }

        }

        // Performing joins to data source
        foreach ($joinList as $join) {
            $source = $source->leftJoin($join[0], $join[1], $join[2], $join[3]);
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
        $this->dataSource = $this->dataSource->select($this->columnsSelect);
    }

    /**
     * Constroi o order by conforme a query
     */
    public function buildOrderby()
    {
        foreach ($this->orderbyList as $orderby) {
            $column = $this->columnsSelect[$orderby[0] ?? 0] ?? null;
            $direction = ($orderby[1] ?? null == 'DESC' ? 'DESC' : 'ASC');

            // Verifica se a coluna está disponível para ordenação
            if (!$column) {
                continue;
            }

            // Verifica se a coluna pode ser usada para ordenação
            if (in_array($orderby[0], $this->allowedOrderbyColumns)) {
                $this->dataSource = $this->dataSource->orderBy($column, $direction);
            }
        }
        
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
            $searchText = $queryString['q'];
            $whereRaw .= " AND (";
            $i = 0;
            foreach ($this->columnsSelect as $column) {
                $i++;
                $whereRaw .= ($i == 1 ? '' : ' OR ') . $column . ' like ? ';
                $whereRawValues[] = is_numeric($searchText) ? $searchText : '%' . $searchText . '%';
            }
            $whereRaw .= ")";
        }

        // Advanced search
        foreach ($this->columns as $column) {
            $columnQueryString = str_replace('.', '_', $column);
            if (!isset($queryString[$columnQueryString]) || !isset($this->columnsSelect[$column])) {
                continue;
            }
            
            $searchText = $queryString[$columnQueryString];
            if (empty($searchText)) {
                continue;
            }

            $prefix = $suffix = $operator = "";
            $mountWhereRaw = true;

            $op = (isset($queryString['op']) && isset($queryString['op'][$column]) ? $queryString['op'][$column] : "like");
            switch  ($op) {
                case '=':
                case '!=':
                case '<':
                case '<=':
                case '>':
                case '>=':
                    $operator = $op;
                    break;
                
                case 'not like':
                    $prefix = $suffix = '%';
                    $operator = 'not like';
                    break;
                
                case 'in':
                    $prefix = '';
                    $suffix = '';
                    $operator = 'in';
                    $values = explode(',', $searchText);
                    $inFields = substr(str_repeat('?,', count($values)), 0, -1);
                    $whereRaw .= " AND " . $this->columnsSelect[$column] . " IN ( $inFields )";
                    $whereRawValues = array_merge($whereRawValues, $values);
                    $mountWhereRaw = false;
                    break;
                
                default:
                    $prefix = $suffix = '%';
                    $operator = 'like';
                    break;
            }
            if ($mountWhereRaw) {
                $whereRaw .= " AND " . $this->columnsSelect[$column] . " $operator ? ";
                $whereRawValues[] = $prefix . $searchText . $suffix;
            }
        }

        $whereRaw = ltrim($whereRaw, ' AND ');
        if (empty($whereRaw)) {
            return null;
        }

        $this->dataSource = $this->dataSource->whereRaw($whereRaw, $whereRawValues);
    }
}