<?php

class skOrm
{
    protected string $tableName;
    protected ?array $data;
    protected array $whereConditions = [];
    protected array $whereBindings = [];
    protected array $relations = [];
    protected array $orderByConditions = [];
    protected array $selectedColumns = [];
    protected array $joins = [];
    protected array $concatenations = [];
    protected int $limit = 0;
    protected int $offset = 0;
    protected int $dbIndex = 1;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function setDbIndex(int $dbIndex): self
    {
        $this->dbIndex = $dbIndex;
        return $this;
    }

    protected function createSql(): rex_sql
    {
        return rex_sql::factory($this->dbIndex);
    }

    private function buildSelectQuery(string $conditionString = '', array $params = []): string
    {

        $selectColumns = empty($this->selectedColumns) ? '*' : implode(', ', $this->selectedColumns);


        if (!empty($this->concatenations)) {
            $selectColumns .= ', ' . implode(', ', $this->concatenations);
        }
        
        $query = "SELECT $selectColumns FROM {$this->tableName}";


        foreach ($this->joins as $join) {
            $query .= " $join";
        }

        if (!empty($this->whereConditions)) {
            $whereString = implode(' AND ', $this->whereConditions);
            $query .= " WHERE $whereString";
        }

        if ($conditionString) {
            $query .= $this->whereConditions ? " AND $conditionString" : " WHERE $conditionString";
        }


        if (!empty($this->orderByConditions)) {
            $orderByString = implode(', ', $this->orderByConditions);
            $query .= " ORDER BY $orderByString";
        }

        if ($this->limit > 0) {
            $query .= " LIMIT {$this->offset}, {$this->limit}";
        }

        return $query;
    }

    private function buildConditions(array $criteria): array
    {
        $conditions = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            $conditions[] = "$key = :$key";
            $params[$key] = $value;
        }

