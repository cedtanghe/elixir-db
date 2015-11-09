<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\SQLAbstract;
use Elixir\DB\Query\SQL\WhereTrait;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Delete extends SQLAbstract 
{
    use WhereTrait;

    /**
     * @param string $part
     * @return Delete
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
            case 'where':
                $this->where = [];
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
            case 'where':
                return $this->where;
        }
        
        return null;
    }
    
    /**
     * @param mixed $data
     * @param string $part
     * @return Delete
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
            case 'where':
                $this->where = array_merge($this->where, $data);
                break;
        }
        
        return $this;
    }

    /**
     * @see SQLInterface::render()
     */
    public function render()
    {
        $SQL = 'DELETE FROM ' . "\n";
        $SQL .= $this->table . ' ' . "\n";
        $SQL .= $this->renderWhere();

        return trim($this->parseAlias($SQL));
    }
}
