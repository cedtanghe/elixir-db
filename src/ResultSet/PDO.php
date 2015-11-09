<?php

namespace Elixir\DB\ResultSet;

use Elixir\DB\ResultSet\ResultSetAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class PDO extends ResultSetAbstract 
{
    /**
     * @var integer
     */
    protected $position = 0;

    /**
     * @ignore
     */
    public function rewind()
    {
        $this->position = 0;
        return $this->resource->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_FIRST);
    }

    /**
     * @ignore
     */
    public function current()
    {
        return $this->resource->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_REL);
    }

    /**
     * @ignore
     */
    public function key() 
    {
        return $this->position;
    }

    /**
     * @ignore
     */
    public function next()
    {
        return $this->fetch(); 
    }

    /**
     * @ignore
     */
    public function valid() 
    {
        return $this->position <= $this->count();
    }

    /**
     * @ignore
     */
    public function count()
    {
        return $this->resource->columnCount();
    }
    
    /**
     * @see ResultSetAbstract::first()
     */
    public function first()
    {
        return $this->next();
    }
    
    /**
     * @see ResultSetAbstract::all()
     */
    public function all()
    {
        return $this->resource->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * @ignore
     */
    public function fetch($fetchStyle = \PDO::FETCH_ASSOC)
    {
        $r = $this->resource->fetch($fetchStyle);
        
        if(false !== $r)
        {
            $this->position++;
        }
        
        return $r; 
    }
    
    /**
     * @ignore
     */
    public function __call($method, $arguments) 
    {
        return call_user_func_array([$this->resource, $method], $arguments);
    }
}
