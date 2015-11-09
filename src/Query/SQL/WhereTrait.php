<?php

namespace Elixir\DB\Query\SQL;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait WhereTrait 
{
    /**
     * @var array 
     */
    protected $where = [];

    /**
     * @param string|callable $condition
     * @param mixed $value
     * @return SQLInterface
     */
    public function where($condition, $value = null) 
    {
        if (is_callable($condition)) 
        {
            $where = new static($this);
            call_user_func_array($condition, [$where]);
                    
            $condition = $where->render();
        }

        $this->where[] = ['query' => $this->assemble($condition, $value), 'type' => 'AND'];
        return $this;
    }

    /**
     * @param string|callable $condition
     * @param mixed $value
     * @return SQLInterface
     */
    public function orWhere($condition, $value = null)
    {
        if (is_callable($condition)) 
        {
            $where = new static($this);
            call_user_func_array($condition, [$where]);
                    
            $condition = $where->render();
        }

        $this->where[] = ['query' => $this->assemble($condition, $value), 'type' => 'OR'];
        return $this;
    }

    /**
     * @return string
     */
    protected function renderWhere() 
    {
        $SQL = '';

        if (count($this->where) > 0)
        {
            $SQL .= 'WHERE ';
            $first = true;

            foreach ($this->where as $where) 
            {
                $SQL .= ($first ? '' : $where['type'] . ' ') . (substr(trim($where['query']), 0, 1) != '(' ? '(' . $where['query'] . ')' : $where['query']) . "\n";
                $first = false;
            }

            $SQL .= ' ';
        }
        
        return $SQL;
    }
}
