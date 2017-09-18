<?php

// -------------------------------------------
//     Basic database query builder of LiF
// -------------------------------------------

namespace Lif\Core\Storage;

class LDO extends \PDO
{
    use \Lif\Core\Traits\MethodNotExists;

    protected $conn     = null;
    protected $crud     = null;
    protected $sql      = null;    // Used for prepare statement
    protected $_sql     = null;    // Used for debug
    protected $lastSql  = null;
    protected $_lastSql = null;
    protected $status   = null;
    protected $result   = [];

    protected $table   = null;
    protected $select  = null;
    protected $where   = null;
    protected $sort    = null;
    protected $group   = null;
    protected $limit   = null;
    protected $updates = null;
    protected $insertKeys = null;
    protected $insertVals = null;
    protected $statement  = null;
    protected $bindValues = [];
    protected $transRes   = true;    // Transaction result

    public function transRes($value='')
    {
        return $this->transRes;
    }

    public function trans(\Closure $trans)
    {
        $this->start();

        $transRes = $trans($this);

        if (true === ($status = $this->transRes)) {
            $this->commit();
        } else {
            $this->rollback();
        }

        $this->transRes = true;

        return is_null($transRes) ? $status : $transRes;
    }

    public function start(): void
    {
        if (! $this->inTransaction()) {
            $this->beginTransaction();
        }
    }

    public function commit(): void
    {
        if ($this->inTransaction()) {
            parent::commit();
        }
    }

    public function rollback(): void
    {
        if ($this->inTransaction()) {
            parent::rollback();
        }
    }

    public function __conn($conn): LDO
    {
        if (! $this->conn) {
            $this->conn = $conn;
        }

        return $this;
    }
    
    public function conn()
    {
        return $this->conn;
    }

    public function setAttribute($attr, $val): LDO
    {
        parent::setAttribute($attr, $val);

        return $this;
    }

    protected function crud($value = null): LDO
    {
        $this->crud = strtoupper($value ?? $this->crud);

        return $this;
    }

    public function __get($name)
    {
        return $this->$name();
    }

    public function __call($name, $args): LDO
    {
        if ('where' === mb_substr($name, 0, 5)) {
            if (! $args) {
                excp('Missing conditions values.');
            }
            $rest = mb_substr($name, 5);
            if ($rest && ($fields = $this->dynamicWhere($rest, $args))) {
                $this->where($fields);
            }
        } elseif ('or' === mb_substr($name, 0, 2)) {
            $rest = mb_substr($name, 2);
            if ($rest && ($fields = $this->dynamicWhere($rest, $args))) {
                $this->or($fields);
            }
        } else {
            excp(
                'Method `'.$name.'()` not exists in '.(static::class)
            );
        }

        return $this;
    }

    protected function dynamicWhere(string $conds, array $args)
    {
        if ($conds) {
            $conds = preg_replace_callback('/[A-Z]/u', function ($match) {
                if (isset($match[0]) && is_string($match[0])) {
                    return '.'.strtolower($match[0]);
                }
            }, $conds);

            $fields   = array_values(array_filter(explode('.', $conds)));
            $fieldCnt = count($fields);
            $argCnt   = count($args);
            
            if ($argCnt > $fieldCnt) {
                excp('Condition count can not greater than fields count.');
            }

            $lastArg = $args[--$argCnt];
            for ($i = $argCnt+1; $i < $fieldCnt; ++$i) {
                $args[] = $lastArg;
            }

            array_walk($fields,
                function (&$item, $key, $args) {
                    $item = [
                        $item,
                        $args[$key]
                    ];
            }, $args);

            return $fields;
        }

        return false;
    }

    public function table($name, $alias = null): LDO
    {
        if (!$name || !is_string($name)) {
            excp('Missing or illegal table name.');
        } elseif ($alias && !is_string($alias)) {
            excp('Illegal table alias.');
        }

        $table = $alias ? $name.' AS '.$alias : $name;

        $this->table = $table;

        return $this;
    }

    protected function verifyWhereCondFields(array $conds)
    {
        switch (count($conds)) {
            // Only one condition, and use default specific operator `=`
            case 2: {
                if (!($condCol = $conds[0]) || !is_string($condCol)) {
                    excp('Expecting first field of condition a string.');
                } elseif (! ($condVal = $conds[1])
                    || (
                        !is_string($condVal)
                        && !is_numeric($condVal)
                        && !is_array($condVal)
                    )
                ) {
                    excp(
                        'Expecting second field of condition a string or array.'
                    );
                }
                $condOp = '=';
            } break;

            // Only one condition, and provide specific operator
            case 3: {
                if (!($condCol = $conds[0]) || !is_string($condCol)) {
                    excp('Expecting first field of condition a string.');
                } elseif (!($condOp = $conds[1])
                    || !is_string($condOp)
                ) {
                    excp('Expecting second field of condition a string.');
                } elseif (! ($condVal = $conds[2])
                    || (
                        !is_string($condVal)
                        && !is_numeric($condVal)
                        && !is_array($condVal)
                    )
                ) {
                    excp('Expecting third field of condition.');
                }
            } break;
            
            default: {
                excp('Illgeal where conditions.');
            } break;
        }

        if (is_array($condVal)) {
            $condOpWithVal = ' in (?)';
            $this->bindValues[] = implode(',', $condVal);
        } else {
            $condOpWithVal = ' '.$condOp.' ?';
            $this->bindValues[] = $condVal;
        }

        return '(`'.$condCol.'`'.$condOpWithVal.')';
    }

