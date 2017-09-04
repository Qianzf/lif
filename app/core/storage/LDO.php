<?php

// -------------------------------------------
//     Basic database query builder of LiF
// -------------------------------------------

namespace Lif\Core\Storage;

class LDO extends \PDO
{
    use \Lif\Core\Traits\MethodNotExists;

    protected $conn    = null;
    protected $crud    = null;
    protected $sql     = null;

    protected $table   = null;
    protected $select  = null;
    protected $where   = null;
    protected $sort    = null;
    protected $group   = null;
    protected $limit   = null;

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

    public function setAttribute($attr, $val)
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
                    || (!is_string($condVal) && !is_numeric($condVal))
                ) {
                    excp('Expecting second field of condition a string.');
                }

                $condVal = is_string($condVal) ? "'$condVal'" : $condVal;

                return '('.$condCol.' = '.$condVal.')';;
            } break;

            // Only one condition, and provide specific operator
            case 3: {
                if (!($condCol = $conds[0]) || !is_string($condCol)) {
                    excp('Expecting first field of condition a string.');
                } elseif (!($condOp = $conds[1])
                    || !is_string($condOp)
                ) {
                    excp('Expecting second field of condition a string.');
                } elseif (! $conds[2]
                    || (!is_string($conds[2]) && !is_array($conds[2]))
                ) {
                    excp('Expecting third field of condition.');
                }

                $condVal = is_array($conds[2])
                ? '('.implode(',', $conds[2]).')'
                : $conds[2];

                return '('.$condCol.' '.$condOp.' '.$condVal.')';
            } break;
            
            default: {
                excp('Illgeal where conditions.');
            } break;
        }
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
                        if (! is_array($conds[0][0])) {
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
                        $conds[0]($table);
                        $where = '('.$table->where.')';
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

    public function sql(): string
    {
        if ($this->sql) {
            return $this->sql;
        } else {
            if (!$this->table && !$this->select) {
                excp('No table or select commands specified.');
            } elseif (!$this->crud
                || !in_array($this->crud, [
                    'CREATE', 'READ', 'UPDATE', 'DELETE'
                ])
            ) {
                excp('Missing or wrong SQL manipulation.');
            }

            $sqlBuilder = 'sql'.ucfirst(strtolower($this->crud));
            return $this->sql = $this->$sqlBuilder();
        }
    }

    protected function sqlCreate(): string
    {
        return $this->sql;
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
        return $this->sql;
    }

    protected function sqlDelete(): string
    {
        return $this->sql;
    }

    // ---------------------
    //     Create/Insert
    // ---------------------
    public function insert()
    {
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

    public function get()
    {
        $this->crud('READ');

        try {
            $statement = $this->prepare($this->sql());

            // $statement->bindParam();
            $statement->execute();

            if ('00000' !== $statement->errorCode()) {
                $msg = implode('; ', array_reverse($statement->errorInfo()));
                excp($msg);
            }

            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $pdoe) {
            excp(
                $pdoe->getMessage()
                .' ( '.$this->sql.' )'
            );
        }
    }

    // --------------
    //     Update
    // --------------
    public function update()
    {
    }

    // --------------
    //     Delete
    // --------------
    public function delete()
    {
    }
}
