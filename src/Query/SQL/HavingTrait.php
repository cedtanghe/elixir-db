<?php

namespace Elixir\DB\Query\SQL;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait HavingTrait 
{
    /**
     * @var array 
     */
    protected $having = [];

    /**
     * @param string|callable $condition
     * @param mixed $value
     * @return SQLInterface
     */
    public function having($condition, $value = null) 
    {
        if (is_callable($condition)) 
        {
            $having = new static($this);
            call_user_func_array($condition, [$having]);
                    
            $condition = $having->render();
        }

        $this->having[] = ['query' => $this->assemble($condition, $value), 'type' => 'AND'];
        return $this;
    }

    /**
     * @param string|callable $condition
     * @param mixed $value
     * @return SQLInterface
     */
    public function orHaving($condition, $value = null)
    {
        if (is_callable($condition)) 
        {
            $having = new static($this);
            call_user_func_array($condition, [$having]);
                    
            $condition = $having->render();
        }

        $this->having[] = ['query' => $this->assemble($condition, $value), 'type' => 'OR'];
        return $this;
    }

    /**
     * @return string
     */
    protected function renderHaving() 
    {
        $SQL = '';

        if (count($this->having) > 0)
        {
            $SQL .= 'HAVING ';
            $first = true;

            foreach ($this->having as $having) 
            {
                $SQL .= ($first ? '' : $having['type'] . ' ') . (substr(trim($having['query']), 0, 1) != '(' ? '(' . $having['query'] . ')' : $having['query']) . "\n";
                $first = false;
            }

            $SQL .= ' ';
        }
        
        return $SQL;
    }
}