    public function or(...$conds): LDO
    {
        $where = $this->__where($conds);

        if ($where) {
            $this->where = $this->where
            ? $this->where.' OR ('.$where.')'
            : $where;
        }

        return $this;
    }

    protected function __where(array $conds): string
    {
        $where = '';
        if ($conds) {
            switch (count($conds)) {
                // Conditions formatted with array
                case 1: {
                    if (!$conds[0]
                        || (! is_string($conds[0])
                            && !is_array($conds[0])
                            && !($conds[0] instanceof \Closure)
                        )
                    ) {
                        excp('Illgeal conditions, expect an un-empty array or closure.');
                    }

                    if (is_string($conds[0])) {
                        $where = $conds[0];
                    } elseif (is_array($conds[0])) {
                        if (! is_array($conds[0][array_keys($conds[0])[0]])) {
                            $where = $this->verifyWhereCondFields($conds[0]);
                        } else {
                            foreach ($conds[0] as $key => $cond) {
                                $where .= $this->verifyWhereCondFields($cond);
                                if (next($conds[0])) {
                                    $where .= ' AND ';
                                }
                            }
                        }
                    } elseif (is_callable($conds[0])) {
                        $table = clone $this;
                        $table->where = null;
                        $table->bindValues = [];
                        $conds[0]($table);
                        $where = $table->where ? '('.$table->where.')' : null;

                        if ($table->bindValues) {
                            $this->bindValues = array_merge(
                                $this->bindValues,
                                $table->bindValues
                            );
                        }
                    }
                } break;
                
                case 2:
                case 3: {
                    $where = $this->verifyWhereCondFields($conds);
                } break;
                
                default: {
                    excp('Illgeal where conditions');
                } break;
            }
        }

        return $where ? '('.$where.')' : '';
    }

    public function where(...$conds): LDO
    {
        $where = $this->__where($conds);

        if ($where) {
            $this->where = $this->where
            ? $this->where.' AND ('.$where.')'
            : $where;
        }

        return $this;
    }

    public function lastSql(): string
    {
        if ($this->lastSql) {
            return $this->lastSql;
        }

        return $this->lastSql = $this->sql();
    }

    public function __lastSql(): string
    {
        if ($this->_lastSql) {
            return $this->_lastSql;
        }

        return $this->_lastSql = $this->__sql();
    }

    public function __sql(): string
    {
        if ($this->_sql) {
            return $this->_sql;
        }

        $sql = $this->sql();

        if (! $this->bindValues) {
            return $sql;
        }

        $sql = preg_replace_callback('/\?/u', function ($matches) {
            static $idx = 0;
            return $this->bindValues[$idx++] ?? null;
        }, $sql);

        unset($idx);

        $this->bindValues = [];

        return $this->_sql = $this->_lastSql = $sql;
    }

    public function sql(): string
    {
        if ($this->sql) {
            return $this->sql;
        } else {
            if (!$this->table && !$this->select) {
                excp('No base table or select commands specified.');
            } elseif (! $this->crud) {
                $this->crud = 'READ';
            }

            if (! in_array($this->crud, [
                    'CREATE',
                    'INSERT',
                    'READ',
                    'SELECT',
                    'UPDATE',
                    'DELETE'
                ])
            ) {
                excp('Missing or wrong SQL manipulation.');
            }

            $sqlBuilder = 'sql'.ucfirst(strtolower($this->crud));

            return $this->sql = $this->lastSql = $this->$sqlBuilder();
        }
    }

    protected function sqlInsert(): string
    {
        $sql  = "INSERT INTO {$this->table} ";
        $sql .= $this->insertKeys ? "({$this->insertKeys}) " : '';
        $sql .= "VALUES {$this->insertVals}";

        return $this->sql = $sql;
    }

    protected function sqlShow(): string
    {
        return $this->sqlRead();
    }

    protected function sqlCreate(): string
    {
        return $this->sqlInsert();
    }

