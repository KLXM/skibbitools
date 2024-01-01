<?php
class skOrm
{
    protected string $tableName;
    protected ?array $data;
    protected array $whereConditions = [];
    protected array $whereBindings = [];
    protected array $relations = [];


    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function load(int $id): ?array
    {
        $sql = rex_sql::factory();
        $sql->setQuery("SELECT * FROM {$this->tableName} WHERE id = :id", ['id' => $id]);

        if ($sql->getRows() == 0) {
            return null;
        }

        $this->data = $sql->getArray()[0];
        return $this->data;
    }

    public function findAll(): array
    {
        $sql = rex_sql::factory();
        $sql->setQuery("SELECT * FROM {$this->tableName}");
        return $sql->getArray();
    }

    public function findBy(array $criteria): array
    {
        $conditions = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            $conditions[] = "$key = :$key";
            $params[$key] = $value;
        }

        $conditionString = implode(' AND ', $conditions);
        $sql = rex_sql::factory();
        $sql->setQuery("SELECT * FROM {$this->tableName} WHERE {$conditionString}", $params);

        return $sql->getArray();
    }

    public function findOneBy(array $criteria): ?array
    {
        $result = $this->findBy($criteria);
        return !empty($result) ? $result[0] : null;
    }

    public function countBy(array $criteria): int
    {
        $conditions = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            $conditions[] = "$key = :$key";
            $params[$key] = $value;
        }

        $conditionString = implode(' AND ', $conditions);
        $sql = rex_sql::factory();
        $sql->setQuery("SELECT COUNT(*) AS count FROM {$this->tableName} WHERE {$conditionString}", $params);

        return (int) $sql->getValue("count");
    }

    public function insert(array $data): int
    {
        $sql = rex_sql::factory();
        $sql->setTable($this->tableName);

        foreach ($data as $key => $value) {
            $sql->setValue($key, $value);
        }

        $sql->insert();
        return (int) $sql->getLastId();
    }

    public function update(array $data, array $criteria): void
    {
        $conditions = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            $conditions[] = "$key = :$key";
            $params[$key] = $value;
        }

        $conditionString = implode(' AND ', $conditions);
        $sql = rex_sql::factory();
        $sql->setTable($this->tableName);

        foreach ($data as $key => $value) {
            $sql->setValue($key, $value);
        }

        $sql->setWhere($conditionString, $params);
        $sql->update();
    }

    public function delete(array $criteria): void
    {
        $conditions = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            $conditions[] = "$key = :$key";
            $params[$key] = $value;
        }

        $conditionString = implode(' AND ', $conditions);
        $sql = rex_sql::factory();
        $sql->setTable($this->tableName);
        $sql->setWhere($conditionString, $params);
        $sql->delete();
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

    protected function applyWhereConditions(rex_sql $sql): void
    {
        if (!empty($this->whereConditions)) {
            $conditionString = implode(' AND ', $this->whereConditions);
            $sql->setWhere($conditionString, $this->whereBindings);
        }
    }

    public function get(): array
    {
        $sql = rex_sql::factory();
        $sql->setTable($this->tableName);
        $this->applyWhereConditions($sql);
        $sql->select("*");
        $results = $sql->getArray();

        if (!empty($this->relations)) {
            foreach ($results as $key => $result) {
                foreach ($this->relations as $relationName => $relation) {
                    $relatedSql = rex_sql::factory();
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


    public function search(array $searchColumns, string $searchTerm, ?string $orderBy = null, string $orderDirection = 'ASC'): array
    {
        $searchConditions = array_map(function ($column) {
            return "$column LIKE :searchTerm";
        }, $searchColumns);

        $searchConditionString = implode(' OR ', $searchConditions);
        $sql = rex_sql::factory();
        $queryParams = ['searchTerm' => "%$searchTerm%"];

        $query = "SELECT * FROM {$this->tableName} WHERE {$searchConditionString}";

        if ($orderBy) {
            $query .= " ORDER BY $orderBy $orderDirection";
        }

        $sql->setQuery($query, $queryParams);
        return $sql->getArray();
    }

    public function searchAndReplace(array $searchColumns, string $searchTerm, string $replaceTerm): void
    {
        $searchConditions = array_map(function ($column) {
            return "$column LIKE :searchTerm";
        }, $searchColumns);

        $searchConditionString = implode(' OR ', $searchConditions);
        $sql = rex_sql::factory();
        $queryParams = [
            'searchTerm' => "%$searchTerm%",
            'replaceTerm' => $replaceTerm
        ];

        $query = "UPDATE {$this->tableName} SET ";

        foreach ($searchColumns as $column) {
            $query .= "$column = REPLACE($column, :searchTerm, :replaceTerm), ";
        }

        $query = rtrim($query, ', ');
        $query .= " WHERE {$searchConditionString}";

        $sql->setQuery($query, $queryParams);
    }
}
