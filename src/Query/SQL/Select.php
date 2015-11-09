<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\HavingTrait;
use Elixir\DB\Query\SQL\JoinTrait;
use Elixir\DB\Query\SQL\LimitTrait;
use Elixir\DB\Query\SQL\OrderTrait;
use Elixir\DB\Query\SQL\SQLAbstract;
use Elixir\DB\Query\SQL\WhereTrait;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Select extends SQLAbstract 
{
    use JoinTrait;
    use WhereTrait;
    use HavingTrait;
    use OrderTrait;
    use LimitTrait;

    /**
     * @var string 
     */
    protected $quantifier;
    
    /**
     * @var array 
     */
    protected $columns = [];
    
    /**
     * @var array 
     */
    protected $group = [];

    /**
     * @var array 
     */
    protected $combine = [];
    
    /**
     * @param string $quantifier
     * @return Select
     */
    public function quantifier($quantifier) 
    {
        $this->quantifier = $quantifier;
        return $this;
    }
    
    /**
     * @param array|string $column
     * @param boolean $reset
     * @return Select
     */
    public function column($column = self::STAR, $reset = false)
    {
        if ($reset) 
        {
            $this->columns = [];
        }
        
        $this->columns = array_merge($this->columns, (array)$column);
        return $this;
    }
    
    /**
     * @param array|string $group
     * @return Select
     */
    public function group($group) 
    {
        $this->group = array_merge($this->group, (array)$group);
        return $this;
    }
    
    /**
     * @see Select::combine()
     */
    public function combineUnion(array $SQL)
    {
        return $this->combine($SQL, self::COMBINE_UNION);
    }
    
    /**
     * @see Select::combine()
     */
    public function combineUnionAll(array $SQL)
    {
        return $this->combine($SQL, self::COMBINE_UNION_ALL);
    }
    
    /**
     * @see Select::combine()
     */
    public function combineExpect(array $SQL)
    {
        return $this->combine($SQL, self::COMBINE_EXPECT);
    }
    
    /**
     * @see Select::combine()
     */
    public function combineIntersect(array $SQL)
    {
        return $this->combine($SQL, self::COMBINE_INTERSECT);
    }
    
    /**
     * @param array $SQL
     * @param string $type
     * @return Select
     */
    public function combine(array $SQL, $type = self::COMBINE_UNION)
    {
        $this->combine['SQL'] = $SQL;
        $this->combine['type'] = $type;

        return $this;
    }

    /**
     * @param string $part
     * @return Select
     */
    public function reset($part) 
    {
        switch ($part) 
        {
            case 'table':
                $this->table = null;
                break;
            case 'alias':
                $this->alias(null);
                break;
            case 'columns':
                $this->columns = [];
                break;
            case 'quantifier':
                $this->quantifier = null;
                break;
            case 'join':
                $this->join = [];
                break;
            case 'where':
                $this->where = [];
                break;
            case 'group':
                $this->group = [];
                break;
            case 'having':
                $this->having = [];
                break;
            case 'order':
                $this->order = [];
                break;
            case 'limit':
                $this->limit = null;
                break;
            case 'offset':
                $this->offset = null;
                break;
            case 'combine':
                $this->combine = [];
                break;
        }

        return $this;
    }
    
    /**
     * @param string $part
     * @return mixed
     */
    public function get($part) 
    {
        switch ($part) 
        {
            case 'table':
                return $this->table;
            case 'alias':
                return $this->alias;
            case 'columns':
                return $this->columns;
            case 'quantifier':
                return $this->quantifier;
            case 'join':
                return $this->join;
            case 'where':
                return $this->where;
            case 'group':
                return $this->group;
            case 'having':
                return $this->having;
            case 'order':
                return $this->order = [];
            case 'limit':
                return $this->limit;
            case 'offset':
                return $this->offset;
            case 'combine':
                return $this->combine;
        }
        
        return null;
    }
    
    /**
     * @param mixed $data
     * @param string $part
     * @return Select
     */
    public function merge($data, $part) 
    {
        switch ($part) 
        {
            case 'table':
                $this->table($data);
                break;
            case 'alias':
                $this->alias($data);
                break;
            case 'columns':
                $this->column($data, false);
                break;
            case 'quantifier':
                $this->quantifier($data);
                break;
            case 'join':
                $this->join = array_merge($this->join, $data);
                break;
            case 'where':
                $this->where = array_merge($this->where, $data);
                break;
            case 'group':
                $this->group = array_merge($this->group, $data);
                break;
            case 'having':
                $this->having = array_merge($this->having, $data);
                break;
            case 'order':
                $this->order = array_merge($this->order, $data);
                break;
            case 'limit':
                $this->limit($data);
                break;
            case 'offset':
                $this->offset($data);
                break;
            case 'combine':
                $this->combine = array_merge($this->combine, $data);
                break;
        }
        
        return $this;
    }

    /**
     * @see SQLInterface::render()
     */
    public function render() 
    {
        $SQL = '';

        if (count($this->combine) > 0) 
        {
            $SQL .= '(' . implode(')' . "\n" . $this->combine['type'] . "\n" . '(', $this->combine['SQL']) . ') ' . "\n";
            $SQL .= $this->renderOrder();
            $SQL .= $this->renderLimit();
        } 
        else
        {
            $SQL .= 'SELECT ' . "\n";
            $SQL .= $this->renderQuantifier();
            $SQL .= $this->renderColumns();
            $SQL .= 'FROM ' . $this->table . ' ' . "\n";
            $SQL .= $this->renderJoin();
            $SQL .= $this->renderWhere();
            $SQL .= $this->renderGroup();
            $SQL .= $this->renderHaving();
            $SQL .= $this->renderOrder();
            $SQL .= $this->renderLimit();
        }

        return trim($this->parseAlias($SQL));
    }

    /**
     * @return string
     */
    protected function renderQuantifier()
    {
        if (null !== $this->quantifier) 
        {
            return $this->quantifier . ' ' . "\n";
        }

        return '';
    }

    /**
     * @return string
     */
    protected function renderColumns() 
    {
        if (count($this->columns) > 0) 
        {
            return implode(', ', $this->columns) . ' ' . "\n";
        }

        if (preg_match('/as\s+(.+)$/i', $this->table, $matches))
        {
            return $matches[1] . '.* ' . "\n";
        }

        return $this->table . '.* ' . "\n";
    }

    /**
     * @return string
     */
    protected function renderGroup() 
    {
        $SQL = '';

        if (count($this->group) > 0)
        {
            $SQL .= 'GROUP BY ';
            $SQL .= implode(', ', $this->group) . ' ' . "\n";
        }

        return $SQL;
    }
}