    protected function sqlRead(): string
    {
        $this->select = $this->select ?? '*';

        $sql  = "SELECT {$this->select} ";
        $sql .= $this->table ? " FROM {$this->table} "     : '';
        $sql .= $this->where ? " WHERE ({$this->where}) "  : '';
        $sql .= $this->group ? " GROUP BY {$this->group} " : '';
        $sql .= $this->sort  ? " ORDER BY {$this->sort}"   : '';
        $sql .= $this->limit ? " LIMIT {$this->limit} "    : '';

        return $this->sql = $sql;
    }
    
    protected function sqlUpdate(): string
    {
        // For data safety, LiF enforce developer to specify where condictions
        if (! $this->where) {
            excp('No where condictions when delete records.');
        } elseif (! $this->updates) {
            excp('No update infomations.');
        }

        $sql  = "UPDATE {$this->table} ";
        $sql .= "SET {$this->updates} ";
        $sql .= "WHERE {$this->where} ";

        return $this->sql = $sql;
    }

    protected function sqlDelete(): string
    {
        // For data safety, LiF enforce developer to specify where condictions
        if (! $this->where) {
            excp('No where condictions when delete records.');
        }

        $sql  = "DELETE FROM {$this->table} ";
        $sql .= "WHERE {$this->where}";

        return $this->sql = $sql;
    }

    // ---------------------
    //     Create/Insert
    // ---------------------
    public function insert(array $inserts, $exec = true, $sql = false)
    {
        $this->insertKeys = $this->insertVals = '';

        $times = $_times = 0;
        foreach ($inserts as $key => $val) {
            ++$times;
            $hasNext = next($inserts);
            if (is_array($val) && $val) {
                foreach ($val as $_key => $_val) {
                    ++$_times;
                    $_hasNext = next($val);
                    if (1 === $times) {
                        $this->insertKeys .= $_key;
                        $this->insertKeys .= $_hasNext ? ',' : '';
                    }
                    $this->insertVals .= (1 === $_times) ? '(?' : '?';
                    $this->insertVals .= $_hasNext ? ',' : ')';
                    $this->insertVals .= (!$_hasNext && $hasNext) ? ',' : '';

                    $this->bindValues[] = $_val;
                }
                $_times = 0;
            } else {
                $this->insertKeys .= $key;
                $this->insertKeys .= $hasNext ? ',' : '';
                $this->insertVals .= (1 === $times) ? '(?' : '?';
                $this->insertVals .= $hasNext ? ',' : ')';

                $this->bindValues[] = $val;
            }
        }

        return $this->crud('INSERT')->execute($exec, $sql);
    }

    // ---------------------
    //     Read/Retrieve
    // ---------------------

    protected function legalSqlSelects($fields)
    {
        if (!is_string($fields) && !is_array($fields)) {
            return false;
        } elseif (! $fields) {
            return '*';
        }

        $selects    = (array) $fields;

        $select_str = '';

        foreach ($selects as $alias => $select) {
            if (is_array($select)) {
                foreach ($select as $_alias => $_select) {
                    if (! is_string($_select)) {
                        return false;
                    }

                    $select_str .= is_string($_alias)
                    ? $_select.' AS '.$_alias
                    : $_select;

                    $select_str .= (false === next($select))
                    ? '' : ', ';
                }
            } elseif (is_string($select)) {
                $select_str .= $select;
            } else {
                excp('Illgeal select field.');
            }

            $select_str .= (false === next($selects))
            ? '' : ', ';
        }

        return $select_str;
    }

    public function select(...$fields): LDO
    {
        if (false === ($selects = $this->legalSqlSelects($fields))) {
            excp('Illgeal select values.');
        }

        $this->crud('READ');
        $this->select = $selects;

        return $this;
    }

    public function leftJoin(
        string $table,
        string $fdLeft,
        string $cond,
        string $fdRight
    ): LDO
    {
        if ($this->table) {
            $this->table .= ' LEFT JOIN '
            .$table
            .' ON '
            .$fdLeft
            .' '
            .$cond
            .' '
            .$fdRight;
        }

        return $this;
    }

    public function limit($start = null, $offset = null): LDO
    {
        $this->crud('READ');

        $limit = '';

        if ((0 === $start) || $start) {
            if (is_numeric($start) && (0 <= intval($start))) {
                $limit .= $start;
            } else {
                excp('Illgeal limit offset: `'.$start.'`.');
            }
        }
        if ((0 === $offset) || $offset) {
            if (is_numeric($offset) && (0 <= intval($offset))) {
                $limit .= ', '.$offset;
            } else {
                excp('Illgeal limit offset: `'.$offset.'`.');
            }
        }

        $this->limit = $limit ? $limit : null;

        return $this;
    }

