<?php

namespace Elixir\DB\ResultSet;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
abstract class ResultSetAbstract implements \Iterator, \Countable  
{
    /**
     * @var mixed
     */
    protected $resource;

    /**
     * @param mixed $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return mixed
     */
    abstract public function first();
    
    /**
     * @return array
     */
    abstract public function all();
}