        return ['conditionString' => implode(' AND ', $conditions), 'params' => $params];
    }

    public function load(int $id): ?array
    {
        $sql = $this->createSql();
        $sql->setQuery($this->buildSelectQuery("id = :id", ['id' => $id]));

        if ($sql->getRows() == 0) {
            return null;
        }

        $this->data = $sql->getArray()[0];
        return $this->data;
    }

    public function findAll(): array
    {
        $sql = $this->createSql();
        $sql->setQuery($this->buildSelectQuery());
        return $sql->getArray();
    }

    public function getOne(): ?array
    {
        $result = $this->get();
        return !empty($result) ? $result[0] : null;
    }

    public function searchAndReplace(array $columns, string $search, string $replace, bool $testOnly = false): array
    {
        $affectedRows = [];
        $rows = $this->searchByString($columns, $search);

        if (!$testOnly) {
            foreach ($rows as $row) {
                $updateData = [];
                foreach ($columns as $column) {
                    $updateData[$column] = str_replace($search, $replace, $row[$column]);
                }
                $this->update($updateData, ['id' => $row['id']]);
                $affectedRows[] = $row['id'];
            }
        } else {
            foreach ($rows as $row) {
                $affectedRows[] = $row['id'];
            }
        }

        return $affectedRows;
    }

    public function searchByString(array $columns, string $searchString): array
    {
        $conditionString = '';
        foreach ($columns as $column) {
            $conditionString .= (empty($conditionString) ? '' : ' OR ') . "$column LIKE :searchString";
        }

        $sql = $this->createSql();
        $sql->setQuery($this->buildSelectQuery($conditionString, ['searchString' => '%' . $searchString . '%']));
        return $sql->getArray();
    }

    public function count(): int
    {
        $results = $this->get();
        return count($results);
    }

    public function insert(array $data): int
    {
        $sql = $this->createSql();
        $sql->setTable($this->tableName);

        foreach ($data as $key => $value) {
            $sql->setValue($key, $value);
        }

        $sql->insert();
        return (int) $sql->getLastId();
    }

    public function update(array $data): void
    {
        $sql = $this->createSql();
        $sql->setTable($this->tableName);

        foreach ($data as $key => $value) {
            $sql->setValue($key, $value);
        }

        $this->applyWhereConditions($sql); // Anwenden der Where-Bedingungen
        $sql->update();
        $this->resetQuery();
    }


    public function delete(): void
    {
        $sql = $this->createSql();
        $sql->setTable($this->tableName);
        $this->applyWhereConditions($sql);
        $sql->delete();
        $this->resetQuery();
    }



    public function where(string $column, string $operator, $value): self
    {
        $this->whereConditions[] = "{$column} {$operator} :{$column}";
        $this->whereBindings[$column] = $value;
        return $this;
    }

    public function whereRaw(string $rawCondition, array $bindings = []): self
    {
        $this->whereConditions[] = $rawCondition;
        $this->whereBindings = array_merge($this->whereBindings, $bindings);
        return $this;
    }

    public function whereInList(string $column, array $values): self
    {
        $regexp = implode('|', array_map('intval', $values));
        $this->whereRaw("CONCAT(',', $column, ',') REGEXP ',(" . $regexp . "),'");

        return $this;
    }

     public function limit(int $limit, int $offset = 0): self {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

      public function paginate(int $page, int $perPage): array {
        $offset = ($page - 1) * $perPage;
        $this->limit($perPage, $offset);

        $totalQuery = $this->buildSelectQuery('COUNT(*)');
        $total = rex_sql::factory()->setQuery($totalQuery)->getArray()[0]['COUNT(*)'];

        // Holt die paginierten Daten
        $data = $this->get();

        return [
            'data' => $data,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
            'lastPage' => ceil($total / $perPage)
        ];
    }

    protected function applyWhereConditions(rex_sql $sql): void
    {
        if (!empty($this->whereConditions)) {
            $conditionString = implode(' AND ', $this->whereConditions);
            $sql->setWhere($conditionString, $this->whereBindings);
        }
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderByConditions[] = "$column $direction";
        return $this;
    }

    public function select(array $columns): self
    {
        $this->selectedColumns = $columns;
        return $this;
    }

    public function concatFields(array $elements, string $as): self {
        $concatParts = array_map(function($element) {
            return strpos($element, '{') === 0 ? $element : "`$element`";
        }, $elements);

        $concatenated = "CONCAT(" . implode(", ", $concatParts) . ") AS `$as`";
        $this->concatenations[] = $concatenated;
        return $this;
    }

    
    public function innerJoin(string $table, string $on): self
    {
        $this->joins[] = "INNER JOIN $table ON $on";
        return $this;
    }


    public function leftJoin(string $table, string $on): self
    {
        $this->joins[] = "LEFT JOIN $table ON $on";
        return $this;
    }


    public function rightJoin(string $table, string $on): self
    {
        $this->joins[] = "RIGHT JOIN $table ON $on";
        return $this;
    }


    public function get(): array
    {
        $sql = $this->createSql();
        $sql->setQuery($this->buildSelectQuery());
        $this->applyWhereConditions($sql);
        $results = $sql->getArray();

        if (!empty($this->relations)) {
            foreach ($results as $key => $result) {
                foreach ($this->relations as $relationName => $relation) {
                    $relatedSql = $this->createSql();
                    $relatedSql->setTable($relation['foreignTable']);
                    $relatedSql->setWhere($relation['foreignKey'] . ' = :value', ['value' => $result[$relation['localKey']]]);
                    $relatedSql->select("*");
                    $results[$key][$relationName] = $relatedSql->getArray();
                }
            }
        }

        $this->resetQuery();
        return $results;
    }

    public function resetQuery(): void
    {
        $this->whereConditions = [];
        $this->whereBindings = [];
        $this->orderByConditions = [];
        $this->selectedColumns = [];
    }

    public function with(string $relationName, string $foreignTable, string $foreignKey, string $localKey = 'id'): self
    {
        $this->relations[$relationName] = [
            'foreignTable' => $foreignTable,
            'foreignKey' => $foreignKey,
            'localKey' => $localKey
        ];
        return $this;
    }

    public function toggleSortByUrl(): void 
    {
        // Liest Sortierparameter aus der URL
        $sortColumn = rex_request('sort', 'string', $this->defaultSortColumn);
        $sortType = rex_request('sorttype', 'string', $this->defaultSortDirection);

        // Setzt die Sortierung, wenn der Spaltenname gültig ist
        if (in_array($sortColumn, $this->selectedColumns)) {
            $this->orderBy($sortColumn, strtoupper($sortType) === 'DESC' ? 'DESC' : 'ASC');
        } else {
            $this->defaultOrderBy();
        }
    }
    
    public function toRexList(array $columnHeadings = []): rex_list
    {
        $list = rex_list::factory($this->buildSelectQuery());

        // Setzt Standardüberschriften, falls keine angegeben sind
        if (empty($columnHeadings)) {
            $columnHeadings = array_combine($this->selectedColumns, $this->selectedColumns);
        }

        foreach ($columnHeadings as $columnName => $heading) {
            $list->setColumnLabel($columnName, $heading);
            $list->setColumnSortable($columnName);
        }
        return $list;
    }

}
