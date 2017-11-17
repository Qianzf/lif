<?php

// -------------------------------------------
//     Basic database query builder of LiF
// -------------------------------------------

namespace Lif\Core\Storage\SQL;

class Builder implements \Lif\Core\Intf\DBConn
{
    use \Lif\Core\Traits\MethodNotExists;
    use \Lif\Core\Traits\WithDB;

    protected $transRes = true;    // Transaction result
    protected $sql      = null;    // Used for prepare statement
    protected $_sql     = null;    // Used for debug
    protected $crud     = null;    // SQL query type
    protected $status   = null;    // Statement execute status
    protected $result   = [];      // Statement execute result

    // SQL parts
    protected $table   = null;
    protected $selects = null;
    protected $where   = null;
    protected $sort    = null;
    protected $group   = null;
    protected $limit   = null;
    protected $updates = null;
    protected $insertKeys = null;
    protected $insertVals = null;
    protected $statement  = null;
    protected $bindValues = [];

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

    public function inTrans()
    {
        return $this->db()->inTransaction();
    }

    public function start(): void
    {
        if (! $this->inTrans()) {
            $this->db()->beginTransaction();
        }
    }

    public function commit(): void
    {
        if ($this->inTrans()) {
            $this->db()->commit();
        }
    }

    public function rollback(): void
    {
        if ($this->inTrans()) {
            $this->db()->rollBack();    // !!! `B` is uppercase
        }
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getTransRes()
    {
        return $this->transRes;
    }

    protected function crud($value = null): Builder
    {
        $this->crud = strtoupper($value ?? $this->crud);

        return $this;
    }

    public function reset() : Builder
    {
        $this->sql     = null;
        $this->_sql    = null;
        $this->crud    = null;
        $this->status  = null;
        $this->selects = null;
        $this->where   = null;
        $this->sort    = null;
        $this->group   = null;
        $this->limit   = null;
        $this->updates = null;
        $this->insertKeys = null;
        $this->insertVals = null;
        $this->statement  = null;
        $this->bindValues = [];
        // $this->table      = null;
        // $this->result     = [];
        $this->transRes   = true;

        return $this;
    }

    public function __get($name)
    {
        return $this->$name();
    }

    public function __call($name, $args): Builder
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

    public function table(string $name, string $alias = null): Builder
    {
        if (empty_safe($name) || is_numeric($name)) {
            excp('Table name only support un-empty, un-numeric string.');
        }

        if (!is_null($alias) && is_numeric($alias)) {
            excp('Table alias only support un-numeric string.');
        }

        $this->table = is_null($alias)
        ? $name
        : escape_fields($name).' AS '.escape_fields($alias);

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
            // Only one condition, and use default operator `=`
            case 2: {
                $condCol = $conds[0] ?? ($conds['col'] ?? null);
                if (!$condCol
                    || !(is_string($condCol) || is_closure($condCol))
                ) {
                    excp(
                        'Expecting first field of condition a un-empty string or closure(1).'
                    );
                }
                $condVal = $conds[1] ?? ($conds['val'] ?? null);
                if (!is_null($condVal)
                    && !is_scalar($condVal)
                    && !is_array($condVal)
                    && !is_closure($condVal)
                ) {
                    excp(
                        'Expecting second field of condition (string/array/closure).'
                    );
                }
                $condOp  = '=';
            } break;

            // Only one condition, and provide specific operator
            case 3: {
                $condCol = $conds[0] ?? ($conds['col'] ?? null);
                if (!$condCol
                    || !(is_string($condCol) || is_closure($condCol))
                ) {
                    excp(
                        'Expecting first field of condition a un-empty string or closure(2).'
                    );
                }
                $condOp = $conds[1] ?? ($conds['op'] ?? null);
                if (!$condOp || !is_string($condOp)) {
                    excp('Expecting second field of condition a string.');
                }
                $condVal = $conds[2] ?? ($conds['val'] ?? null);
                if (!is_null($condVal)
                    && !is_scalar($condVal)
                    && !is_array($condVal)
                    && !is_closure($condVal)
                ) {
                    excp('Expecting third field of condition.');
                }
            } break;
            
            default: {
                excp('Illgeal where conditions.');
            } break;
        }

        $condCol = is_closure($condCol)
        ? $condCol()
        : escape_fields($condCol);

        if (is_scalar($condVal)) {
            $condOpWithVal      = " $condOp ?";
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
            $condOpWithVal = "in ($stubs)";
        } elseif (is_null($condVal)) {
            $condOpWithVal = ' is null';
        } elseif (is_closure($condVal)) {
            $condOpWithVal = " $condOp ".$condVal();
        } else {
            excp('Illgeal where field value.');
        }

        return "({$condCol}{$condOpWithVal})";
    }

