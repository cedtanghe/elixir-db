<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\SQLInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class WhereClause 
{
    /**
     * @var SQLInterface
     */
    protected $SQL;

    /**
     * @var array 
     */
    protected $where = [];

    /**
     * @param SQLInterface $SQL
     */
    public function __construct(SQLInterface $SQL) 
    {
        $this->SQL = $SQL;
    }

    /**
     * @param string|callable $condition
     * @param mixed $value
     * @return WhereClause
     */
    public function where($condition, $value = null)
    {
        if (is_callable($condition)) 
        {
            $where = new static($this->SQL);
            call_user_func_array($condition, [$where]);
                    
            $condition = $where->render();
        }

        $this->where[] = ['query' => $this->SQL->assemble($condition, $value), 'type' => 'AND'];
        return $this;
    }

    /**
     * @param string|callable $condition
     * @param mixed $value
     * @return WhereClause
     */
    public function orWhere($condition, $value = null) 
    {
        if (is_callable($condition))
        {
            $where = new static($this->SQL);
            call_user_func_array($condition, [$where]);
                    
            $condition = $where->render();
        }

        $this->where[] = ['query' => $this->SQL->assemble($condition, $value), 'type' => 'OR'];
        return $this;
    }

    /**
     * @return WhereClause
     */
    public function reset() 
    {
        $this->where = [];
        return $this;
    }
    
    /**
     * @return array
     */
    public function get() 
    {
        return $this->where;
    }
    
    /**
     * @param array $data
     * @return WhereClause
     */
    public function merge(array $data) 
    {
        $this->where = array_merge($this->where, $data);
        return $this;
    }

    /**
     * @return string
     */
    public function render() 
    {
        return $this->renderWhere();
    }

    /**
     * @return string
     */
    protected function renderWhere() 
    {
        $SQL = '';
        $first = true;

        foreach ($this->where as $where) 
        {
            $SQL .= ($first ? '' : $where['type'] . ' ') . '(' . $where['query'] . ')' . "\n";
            $first = false;
        }

        if (count($this->where) > 1) 
        {
            $SQL = '(' . $SQL . ')';
        }

        return $SQL;
    }

    /**
     * @ignore
     */
    public function __toString() 
    {
        return $this->render();
    }
}