    public function sort(...$fields): LDO
    {
        $this->crud('READ');

        if ($fields) {
            $sort = '';
            foreach ($fields as $field => $order) {
                if (is_array($order)) {
                    foreach ($order as $_field => $_order) {
                        if (! is_string($_order)
                            || !($_order = strtoupper($_order))
                            || !in_array($_order, ['DESC', 'ASC'])
                        ) {
                            excp('Illgeal sort order.');
                        }

                        $sort .= (is_string($_field) && $_field)
                        ? $_field.' '.$_order
                        : $_order;

                        if (next($order)) {
                            $sort .= ', ';
                        }
                    }
                } elseif (is_string($order)) {
                    $sort .= $order;
                } else {
                    excp('Illgeal sort order.');
                }

                if (next($fields)) {
                    $sort .= ', ';
                }
            }

            $this->sort = $sort;
        }

        return $this;
    }

    public function group(...$fields): LDO
    {
        $this->crud('READ');

        $group = '';

        if ($fields) {
            foreach ($fields as $field) {
                if (is_array($field)) {
                    foreach ($field as $_field) {
                        if (! is_string($_field)) {
                            excp('Illgeal group by field.');
                        }

                        $group .= $_field;

                        if (next($field)) {
                            $group .= ', ';
                        }
                    }
                } elseif (is_string($field)) {
                    $group .= $field;
                } else {
                    excp('Illgeal group by field.');
                }

                if (next($fields)) {
                    $group .= ', ';
                }
            }

            $this->group = $group;
        }

        return $this;
    }

    // $exec => If execute SQL right now
    // $sql  => If get raw sql only (
    //  false: don't return raw sql
    //  1: without binding values
    //  2: with binding vlaues
    // )
    protected function execute($exec = true, $sql = false)
    {
        try {
            if ($sql) {
                $sqlArr = [
                    1 => $this->sql(),
                    2 => $this->__sql(),
                    3 => $this->lastSql(),
                    4 => $this->__lastSql(),
                ];

                return $sqlArr[$sql] ?? $sqlArr[1];
            } elseif ($exec) {
                $this->statement = $this->prepare($this->sql());
                foreach ($this->bindValues as $idx => $value) {
                    $type = is_bool($value)
                    ? self::PARAM_BOOL : (
                        is_null($value)
                        ? self::PARAM_NULL : (
                            is_integer($value)
                            ? self::PARAM_INT : self::PARAM_STR
                        )
                    );

                    $this->statement->bindValue(++$idx, $value, $type);
                }

                $this->statement->execute();

                if ('00000' !== ($this->status = $this->statement->errorCode())) {
                    $msg = implode(
                        '; ',
                        array_reverse($this->statement->errorInfo())
                    );
                    excp($msg);
                }

                // Reset for transaction
                $this->bindValues = [];
                $this->sql = $this->_sql = null;

                if (in_array($this->crud, [
                    'CREATE',
                    'INSERT',
                    'UPDATE',
                    'DELETE'
                ])) {
                    // Always return last insert ID when insert
                    // Number of rows affected by the last SQL statement
                    $this->result = ('INSERT' === $this->crud)
                    ? $this->lastInsertId()
                    : $this->statement->rowCount();

                    $this->transRes = $this->transRes && ($this->result >= 0);
                } else {
                    $this->result = $this->statement->fetchAll(
                        \PDO::FETCH_ASSOC
                    );

                    $this->trans = true;
                }

                return $this->result;
            }

            return $this;
        } catch (\PDOException $pdoe) {
            excp(
                $pdoe->getMessage()
                .' ( '.$this->sql.' )'
            );
        }
    }

    public function get($exec = true, $sql = false)
    {
        return $this->crud('READ')->execute($exec, $sql);
    }

    public function first($exec = true, $sql = false)
    {
        $this->limit = 1;

        $res = $this->crud('READ')->execute($exec, $sql);

        return $res[0] ?? [];
    }

    // --------------
    //     Update
    // --------------
    public function update(array $updates, $exec = true, $sql = false)
    {
        $this->updates = '';

        foreach ($updates as $key => $newVal) {
            $this->updates .= $key.'='.$newVal;

            if (next($updates)) {
                $this->updates .= ',';
            }
        }

        return $this->crud('UPDATE')->execute($exec, $sql);
    }

    // --------------
    //     Delete
    // --------------
    public function delete($exec = true, $sql = false)
    {
        return $this->crud('DELETE')->execute($exec, $sql);
    }

    public function truncate()
    {
        return $this->crud('DELETE')->raw('TRUNCATE TABLE '.$this->table);
    }

    public function raw($raw, array $values = [], $exec = true, $sql = false)
    {
        if (! $this->crud) {
            $sqlArr  = explode(' ', $raw);
            if (! isset($sqlArr[0])) {
                excp('Illgeal SQL statement.');
            } else {
                $this->curd = strtoupper($sqlArr[0]);
            }
        }

        $this->sql = $raw;
        $this->bindValues = $values;

        return $this->execute($exec, $sql);
    }
}
