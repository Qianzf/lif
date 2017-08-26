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

    public function where(...$conds): LDO
    {
        if ($conds) {
            switch (count($conds)) {
                case 1: {
                } break;
                case 2: {
                } break;
                case 3: {
                } break;
                default:
                break;
            }
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
        $sql .= $this->table ? " FROM {$this->table} "   : '';
        $sql .= $this->where ? " {$this->where} "        : '';
        $sql .= $this->group ? " GROUP BY {$this->group} "        : '';
        $sql .= $this->sort  ? " ORDER BY {$this->sort}" : '';
        $sql .= $this->limit ? " LIMIT {$this->limit} "  : '';

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

    // $fields => String | Array
    public function select(...$fields): LDO
    {
        if (false === ($selects = legal_sql_selects($fields))) {
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
