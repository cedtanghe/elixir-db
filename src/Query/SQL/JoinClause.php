<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\SQLInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class JoinClause 
{
    /**
     * @var SQLInterface
     */
    protected $SQL;

    /**
     * @var array 
     */
    protected $on = [];

    /**
     * @var array 
     */
    protected $using = [];

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
     * @return JoinClause
     */
    public function on($condition, $value = null)
    {
        if (is_callable($condition)) 
        {
            $on = new static($this->SQL);
            call_user_func_array($condition, [$on]);
                    
            $condition = $on->render();
        }

        $this->on[] = ['query' => $this->SQL->assemble($condition, $value), 'type' => 'AND'];
        return $this;
    }

    /**
     * @param string|callable $condition
     * @param mixed $value
     * @return JoinClause
     */
    public function orOn($condition, $value = null) 
    {
        if (is_callable($condition)) 
        {
            $on = new static($this->SQL);
            call_user_func_array($condition, [$on]);
                    
            $condition = $on->render();
        }

        $this->on[] = ['query' => $this->SQL->assemble($condition, $value), 'type' => 'OR'];
        return $this;
    }

    /**
     * @param array|string $using
     * @return JoinClause
     */
    public function using($using) 
    {
        $this->using = array_merge($this->using, (array)$using);
        return $this;
    }

    /**
     * @param array|string $column
     * @param boolean $reset
     * @return JoinClause
     * @throws \BadMethodCallException
     */
    public function column($column = SQLInterface::STAR, $reset = false)
    {
        if(!method_exists($this->SQL, 'column'))
        {
            throw new \BadMethodCallException('SQL does not have any method "column".');
        }
        
        $this->SQL->column($column, $reset);
        return $this;
    }

    /**
     * @param string $part
     * @return JoinClause
     */
    public function reset($part)
    {
        switch ($part) 
        {
            case 'on':
                $this->on = [];
                break;
            case 'using':
                $this->using = [];
                break;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function get($pPart) 
    {
        switch ($pPart) 
        {
            case 'on':
                return $this->on;
            case 'using':
                return $this->using;
        }
    }

    /**
     * @param mixed $data
     * @param string $part
     * @return JoinClause
     */
    public function merge($data, $part) 
    {
        switch ($part) 
        {
            case 'on':
                $this->on = array_merge($this->on, $data);
                break;
            case 'using':
                $this->using($data);
                break;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function render() 
    {
        $SQL = '';

        if (count($this->using) > 0) 
        {
            $SQL .= $this->renderUsing();
        } 
        else 
        {
            $SQL .= $this->renderOn();
        }

        return $SQL;
    }

    /**
     * @return string
     */
    protected function renderUsing() 
    {
        return 'USING (' . implode(', ', $this->using) . ')';
    }
    
    /**
     * @return string
     */
    protected function renderOn()
    {
        $SQL = '';
        $first = true;

        foreach ($this->on as $on)
        {
            $SQL .= ($first ? '' : $on['type'] . ' ') . '(' . $on['query'] . ')' . "\n";
            $first = false;
        }

        if (count($this->on) > 1)
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
