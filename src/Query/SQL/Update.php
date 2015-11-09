<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\SQLAbstract;
use Elixir\DB\Query\SQL\WhereTrait;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Update extends SQLAbstract 
{
    use WhereTrait;

    /**
     * @var boolean 
     */
    protected $raw = false;

    /**
     * @var array 
     */
    protected $set = [];

    /**
     * @param boolean $value
     * @return Update
     */
    public function raw($value) 
    {
        $this->raw = $value;
        return $this;
    }
    
    /**
     * @param array $values
     * @param string $type
     * @return Update
     */
    public function set(array $values, $type = self::VALUES_SET) 
    {
        if ($type == self::VALUES_SET) 
        {
            $this->set = $values;
        } 
        else 
        {
            $this->set = array_merge($this->set, $values);
        }

        return $this;
    }

    /**
     * @param string $part
     * @return Update
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
            case 'raw':
                $this->raw = false;
                break;
            case 'where':
                $this->where = [];
                break;
            case 'set':
                $this->set = [];
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
            case 'raw':
                return $this->raw;
            case 'where':
                return $this->where;
            case 'set':
                return $this->set;
        }
        
        return null;
    }
    
    /**
     * @param mixed $data
     * @param string $part
     * @return Update
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
            case 'raw':
                $this->raw($data);
                break;
            case 'where':
                $this->where = array_merge($this->where, $data);
                break;
            case 'set':
                $this->set = array_merge($this->set, $data);
                break;
        }
        
        return $this;
    }

    /**
     * @see SQLInterface::render()
     */
    public function render()
    {
        $SQL = 'UPDATE ' . "\n";
        $SQL .= $this->table . ' ' . "\n";
        $SQL .= $this->renderSet();
        $SQL .= $this->renderWhere();

        return trim($this->parseAlias($SQL));
    }

    /**
     * @return string
     */
    protected function renderSet()
    {
        $SQL = 'SET ';
        $sets = [];

        foreach ($this->set as $key => $value)
        {
            if (!$this->raw) 
            {
                $value = $this->quote($value);
            }

            $sets[] = $key . ' = ' . $value;
        }

        $SQL .= implode(', ', $sets) . ' ' . "\n";
        return $SQL;
    }
}
