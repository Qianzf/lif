<?php

// -------------------------------------------
//     Basic database query builder of LiF
// -------------------------------------------

namespace Lif\Core\Storage;

class LDO extends \PDO
{
    use \Lif\Core\Traits\MethodNotExists;

    // Un-resetable attrs
    protected $conn     = null;
    protected $table    = null;
    protected $lastSql  = null;
    protected $_lastSql = null;
    protected $result   = [];
    protected $transRes = true;    // Transaction result
    protected $lastWhere = null;
    protected $lastBindValues = [];

    // Resetable attrs
    protected $sql      = null;    // Used for prepare statement
    protected $_sql     = null;    // Used for debug
    protected $crud     = null;
    protected $status   = null;

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

    public function getAttrsForModel(): array
    {
        return [
            'where'      => $this->where,
            'bindValues' => $this->bindValues,
        ];
    }

    public function setAttrsForModel(array $attrs): LDO
    {
        foreach ($attrs as $key => $value) {
            // !!! Use `isset()` to check a null value is false
            if (isset($this->$key) || is_null($this->$key)) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    public function reset()
    {
        $this->lastWhere      = $this->where;
        $this->lastBindValues = $this->bindValues;
        $this->sql     = null;
        $this->crud    = null;
        $this->status  = null;
        $this->select  = null;
        $this->where   = null;
        $this->sort    = null;
        $this->group   = null;
        $this->limit   = null;
        $this->updates = null;
        $this->insertKeys = null;
        $this->insertVals = null;
        $this->statement  = null;
        $this->bindValues = [];
    }

    public function __get($name)
    {
        return $this->$name();
    }

    public function __call($name, $args): LDO
    {
        if ('where' === mb_substr($name, 0, 5)) {
            if (! $args) {
                dd($name, $args);
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
                if (1 === $argCnt) {
                    $args = ['=', $args[0]];
                } elseif (2 === $argCnt) {
                } else {
                    excp('Illgeal conditions amount.');
                }
                array_walk($fields,
                    function (&$item, $key, $args) {
                        $item = [
                            $item,
                            $args[0],
                            $args[1],
                        ];
                }, $args);
            } else {
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
            }

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

        $this->table = escape_fields($table);

        return $this;
    }

    // Verify where conditionals and parse out fields and their conditions
    protected function whereConditionals(array $conds)
    {
        switch (count($conds)) {
            // Assoc array
            // Field name is array key, field value is array value
            // Use default operator `=`
            case 1: {
                $keys    = array_keys($conds);
                $values  = array_values($conds);
                if (!isset($keys[0]) || !($condCol = $keys[0])) {
                    excp('Illgeal where field name.');
                }
                if (! isset($values[0])) {
                    excp('Missing where field value.');
                }
                $condVal = $values[0];
                $condOp  = '=';
            } break;
            // Only one condition, and use default specific operator `=`
            case 2: {
                if (!($condCol = $conds[0]) || !is_string($condCol)) {
                    excp('Expecting first field of condition a string(2).');
                }
                if (isset($conds[1])) {
                    if (!is_string($conds[1])
                        && !is_numeric($conds[1])
                        && !is_array($conds[1])
                        && !is_callable($conds[1])
                    ) {
                        excp(
                            'Expecting second field of condition (string/array/closure).'
                        );
                    }
                } else {
                    $conds[1] = null;
                }

                $condVal = $conds[1];
                $condOp  = '=';
            } break;

            // Only one condition, and provide specific operator
            case 3: {
                if (!($condCol = $conds[0]) || !is_string($condCol)) {
                    excp('Expecting first field of condition a string(3).');
                }
                if (!($condOp = $conds[1]) || !is_string($condOp)
                ) {
                    excp('Expecting second field of condition a string.');
                }
                if (isset($conds[2])) {
                    if (!is_string($conds[2])
                        && !is_numeric($conds[2])
                        && !is_array($conds[2])
                        && !is_callable($conds[2])
                    ) {
                        excp('Expecting third field of condition.');
                    }
                } else {
                    $conds[2] = null;
                }

                $condVal = $conds[2];
            } break;
            
            default: {
                excp('Illgeal where conditions.');
            } break;
        }

        if (is_scalar($condVal)) {
            $condOpWithVal      = ' '.$condOp.' ?';
            $this->bindValues[] = $condVal;
        } elseif (is_array($condVal)) {
            $stubs = '';
            foreach ($condVal as $key => $val) {
                $item   = escape_fields($val);
                $this->bindValues[] = $val;
                $stubs .= '?';
                if (false !== next($condVal)) {
                    $stubs .= ',';
                }
            }
            $condOpWithVal = ' in ('.$stubs.')';
        } elseif (is_null($condVal)) {
            $condOpWithVal = ' is null';
        } elseif (is_callable($condVal)) {
            $condOpWithVal = ' '.$condOp.' '.$condVal();
        } else {
            excp('Illgeal where field value.');
        }

        return '('.$condCol.$condOpWithVal.')';
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

    public function __clone()
    {
        // Clear tmp stack
        $this->where = null;
        $this->bindValues = [];
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

    protected function __where(array $conds): string
    {
        $where = '';
        if ($conds) {
            switch (count($conds)) {
                // Conditions formatted with array
                case 1: {
                    if (!$conds[0]
                        || (! is_scalar($conds[0])
                            && !is_array($conds[0])
                            && !($conds[0] instanceof \Closure)
                        )
                    ) {
                        excp('Illgeal conditions, expect an un-empty array or closure.');
                    }

                    if (is_scalar($conds[0])) {
                        $where = $conds[0];
                    } elseif (is_array($conds[0])) {
                        $_conds = $conds[0][array_keys($conds[0])[0]];
                        if (! is_array($_conds)) {
                            $where = $this->whereConditionals($conds[0]);
                        } else {
                            foreach ($conds[0] as $key => $cond) {
                                $where .= $this->whereConditionals($cond);
                                if (false !== next($conds[0])) {
                                    $where .= ' AND ';
                                }
                            }
                        }
                    } elseif (is_callable($conds[0])) {
                        $table = clone $this;
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
                    $where = $this->whereConditionals($conds);
                } break;
                
                default: {
                    excp('Illgeal where conditions');
                } break;
            }
        }

        return $where ? '('.$where.')' : '';
    }

    public function lastSql(): string
    {
        if ($this->lastSql) {
            return $this->lastSql;
        }

        return $this->lastSql = $this->sql();
    }

    public function _lastSql(): string
    {
        if ($this->_lastSql) {
            return $this->_lastSql;
        }

        return $this->_lastSql = $this->_sql();
    }

    public function _sql(): string
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
        
        $sql .= mb_strlen($this->limit)
        ? " LIMIT {$this->limit} "   : '';

        return $this->sql = $sql;
    }
    
    protected function sqlUpdate(): string
    {
        // For data safety, LiF enforce developer to specify where condictions
        if (! $this->where) {
            excp('No where condictions when update records.');
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
            $hasNext = (false === next($inserts)) ? false : true;
            if (is_array($val)) {
                foreach ($val as $_key => $_val) {
                    ++$_times;
                    $_hasNext = (false === next($val)) ? false : true;
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
            return '`*`';
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
                    ? escape_fields($_select).' AS '.escape_fields($_alias)
                    : escape_fields($_select);

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

    public function count($exec = true, $sql = false)
    {
        $this->select = 'count(*) AS `count`';

        $res = $this->execute($exec, $sql);

        return intval($res[0]['count'] ?? 0);
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
            .escape_fields($table)
            .' ON '
            .escape_fields($fdLeft)
            .' '
            .$cond
            .' '
            .escape_fields($fdRight);
        }

        return $this;
    }

    public function limit($start = null, $offset = null): LDO
    {
        $this->crud('READ');

        $start  = is_null($start)  ? null : intval($start);
        $offset = is_null($offset) ? null : intval($offset);

        $limit  = '';

        if ((0 === $start) || $start) {
            if (0 <= intval($start)) {
                $limit .= $start;
            } else {
                excp('Illgeal limit start: `'.$start.'`.');
            }
        }
        if ((0 === $offset) || $offset) {
            if (0 <= intval($offset)) {
                $limit .= ', '.$offset;
            } else {
                excp('Illgeal limit offset: `'.$offset.'`.');
            }
        }

        $this->limit = $limit ?? null;

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
                        ? escape_fields($_field).' '.$_order
                        : escape_fields($_order);

                        if (false !== next($order)) {
                            $sort .= ', ';
                        }
                    }
                } elseif (is_string($order)) {
                    $sort .= escape_fields($order);
                } else {
                    excp('Illgeal sort order.');
                }

                if (false !== next($fields)) {
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

                        if (false !== next($field)) {
                            $group .= ', ';
                        }
                    }
                } elseif (is_string($field)) {
                    $group .= $field;
                } else {
                    excp('Illgeal group by field.');
                }

                if (false !== next($fields)) {
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
                    2 => $this->_sql(),
                    3 => $this->lastSql(),
                    4 => $this->_lastSql(),
                ];
                return $sqlArr[$sql] ?? $sqlArr[1];
            }
            if ($exec) {
                $this->statement = $this->prepare(
                    $this->sql(), [
                        // self::ATTR_CURSOR => self::CURSOR_SCROLL
                    ]
                );
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

                $this->reset();    // Reset for transaction

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

    public function get($exec = true, $sql = false): array
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
        $bindValues    = [];
        
        foreach ($updates as $key => $newVal) {
            if (is_callable($newVal)) {
                $this->updates .= $key.' = ('.$newVal().')';
            } else {
                $this->updates .= $key.' = ? ';
                $bindValues[]   = $newVal;
            }

            if (false !== next($updates)) {
                $this->updates .= ',';
            }
        }

        $this->bindValues = array_merge($bindValues, $this->bindValues);

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

    public function native(string $native)
    {
        return function () use ($native) : string {
            return $native;
        };
    }
}