    public function or(...$conds): Builder
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

    public function where(...$conds): Builder
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
                        excp(
                            'Illgeal conditions, expect an un-empty array or closure.'
                        );
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
                    } elseif (is_closure($conds[0])) {
                        $table = clone $this;
                        $conds[0]($table);
                        $where = $table->where ? "({$table->where})" : null;

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

        return $where ? "({$where})" : '';
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

        return $this->_sql = $sql;
    }

    public function sql(): string
    {
        if ($this->sql) {
            return $this->sql;
        }

        if (!$this->table && !$this->selects) {
            excp('No base table or select commands specified.');
        } elseif (! $this->crud) {
            excp('No query operations yet.');
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

        $builder = 'sql'.ucfirst(strtolower($this->crud));

        if (! method_exists($this, $builder)) {
            excp('SQL handler not exists: ', $builder);
        }

        return $this->sql = call_user_func([$this, $builder]);
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
        $this->selects = $this->selects ?? '*';

        $sql  = "SELECT {$this->selects} ";
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
                        $this->insertKeys .= escape_fields($_key);
                        $this->insertKeys .= $_hasNext ? ',' : '';
                    }
                    $this->insertVals .= (1 === $_times) ? '(?' : '?';
                    $this->insertVals .= $_hasNext ? ',' : ')';
                    $this->insertVals .= (!$_hasNext && $hasNext) ? ',' : '';

                    $this->bindValues[] = $_val;
                }
                $_times = 0;
            } else {
                $this->insertKeys .= escape_fields($key);
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
        }
        if (! $fields) {
            return '`*`';
        }

        $selects   = (array) $fields;
        $selectStr = '';

        foreach ($selects as $alias => $select) {
            if (is_array($select)) {
                foreach ($select as $_alias => $_select) {
                    if (! is_string($_select)) {
                        return false;
                    }

                    $selectStr .= is_string($_alias)
                    ? escape_fields($_select).' AS '.escape_fields($_alias)
                    : escape_fields($_select);

                    $selectStr .= (false === next($select))
                    ? '' : ', ';
                }
            } elseif (is_string($select)) {
                $selectStr .= $select;
            } else {
                excp('Illgeal select field.');
            }

            $selectStr .= (false === next($selects))
            ? '' : ', ';
        }

        return $selectStr;
    }

    public function count($exec = true, $sql = false)
    {
        $this->selects = 'count(*) AS `count`';

        $res = $this->crud('READ')->execute($exec, $sql);

        return isset($res[0]['count'])
        ? intval($res[0]['count'])
        : $res;
    }

    public function select(...$fields): Builder
    {
        if (false === ($selects = $this->legalSqlSelects($fields))) {
            excp('Illgeal select values.');
        }

        $this->crud('READ');
        $this->selects = $selects;

        return $this;
    }

    public function leftJoin(
        string $table,
        string $fdLeft,
        string $cond,
        string $fdRight = null
    ): Builder
    {
        if (is_null($fdRight)) {
            $fdRight = $cond;
            $cond = '=';
        }

        if ($this->table) {
            $this->table .= ' LEFT JOIN '
            .$table
            .' ON '
            .escape_fields($fdLeft)
            .' '
            .$cond
            .' '
            .escape_fields($fdRight);
        }

        return $this;
    }

    public function limit($start = null, $offset = null): Builder
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

    public function sort(...$fields): Builder
    {
        $this->crud('READ');

        if ($fields) {
            $sort = '';
            foreach ($fields as $field => $order) {
                if (is_array($order)) {
                    foreach ($order as $_field => $_order) {
                        if (!is_string($_order)
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

    public function group(...$fields): Builder
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

    public function get($exec = true, $sql = false)
    {
        return $this->crud('READ')->execute($exec, $sql);
    }

    public function first($exec = true, $sql = false)
    {
        $this->limit = 1;

        $res = $this->crud('READ')->execute($exec, $sql);

        return (is_array($res) && isset($res[0]))
        ? $res[0]
        : $res;
    }

    // --------------
    //     Update
    // --------------
    public function update(...$updates)
    {
        $exec = true;
        $sql  = false;
        switch (count($updates)) {
            case 1: {
                if (!isset($updates[0])
                    || !is_array($updates[0])
                    || !($updates = $updates[0])
                ) {
                    excp('Illgeal updates parameters (1)');
                }
            } break;
            case 2: {
                if (!isset($updates[0])
                    || !is_string($updates[0])
                    || !$updates[0]
                ) {
                    excp('Illgeal updates parameters (2)');
                }

                $updates = [$updates[0] => $updates[1]];
            } break;
            case 3: {
                if (is_array($updates[0]) && $updates[0]) {
                    $exec    = (bool) $updates[1];
                    $sql     = (bool) $updates[2];
                    $updates = $updates[0];
                } elseif (is_string($updates[0]) && $updates[0]) {
                    $exec    = (bool) $updates[2];
                    $updates = [$updates[0] => $updates[1]];
                } else {
                    excp('Illgeal updates parameters (3)');
                }
            } break;
            case 4: {
                if (is_string($updates[0])) {
                    $exec = (bool) $updates[2];
                    $sql  = (bool) $updates[3];
                }

                excp('Illgeal updates parameters (4)');
            } break;
            default: {
                excp('Illgeal updates parameters (5)');
            } break;
        }

        $this->updates = '';
        $bindValues    = [];
        
        foreach ($updates as $key => $newVal) {
            $_key = escape_fields($key);
            if (is_closure($newVal)) {
                $this->updates .= $_key.' = ('.$newVal().')';
            } else {
                $this->updates .= $_key.' = ? ';
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

    public function truncate(string $table = null)
    {
        if ($table = $table ?: $this->table) {
            return $this->crud('DELETE')->raw("TRUNCATE TABLE `{$table}`");
        }

        excp('Missing table name to truncate.');
    }

    public function raw($raw, array $values = [], $exec = true, $sql = false)
    {
        if (! $this->crud) {
            $sqlArr  = explode(' ', $raw);

            if (! isset($sqlArr[0])) {
                excp('Illgeal SQL statement.');
            } else {
                $this->crud = strtoupper(trim($sqlArr[0]));
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

    public function exec(string $sql)
    {
        try {
            return $this->db()->exec($sql);
        } catch (\PDOException $pdoe) {
            excp($pdoe->getMessage()."({$sql})");
        } catch (\Error $e) {
            excp($e->getMessage()."({$sql})");
        }
    }

    public function query(string $sql)
    {
        try {
            return $this->db()->query($sql);
        } catch (\PDOException $pdoe) {
            excp($pdoe->getMessage()."({$sql})");
        } catch (\Error $e) {
            excp($e->getMessage()."({$sql})");
        }
    }

    // $exec => If execute SQL immediately
    // $sql  => If get raw sql only (
    //  false: don't return raw sql
    //  1: without binding values
    //  2: with binding vlaues
    // )
    // @return:
    // => object(self)
    // => array
    // => string
    // => integer
    protected function execute(bool $exec = true, $sql = false)
    {
        try {
            if ($sql) {
                switch ($sql) {
                    case 1:
                    default:
                        return $this->sql();
                        break;
                    case 2:
                        return $this->_sql();
                        break;
                }
            }
            if ($exec) {
                $this->statement = $this->db()
                ->prepare(
                    $this->sql(), [
                        // \PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL
                    ]
                );
                foreach ($this->bindValues as $idx => $value) {
                    $type = is_bool($value)
                    ? \PDO::PARAM_BOOL : (
                        is_null($value)
                        ? \PDO::PARAM_NULL : (
                            is_integer($value)
                            ? \PDO::PARAM_INT : \PDO::PARAM_STR
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
                    $this->result = intval(('INSERT' === $this->crud)
                    // Always return last insert ID when inserting
                    ? $this->db()->lastInsertId()
                    // Or number of rows affected by the last SQL statement
                    : $this->statement->rowCount());

                    $this->transRes = $this->transRes && ($this->result >= 0);
                } else {
                    $this->result = $this->statement->fetchAll(
                        \PDO::FETCH_ASSOC
                    );

                    $this->transRes = true;
                }

                $this->reset();    // Reset for transaction

                return $this->result;
            }

            return $this;
        } catch (\PDOException $pdoe) {
            excp($pdoe->getMessage()."({$this->sql})");
        } catch (\Error $e) {
            excp($e->getMessage()."({$this->sql})");
        }
    }
}
