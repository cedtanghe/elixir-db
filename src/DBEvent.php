<?php

namespace Elixir\DB;

use Elixir\DB\Query\QueryInterface;
use Elixir\Dispatcher\Event;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class DBEvent extends Event 
{
    /**
     * @var string
     */
    const PRE_QUERY = 'pre_query';

    /**
     * @var string
     */
    const QUERY = 'query';

    /**
     * @var QueryInterface|string 
     */
    protected $query;

    /**
     * @var array 
     */
    protected $bindings;

    /**
     * @var integer 
     */
    protected $elapsedTime;

    /**
     * @see Event::__contruct()
     * @param array $params
     */
    public function __construct($type, array $params = []) 
    {
        parent::__construct($type);

        $params += [
            'query' => null,
            'bindings' => [],
            'elapsed_time' => 0
        ];
        
        $this->query = $params['query'];
        $this->bindings = $params['bindings'];
        $this->elapsedTime = $params['elapsed_time'];
    }
    
    /**
     * @return QueryInterface|string
     */
    public function getQuery() 
    {
        return $this->query;
    }
    
    /**
     * @param QueryInterface|string $value
     */
    public function setQuery($value) 
    {
        $this->query = $value;
    }

    /**
     * @return array
     */
    public function getBindings() 
    {
        return $this->bindings;
    }
    
    /**
     * @param array $values
     */
    public function setBindings(array $values) 
    {
        $this->bindings = $values;
    }
    
    /**
     * @return integer
     */
    public function getElapsedTime() 
    {
        return $this->elapsedTime;
    }
}
