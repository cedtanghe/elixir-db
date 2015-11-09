<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\JoinClause;
use Elixir\DB\Query\SQL\SQLInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait JoinTrait 
{
    /**
     * @var array 
     */
    protected $join = [];
    
    /**
     * @see JoinTrait::join()
     */
    public function innerJoin($table, $condition, $value = null, $column = null)
    {
        return $this->join($table, $condition, $value, $column, self::JOIN_INNER);
    }
    
    /**
     * @see JoinTrait::join()
     */
    public function leftJoin($table, $condition, $value = null, $column = null)
    {
        return $this->join($table, $condition, $value, $column, self::JOIN_LEFT);
    }
    
    /**
     * @see JoinTrait::join()
     */
    public function rightJoin($table, $condition, $value = null, $column = null)
    {
        return $this->join($table, $condition, $value, $column, self::JOIN_RIGHT);
    }
    
    /**
     * @see JoinTrait::join()
     */
    public function fullJoin($table, $condition, $value = null, $column = null)
    {
        return $this->join($table, $condition, $value, $column, self::JOIN_FULL);
    }
    
    /**
     * @param string $table
     * @param string|callable $condition
     * @param mixed $value
     * @param array|string $column
     * @param string $type
     * @return SQLInterface
     * @throws \RuntimeException
     */
    public function join($table, $condition, $value = null, $column = null, $type = self::JOIN_INNER) 
    {
        if (is_callable($condition)) 
        {
            $on = new JoinClause($this);
            call_user_func_array($condition, [$on]);
                    
            $condition = $on->render();
        }
        
        $this->join[] = ['query' => $this->assemble($condition, $value), 'type' => $type, 'table' => $table];

        if (null !== $column) 
        {
            if(!method_exists($this, 'column'))
            {
                throw new \RuntimeException('SQL does not have any method "column".');
            }
            
            $this->column($column, false);
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function renderJoin() 
    {
        $SQL = '';

        if (count($this->join) > 0) 
        {
            $first = true;

            foreach ($this->join as $join)
            {
                $query = $join['query'];

                if (substr($query, 3) != 'ON ' && substr($query, 6) != 'USING ') 
                {
                    $query = 'ON ' . (substr(trim($query), 0, 1) != '(' ? '(' . $query . ')' : $query);
                }

                $SQL .= ($first ? $join['type'] : ' ' . $join['type']) . ' JOIN ' . $join['table'] . ' ' . $query . "\n";
                $first = false;
            }

            $SQL .= ' ';
        }

        return $SQL;
    }
}
